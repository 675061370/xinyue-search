<?php

namespace app\api\controller;

use app\api\QfShop;
use think\facade\Request;
use think\facade\Cache;
use app\model\Source as SourceModel;

class Chatbot extends QfShop
{
    // 应用管理 开放API 生成三个参数填在下方 回调地址：https://XXXX/api/chatbot 填在微信官方 即可
    private $appId = '';
    private $token = '';
    private $encodingAESKey = '';

    
    // 回复内容在下方，如有需要自行修改
    private $wName = '心悦搜索';
    
    private $key;

    public function __construct()
    {
        $this->key = base64_decode($this->encodingAESKey . "=");
    }

    public function index()
    {
        $encrypted = input('encrypted');
        
        //解决全局搜时会多次接收相同消息导致多次请求的bug，原因未知
        if(!empty($encrypted)){
            if(Cache::get($encrypted) == 1){
                return;
            }
            Cache::set($encrypted, 1, 600);
        }
        
        $msg = $this->decryptMessage($encrypted);
        
        
        $msgtype = $msg['content']['msgtype']??'';
        $message = $msg['content']['msg']??'';
        if ($msgtype !== 'text' || empty($message)) {
            if(Cache::get($msg['userid']) != 1){
                $messages = "欢迎来到 ".$this->wName."！🍿🎬 这里是影迷的梦幻天堂，准备好享受每一部影视的精彩时刻吧！\n\n";
                $messages .= "🔍 使用指南 🔍\n\n";
                $messages .= "1. 搜剧命令\n";
                $messages .= "回复 “搜剧+剧名”，免费获取最全的影视资源。\n";
                $messages .= "示例：<a href='weixin://bizmsgmenu?msgmenucontent=搜剧我被美女包围了&msgmenuid=搜剧我被美女包围了'>搜剧我被美女包围了</a>\n\n";
                $messages .= "2. 全网搜\n";
                $messages .= "回复 “全网搜+关键词”，快速找到全网资源！\n";
                $messages .= "示例：<a href='weixin://bizmsgmenu?msgmenucontent=全网搜学剪辑&msgmenuid=全网搜学剪辑'>全网搜学剪辑</a>\n\n";
                $messages .= "赶快准备好你的爆米花，和我们一起开启下一场视觉盛宴吧！🎥";
                
                $this->sendMessage($msg, $messages);
            }
            Cache::set($msg['userid'], 1, 604800);
        }
        // 检查用户消息内容中开头第一个字是否包含“搜”关键字
        if (strncmp($message, '搜', 1) === 0 || strncmp($message, '全网搜', 1) === 0) {
            
            if(strncmp($message, '全网搜', 1) === 0){
                $list = [];
                // 去除“全网搜”关键字，提取用户输入的剧名
                $newString = preg_replace('/^全网搜/u', '', $message);
            }else{
                // 去除“搜剧”关键字，提取用户输入的剧名
                $newString = preg_replace('/^搜剧|^搜/u', '', $message);
                
                // 实例化资源模型，并搜索匹配的剧名
                $map['page_size'] = 5;
                $map['title'] = $newString;
                $SourceModel = new SourceModel();
                $result = $SourceModel->getList($map);
                $list = $result['items']??[];
            }
            
            
            
            if(empty($list)){
                //系统没有资源去全网搜 
                $this->sendMessage($msg, "正在深入搜索，请稍等...");
                
                $list = $this->Qsearch($newString);
            }
            
            $content = "🔍 ".$newString."丨搜索结果";
            $is_times = 0;
            if (!empty($list)) {
                foreach ($list as $item) {
                    $content = $content . "\n";
                    if(!empty($item['is_time']) && $item['is_time'] == 1){
                        $content = $content . "\n🌐️ " . $item['title'] . "\n<a href='" . $item['url'] . "'>" . $item['url'] . "</a>";
                        $is_times++;
                    }else{
                       $content = $content . "\n" . $item['title'] . "\n<a href='" . $item['url'] . "'>" . $item['url'] . "</a>"; 
                    }
                }
                $content = $content . "\n";
                if($is_times>0){
                    $content = $content . "\n🌐️ 资源来源网络，30分钟后删除";
                }else if(!empty($newString)){ 
                    $content = $content . "\n 不是短剧？请尝试：<a href='weixin://bizmsgmenu?msgmenucontent=全网搜".$newString."&msgmenuid=全网搜".$newString."'>全网搜".$newString."</a>";
                }
                $content = $content . "\n ------------------------------------------------";
                $content = $content . "\n 欢迎观看！如果喜欢可以喊你的朋友一起来哦";
            } else {
                // 如果没有找到匹配的剧名，提示用户减少关键词尝试搜索
                $content = $content . "\n";
                $content = $content . "\n 未找到，可换个关键词尝试哦~";
                $content = $content . "\n ⚠️宁少写，不多写、错写~";
            }
            
            $this->sendMessage($msg, $content);
        }
        
        
        
    }
    
