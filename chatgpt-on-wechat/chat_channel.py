import os
import re
import threading
import time
import requests
from asyncio import CancelledError
from concurrent.futures import Future, ThreadPoolExecutor

from bridge.context import *
from bridge.reply import *
from channel.channel import Channel
from common.dequeue import Dequeue
from common import memory
from plugins import *

try:
    from voice.audio_convert import any_to_wav
except Exception as e:
    pass

handler_pool = ThreadPoolExecutor(max_workers=8)  # 处理消息的线程池


# 抽象类, 它包含了与消息通道无关的通用处理逻辑
class ChatChannel(Channel):
    name = None  # 登录的用户名
    user_id = None  # 登录的用户id
    futures = {}  # 记录每个session_id提交到线程池的future对象, 用于重置会话时把没执行的future取消掉，正在执行的不会被取消
    sessions = {}  # 用于控制并发，每个session_id同时只能有一个context在处理
    lock = threading.Lock()  # 用于控制对sessions的访问

    def __init__(self):
        _thread = threading.Thread(target=self.consume)
        _thread.setDaemon(True)
        _thread.start()

    # 根据消息构造context，消息内容相关的触发项写在这里
    def _compose_context(self, ctype: ContextType, content, **kwargs):
        context = Context(ctype, content)
        context.kwargs = kwargs
        # context首次传入时，origin_ctype是None,
        # 引入的起因是：当输入语音时，会嵌套生成两个context，第一步语音转文本，第二步通过文本生成文字回复。
        # origin_ctype用于第二步文本回复时，判断是否需要匹配前缀，如果是私聊的语音，就不需要匹配前缀
        if "origin_ctype" not in context:
            context["origin_ctype"] = ctype
        # context首次传入时，receiver是None，根据类型设置receiver
        first_in = "receiver" not in context
        # 群名匹配过程，设置session_id和receiver
        if first_in:  # context首次传入时，receiver是None，根据类型设置receiver
            config = conf()
            cmsg = context["msg"]
            user_data = conf().get_user_data(cmsg.from_user_id)
            context["openai_api_key"] = user_data.get("openai_api_key")
            context["gpt_model"] = user_data.get("gpt_model")
            if context.get("isgroup", False):
                group_name = cmsg.other_user_nickname
                group_id = cmsg.other_user_id

                group_name_white_list = config.get("group_name_white_list", [])
                group_name_keyword_white_list = config.get("group_name_keyword_white_list", [])
                if any(
                    [
                        group_name in group_name_white_list,
                        "ALL_GROUP" in group_name_white_list,
                        check_contain(group_name, group_name_keyword_white_list),
                    ]
                ):
                    group_chat_in_one_session = conf().get("group_chat_in_one_session", [])
                    session_id = cmsg.actual_user_id
                    if any(
                        [
                            group_name in group_chat_in_one_session,
                            "ALL_GROUP" in group_chat_in_one_session,
                        ]
                    ):
                        session_id = group_id
                else:
                    logger.debug(f"No need reply, groupName not in whitelist, group_name={group_name}")
                    return None
                context["session_id"] = session_id
                context["receiver"] = group_id
            else:
                context["session_id"] = cmsg.other_user_id
                context["receiver"] = cmsg.other_user_id
            e_context = PluginManager().emit_event(EventContext(Event.ON_RECEIVE_MESSAGE, {"channel": self, "context": context}))
            context = e_context["context"]
            if e_context.is_pass() or context is None:
                return context
            if cmsg.from_user_id == self.user_id and not config.get("trigger_by_self", True):
                logger.debug("[chat_channel]self message skipped")
                return None

        # 消息内容匹配过程，并处理content
        if ctype == ContextType.TEXT:
            if first_in and "」\n- - - - - - -" in content:  # 初次匹配 过滤引用消息
                logger.debug(content)
                logger.debug("[chat_channel]reference query skipped")
                return None
            
            pattern = f"@{re.escape(self.name)}(\u2005|\u0020)"
            content_search = re.sub(pattern, r"", content)
            if isinstance(context["msg"].at_list, list):
                for at in context["msg"].at_list:
                    pattern = f"@{re.escape(at)}(\u2005|\u0020)"
                    content_search = re.sub(pattern, r"", content_search)
            if content_search == content and context["msg"].self_display_name:
                # 前缀移除后没有变化，使用群昵称再次移除
                pattern = f"@{re.escape(context['msg'].self_display_name)}(\u2005|\u0020)"
                content_search = re.sub(pattern, r"", content)
                
            
            # 去除字符串开头和结尾的所有空格字符
            content_search = content_search.strip()
            # logger.info("[来消息了] content={}, content_search={}".format(content, content_search))
            content_search = process_string(content_search)


            nick_name_black_list = conf().get("nick_name_black_list", [])
            if context.get("isgroup", False):  # 群聊
            
                if any(content_search.startswith(prefix) for prefix in ["搜剧", "搜", "全网搜"]) and not content_search.startswith("搜索"):
                    content_search = process_string2(content_search)
                    user_nickname = context['msg'].actual_user_nickname
                    reply_text = f"@{user_nickname}"
                    

                    contentSearch = remove_prefix(content_search, ["搜剧", "搜", "全网搜"]).strip()
                
                    def perform_search():
                        # 初次搜索
                        response_data = search_question(contentSearch) if not content_search.startswith("全网搜") else []
                        if not response_data:
                            # 通知用户深入搜索
                            reply_text2 = f"@{user_nickname}\n正在深入搜索，请稍等..."
                            self._send_reply(context, Reply(ReplyType.TEXT, reply_text2))
                
                            # 启动线程进行第二次搜索
                            def perform_second_search():
                                response_data = search_alone(contentSearch)
                                send_final_reply(response_data, reply_text, context)
                            
                            second_search_thread = threading.Thread(target=perform_second_search)
                            second_search_thread.start()
                        else:
                            # 如果第一次搜索找到结果，发送最终回复
                            send_final_reply(response_data, reply_text, context)
                
                    def send_final_reply(response_data, reply_text, context):
                        is_times = 0
                        if not response_data:
                            reply_text_final = f"{reply_text}\n未找到，可换个关键词尝试哦~"
                            reply_text_final += "\n⚠️宁少写，不多写、错写~"
                            # reply_text_final += "\n--------------------"
                            # reply_text_final += "\n可访问以下链接提交资源需求"
                            # reply_text_final += "\nhttps://pan.xinyuedh.com"
                            # reply_text_final += "\n--------------------"
                            # reply_text_final += "\nGPT小助手分享"
                            # reply_text_final += "\n--------------------"
                            # reply_text_final += "\nhttps://chat.xinyuedh.com"
                        else:
                            reply_text_final = f"{reply_text}\n--------------------"
                            for item in response_data:
                                if item.get('is_time') == 1:
                                    reply_text_final += f"\n 🌐️ {item.get('title', '未知标题')}"
                                    is_times += 1
                                else:
                                    reply_text_final += f"\n{item.get('title', '未知标题')}"
                                reply_text_final += f"\n{item.get('url', '未知URL')}"
                                reply_text_final += "\n--------------------"
                            
                            if is_times > 0:
                                reply_text_final += "\n 🌐️资源来源网络，30分钟后删除"
                                reply_text_final += "\n--------------------"
                            else:
                                reply_text_final += "\n 不是短剧？请尝试：全网搜XX"
                                reply_text_final += "\n--------------------"
                            
                            reply_text_final += "\n欢迎观看！如果喜欢可以喊你的朋友一起来哦"
                
                        reply = Reply(ReplyType.TEXT, reply_text_final)
                        self._send_reply(context, reply)

                
                    # 启动线程执行第一次搜索
                    first_search_thread = threading.Thread(target=perform_search)
                    first_search_thread.start()
                    return None
                
                # 校验关键字
                match_prefix = check_prefix(content, conf().get("group_chat_prefix"))
                match_contain = check_contain(content, conf().get("group_chat_keyword"))
                flag = False
                if context["msg"].to_user_id != context["msg"].actual_user_id:
                    if match_prefix is not None or match_contain is not None:
                        flag = True
                        if match_prefix:
                            content = content.replace(match_prefix, "", 1).strip()
                    if context["msg"].is_at:
                        nick_name = context["msg"].actual_user_nickname
                        if nick_name and nick_name in nick_name_black_list:
                            # 黑名单过滤
                            logger.warning(f"[chat_channel] Nickname {nick_name} in In BlackList, ignore")
                            return None

                        logger.info("[chat_channel]receive group at")
                        if not conf().get("group_at_off", False):
                            flag = True
                        pattern = f"@{re.escape(self.name)}(\u2005|\u0020)"
                        subtract_res = re.sub(pattern, r"", content)
                        if subtract_res.startswith("画"):
                            subtract_res = "生成图片要求如下：\n" + subtract_res[1:]
                        
                        if isinstance(context["msg"].at_list, list):
                            for at in context["msg"].at_list:
                                pattern = f"@{re.escape(at)}(\u2005|\u0020)"
                                subtract_res = re.sub(pattern, r"", subtract_res)
                        if subtract_res == content and context["msg"].self_display_name:
                            # 前缀移除后没有变化，使用群昵称再次移除
                            pattern = f"@{re.escape(context['msg'].self_display_name)}(\u2005|\u0020)"
                            subtract_res = re.sub(pattern, r"", content)
                        content = subtract_res
                if not flag:
                    if context["origin_ctype"] == ContextType.VOICE:
                        logger.info("[chat_channel]receive group voice, but checkprefix didn't match")
                    return None
            else:  # 单聊
                nick_name = context["msg"].from_user_nickname
                if nick_name and nick_name in nick_name_black_list:
                    # 黑名单过滤
                    logger.warning(f"[chat_channel] Nickname '{nick_name}' in In BlackList, ignore")
                    return None

                match_prefix = check_prefix(content, conf().get("single_chat_prefix", [""]))
                if match_prefix is not None:  # 判断如果匹配到自定义前缀，则返回过滤掉前缀+空格后的内容
                    content = content.replace(match_prefix, "", 1).strip()
                elif context["origin_ctype"] == ContextType.VOICE:  # 如果源消息是私聊的语音消息，允许不匹配前缀，放宽条件
                    pass
                else:
                    return None
            content = content.strip()
            img_match_prefix = check_prefix(content, conf().get("image_create_prefix",[""]))
            if img_match_prefix:
                content = content.replace(img_match_prefix, "", 1)
                context.type = ContextType.IMAGE_CREATE
            else:
                context.type = ContextType.TEXT
            context.content = content.strip()
            if "desire_rtype" not in context and conf().get("always_reply_voice") and ReplyType.VOICE not in self.NOT_SUPPORT_REPLYTYPE:
                context["desire_rtype"] = ReplyType.VOICE
        elif context.type == ContextType.VOICE:
            if "desire_rtype" not in context and conf().get("voice_reply_voice") and ReplyType.VOICE not in self.NOT_SUPPORT_REPLYTYPE:
                context["desire_rtype"] = ReplyType.VOICE
        return context

    def _handle(self, context: Context):
        if context is None or not context.content:
            return
        logger.debug("[chat_channel] ready to handle context: {}".format(context))
        # reply的构建步骤
        reply = self._generate_reply(context)

        logger.debug("[chat_channel] ready to decorate reply: {}".format(reply))

        # reply的包装步骤
        if reply and reply.content:
            reply = self._decorate_reply(context, reply)

            # reply的发送步骤
            self._send_reply(context, reply)

    def _generate_reply(self, context: Context, reply: Reply = Reply()) -> Reply:
        e_context = PluginManager().emit_event(
            EventContext(
                Event.ON_HANDLE_CONTEXT,
                {"channel": self, "context": context, "reply": reply},
            )
        )
        reply = e_context["reply"]
        if not e_context.is_pass():
            logger.debug("[chat_channel] ready to handle context: type={}, content={}".format(context.type, context.content))
            if context.type == ContextType.TEXT or context.type == ContextType.IMAGE_CREATE:  # 文字和图片消息
                context["channel"] = e_context["channel"]
                reply = super().build_reply_content(context.content, context)
            elif context.type == ContextType.VOICE:  # 语音消息
                cmsg = context["msg"]
                cmsg.prepare()
                file_path = context.content
                wav_path = os.path.splitext(file_path)[0] + ".wav"
                try:
                    any_to_wav(file_path, wav_path)
                except Exception as e:  # 转换失败，直接使用mp3，对于某些api，mp3也可以识别
                    logger.warning("[chat_channel]any to wav error, use raw path. " + str(e))
                    wav_path = file_path
                # 语音识别
                reply = super().build_voice_to_text(wav_path)
                # 删除临时文件
                try:
                    os.remove(file_path)
                    if wav_path != file_path:
                        os.remove(wav_path)
                except Exception as e:
                    pass
                    # logger.warning("[chat_channel]delete temp file error: " + str(e))

                if reply.type == ReplyType.TEXT:
                    new_context = self._compose_context(ContextType.TEXT, reply.content, **context.kwargs)
                    if new_context:
                        reply = self._generate_reply(new_context)
                    else:
                        return
            elif context.type == ContextType.IMAGE:  # 图片消息，当前仅做下载保存到本地的逻辑
                memory.USER_IMAGE_CACHE[context["session_id"]] = {
                    "path": context.content,
                    "msg": context.get("msg")
                }
            elif context.type == ContextType.SHARING:  # 分享信息，当前无默认逻辑
                pass
            elif context.type == ContextType.FUNCTION or context.type == ContextType.FILE:  # 文件消息及函数调用等，当前无默认逻辑
                pass
            else:
                logger.warning("[chat_channel] unknown context type: {}".format(context.type))
                return
        return reply

    def _decorate_reply(self, context: Context, reply: Reply) -> Reply:
        if reply and reply.type:
            e_context = PluginManager().emit_event(
                EventContext(
                    Event.ON_DECORATE_REPLY,
                    {"channel": self, "context": context, "reply": reply},
                )
            )
            reply = e_context["reply"]
            desire_rtype = context.get("desire_rtype")
            if not e_context.is_pass() and reply and reply.type:
                if reply.type in self.NOT_SUPPORT_REPLYTYPE:
                    logger.error("[chat_channel]reply type not support: " + str(reply.type))
                    reply.type = ReplyType.ERROR
                    reply.content = "不支持发送的消息类型: " + str(reply.type)

                if reply.type == ReplyType.TEXT:
                    reply_text = reply.content
                    if desire_rtype == ReplyType.VOICE and ReplyType.VOICE not in self.NOT_SUPPORT_REPLYTYPE:
                        reply = super().build_text_to_voice(reply.content)
                        return self._decorate_reply(context, reply)
                    if context.get("isgroup", False):
                        if not context.get("no_need_at", False):
                            reply_text = "@" + context["msg"].actual_user_nickname + "\n" + reply_text.strip()
                        reply_text = conf().get("group_chat_reply_prefix", "") + reply_text + conf().get("group_chat_reply_suffix", "")
                    else:
                        reply_text = conf().get("single_chat_reply_prefix", "") + reply_text + conf().get("single_chat_reply_suffix", "")
                    reply.content = reply_text
                elif reply.type == ReplyType.ERROR or reply.type == ReplyType.INFO:
                    reply.content = "[" + str(reply.type) + "]\n" + reply.content
                elif reply.type == ReplyType.IMAGE_URL or reply.type == ReplyType.VOICE or reply.type == ReplyType.IMAGE or reply.type == ReplyType.FILE or reply.type == ReplyType.VIDEO or reply.type == ReplyType.VIDEO_URL:
                    pass
                else:
                    logger.error("[chat_channel] unknown reply type: {}".format(reply.type))
                    return
            if desire_rtype and desire_rtype != reply.type and reply.type not in [ReplyType.ERROR, ReplyType.INFO]:
                logger.warning("[chat_channel] desire_rtype: {}, but reply type: {}".format(context.get("desire_rtype"), reply.type))
            return reply

    def _send_reply(self, context: Context, reply: Reply):
        if reply and reply.type:
            e_context = PluginManager().emit_event(
                EventContext(
                    Event.ON_SEND_REPLY,
                    {"channel": self, "context": context, "reply": reply},
                )
            )
            reply = e_context["reply"]
            if not e_context.is_pass() and reply and reply.type:
                logger.debug("[chat_channel] ready to send reply: {}, context: {}".format(reply, context))
                self._send(reply, context)

    def _send(self, reply: Reply, context: Context, retry_cnt=0):
        try:
            self.send(reply, context)
        except Exception as e:
            logger.error("[chat_channel] sendMsg error: {}".format(str(e)))
            if isinstance(e, NotImplementedError):
                return
            logger.exception(e)
            if retry_cnt < 2:
                time.sleep(3 + 3 * retry_cnt)
                self._send(reply, context, retry_cnt + 1)

    def _success_callback(self, session_id, **kwargs):  # 线程正常结束时的回调函数
        logger.debug("Worker return success, session_id = {}".format(session_id))

    def _fail_callback(self, session_id, exception, **kwargs):  # 线程异常结束时的回调函数
        logger.exception("Worker return exception: {}".format(exception))

    def _thread_pool_callback(self, session_id, **kwargs):
        def func(worker: Future):
            try:
                worker_exception = worker.exception()
                if worker_exception:
                    self._fail_callback(session_id, exception=worker_exception, **kwargs)
                else:
                    self._success_callback(session_id, **kwargs)
            except CancelledError as e:
                logger.info("Worker cancelled, session_id = {}".format(session_id))
            except Exception as e:
                logger.exception("Worker raise exception: {}".format(e))
            with self.lock:
                self.sessions[session_id][1].release()

        return func

    def produce(self, context: Context):
        session_id = context["session_id"]
        with self.lock:
            if session_id not in self.sessions:
                self.sessions[session_id] = [
                    Dequeue(),
                    threading.BoundedSemaphore(conf().get("concurrency_in_session", 4)),
                ]
            if context.type == ContextType.TEXT and context.content.startswith("#"):
                self.sessions[session_id][0].putleft(context)  # 优先处理管理命令
            else:
                self.sessions[session_id][0].put(context)

    # 消费者函数，单独线程，用于从消息队列中取出消息并处理
    def consume(self):
        while True:
            with self.lock:
                session_ids = list(self.sessions.keys())
                for session_id in session_ids:
                    context_queue, semaphore = self.sessions[session_id]
                    if semaphore.acquire(blocking=False):  # 等线程处理完毕才能删除
                        if not context_queue.empty():
                            context = context_queue.get()
                            logger.debug("[chat_channel] consume context: {}".format(context))
                            future: Future = handler_pool.submit(self._handle, context)
                            future.add_done_callback(self._thread_pool_callback(session_id, context=context))
                            if session_id not in self.futures:
                                self.futures[session_id] = []
                            self.futures[session_id].append(future)
                        elif semaphore._initial_value == semaphore._value + 1:  # 除了当前，没有任务再申请到信号量，说明所有任务都处理完毕
                            self.futures[session_id] = [t for t in self.futures[session_id] if not t.done()]
                            assert len(self.futures[session_id]) == 0, "thread pool error"
                            del self.sessions[session_id]
                        else:
                            semaphore.release()
            time.sleep(0.1)

    # 取消session_id对应的所有任务，只能取消排队的消息和已提交线程池但未执行的任务
    def cancel_session(self, session_id):
        with self.lock:
            if session_id in self.sessions:
                for future in self.futures[session_id]:
                    future.cancel()
                cnt = self.sessions[session_id][0].qsize()
                if cnt > 0:
                    logger.info("Cancel {} messages in session {}".format(cnt, session_id))
                self.sessions[session_id][0] = Dequeue()

    def cancel_all_session(self):
        with self.lock:
            for session_id in self.sessions:
                for future in self.futures[session_id]:
                    future.cancel()
                cnt = self.sessions[session_id][0].qsize()
                if cnt > 0:
                    logger.info("Cancel {} messages in session {}".format(cnt, session_id))
                self.sessions[session_id][0] = Dequeue()


def check_prefix(content, prefix_list):
    if not prefix_list:
        return None
    for prefix in prefix_list:
        if content.startswith(prefix):
            return prefix
    return None


def check_contain(content, keyword_list):
    if not keyword_list:
        return None
    for ky in keyword_list:
        if content.find(ky) != -1:
            return True
    return None


def remove_prefix(content, prefixes):
    for prefix in prefixes:
        if content.startswith(prefix):
            return content[len(prefix):].strip()
    return content.strip()
    

    
def process_string(s):
    # 判断是否以@开头并且包含"搜"字
    if s.startswith('@') and '搜' in s:
        # 找到"搜"字的位置
        index = s.index('搜')
        # 去除"搜"字前面的内容
        return s[index:]
    else:
        return s
        
def process_string2(s):
    # 判断是否包含@
    if '@' in s:
        # 找到@字符的位置
        index = s.index('@')
        # 删除包含@在内后面的所有字符
        return s[:index]
    else:
        return s



def search_question(question):
    url = conf().get("duanju_url", "") + '/api/search'
    params = {
        'is_time': '1',
        'page_no': '1',
        'page_size': '5',
        'title': question
    }
    try:
        response = requests.get(url, params=params)
        response.raise_for_status()  # 检查请求是否成功
        responseData = response.json().get('data', {}).get('items', [])
        return responseData
    except requests.exceptions.RequestException as e:
        print(f"Error fetching data: {e}")
        return []

def search_alone(question):
    url = conf().get("duanju_url", "") + '/api/other/all_search'
    payload = {
        'title': question
    }
    try:
        response = requests.post(url, json=payload)
        response.raise_for_status()
        responseData = response.json().get('data', [])
        return responseData
    except requests.exceptions.RequestException as e:
        print(f"Error fetching data: {e}")
        return []    