    private function Qsearch($newString)
    {
        $list = [];
        $urlData = array(
            'title' => $newString,
        );
        $res = curlHelper(Request::domain()."/api/other/all_search", "POST", $urlData)['body'];
        $res = json_decode($res, true);
        
        if($res['code'] === 200){
            $list = $res['data']??[];
        }
        
        return $list;
    }

    // 解密消息
    private function decryptMessage($encrypted)
    {
        $result = $this->decrypt($encrypted,$this->appId);
        if ($result[0] !== 0) {
            return [];
        }
        return json_decode(json_encode(simplexml_load_string($result[1])), true);
    }

    // 发送消息
    private function sendMessage($msg, $message)
    {
        $data = $this->createXmlMessage($msg, $message);
        $result = $this->encrypt($data,$this->appId);
        
        if ($result[0] !== 0) {
            return;
        }
        
        $this->sendPostRequest("https://chatbot.weixin.qq.com/openapi/sendmsg/".$this->token, [
            'encrypt' => $result[1],
        ]);
    }

    // 创建 XML 消息
    private function createXmlMessage($msg, $message)
    {
        return '<xml>
            <appid><![CDATA['.$msg['appid'].']]></appid>
            <openid><![CDATA['.$msg['userid'].']]></openid>
            <msg><![CDATA['.$message.']]></msg>
            <channel>'.$msg['channel'].'</channel>
        </xml>';
    }

    private function sendPostRequest($url, $data)
    {
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            // 处理错误（可添加日志）
            return ['error' => curl_error($ch)];
        }

        curl_close($ch);
        return json_decode($response, true);
    }
    
    
    
    
    
    /**
	 * 对密文进行解密
	 * @param string $encrypted 需要解密的密文
	 * @return string 解密得到的明文
	 */
    private function decrypt($encrypted,$appid)
    {
        try {
			if(version_compare(PHP_VERSION, '7','>=')) {
				$iv = substr($this->key, 0, 16);          
        		$decrypted = openssl_decrypt($encrypted,'AES-256-CBC',substr($this->key, 0, 32),OPENSSL_ZERO_PADDING,$iv);
			} else {
				//使用BASE64对需要解密的字符串进行解码
				$ciphertext_dec = base64_decode($encrypted);
				$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
				$iv = substr($this->key, 0, 16);
				mcrypt_generic_init($module, $this->key, $iv);
 
				//解密
				$decrypted = mdecrypt_generic($module, $ciphertext_dec);
				mcrypt_generic_deinit($module);
				mcrypt_module_close($module);
			}
 
		} catch (Exception $e) {
			return [-40007, null];
		}
 
 
		try {
			//去除补位字符
			$result = $this->decode($decrypted);
			//去除16位随机字符串,网络字节序和AppId
			if (strlen($result) < 16)
				return "";
			$content = substr($result, 16, strlen($result));
			$len_list = unpack("N", substr($content, 0, 4));
			$xml_len = $len_list[1];
			$xml_content = substr($content, 4, $xml_len);
			$from_appid = substr($content, $xml_len + 4);
			if(version_compare(PHP_VERSION, '7','>=')) {
		        if (!$appid) {
		        	//如果传入的appid是空的，则认为是订阅号，使用数据中提取出来的appid
		            $appid = $from_appid; 
		        }
		    }
		} catch (Exception $e) {
			//print $e;
			return [-40008, null];
		}
		if ($from_appid != $appid)
			return [-40005, null];
		//不注释上边两行，避免传入appid是错误的情况
		if(version_compare(PHP_VERSION, '7','>=')) {
			//增加appid，为了解决后面加密回复消息的时候没有appid的订阅号会无法回复
			return array(0, $xml_content, $from_appid);
		} else {
			return array(0, $xml_content);
		}
        return $encrypted;
    }
    
    /**
	 * 对解密后的明文进行补位删除
	 * @param decrypted 解密后的明文
	 * @return 删除填充补位后的明文
	 */
	private function decode($text)
	{

		$pad = ord(substr($text, -1));
		if ($pad < 1 || $pad > 32) {
			$pad = 0;
		}
		return substr($text, 0, (strlen($text) - $pad));
	}
	
	
	/**
	 * 对明文进行加密
	 * @param string $text 需要加密的明文
	 * @return string 加密后的密文
	 */
	private function encrypt($text, $appid)
	{
		try {
			if(version_compare(PHP_VERSION, '7','>=')) {
		 		//获得16位随机字符串，填充到明文之前
		        $random = $this->getRandomStr();
		        $text = $random . pack("N", strlen($text)) . $text . $appid;
		        $iv = substr($this->key, 0, 16);
		        $text = $this->encode($text);
		        $encrypted = openssl_encrypt($text,'AES-256-CBC',substr($this->key, 0, 32),OPENSSL_ZERO_PADDING,$iv);
			} else {
				//获得16位随机字符串，填充到明文之前
				$random = $this->getRandomStr();
				$text = $random . pack("N", strlen($text)) . $text . $appid;
				// 网络字节序
				$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
				$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
				$iv = substr($this->key, 0, 16);
				//使用自定义的填充方式对明文进行补位填充
				$text = $this->encode($text);
				mcrypt_generic_init($module, $this->key, $iv);
				//加密
				$encrypted = mcrypt_generic($module, $text);
				mcrypt_generic_deinit($module);
				mcrypt_module_close($module);
				$encrypted = base64_encode($encrypted);
			}
 
			//print($encrypted);
			//使用BASE64对加密后的字符串进行编码
			return [0, $encrypted];
		} catch (Exception $e) {
			//print $e;
			return [-40006, null];
		}
	}
	/**
	 * 对需要加密的明文进行填充补位
	 * @param $text 需要进行填充补位操作的明文
	 * @return 补齐明文字符串
	 */
	private function encode($text)
	{
		$block_size = 32;
		$text_length = strlen($text);
		//计算需要填充的位数
		$amount_to_pad = 32 - ($text_length % 32);
		if ($amount_to_pad == 0) {
			$amount_to_pad = 32;
		}
		//获得补位所用的字符
		$pad_chr = chr($amount_to_pad);
		$tmp = "";
		for ($index = 0; $index < $amount_to_pad; $index++) {
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}
	
	/**
	 * 随机生成16位字符串
	 * @return string 生成的字符串
	 */
	private function getRandomStr()
	{

		$str = "";
		$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($str_pol) - 1;
		for ($i = 0; $i < 16; $i++) {
			$str .= $str_pol[mt_rand(0, $max)];
		}
		return $str;
	}

    
    private function writeLog($value)
    {
        // 获取当前目录
        $currentDir = __DIR__; // 当前控制器目录
        $logFile = $currentDir . '/log.txt'; // 日志文件名称，可以自定义

        // 生成时间戳
        $timestamp = date('Y-m-d H:i:s');

        // 判断 $value 是否为数组，如果是，则转换为 JSON 字符串
        if (is_array($value)) {
            $value = json_encode($value); // 转换为 JSON 字符串
        }

        $logEntry = "[$timestamp] 参数值: $value\n"; // 日志内容

        // 写入日志文件
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}
