<?php /*a:5:{s:71:"C:\Users\liu67\Desktop\test\xinyue-search\app\index\view\news\list.html";i:1726109170;s:75:"C:\Users\liu67\Desktop\test\xinyue-search\app\index\view\common\header.html";i:1726022411;s:73:"C:\Users\liu67\Desktop\test\xinyue-search\app\index\view\common\head.html";i:1726108056;s:73:"C:\Users\liu67\Desktop\test\xinyue-search\app\index\view\common\foot.html";i:1726106690;s:75:"C:\Users\liu67\Desktop\test\xinyue-search\app\index\view\common\footer.html";i:1726122055;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php if(!(empty($config['app_icon']) || (($config['app_icon'] instanceof \think\Collection || $config['app_icon'] instanceof \think\Paginator ) && $config['app_icon']->isEmpty()))): ?>
    <link rel="icon" href="<?php echo htmlentities($config['app_icon']); ?>" />
    <?php endif; ?>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1.0">
    <title><?php if(isset($detail) && $detail['title']): ?><?php echo htmlentities($detail['title']); ?> - <?php echo htmlentities($config['app_name']); else: ?><?php echo htmlentities($config['app_name']); if(!(empty($config['app_title']) || (($config['app_title'] instanceof \think\Collection || $config['app_title'] instanceof \think\Paginator ) && $config['app_title']->isEmpty()))): ?> - <?php echo htmlentities($config['app_title']); ?><?php endif; ?><?php endif; ?></title>
    <meta name="keywords" content="<?php if(!(empty($detail) || (($detail instanceof \think\Collection || $detail instanceof \think\Paginator ) && $detail->isEmpty()))): ?><?php echo htmlentities($detail['title']); ?>,<?php endif; ?><?php echo htmlentities($config['app_keywords']); ?>" />
    <meta name="description" content="<?php if(!(empty($detail) || (($detail instanceof \think\Collection || $detail instanceof \think\Paginator ) && $detail->isEmpty()))): ?><?php echo htmlentities($detail['title']); ?> - <?php endif; ?><?php echo htmlentities($config['app_description']); ?>" />
    <link rel="stylesheet" href="/static/index/css/index.min.css">
    <link rel="stylesheet" href="/static/index/css/app.css">
    <link rel="stylesheet" href="/static/index/css/m.css">
    
    <?php echo $config['seo_statistics']; ?>
    
    <style>
        :root {
            --theme-color: <?php echo htmlentities((isset($config['home_color']) && ($config['home_color'] !== '')?$config['home_color']:'#3e3e3e')); ?>;
            --theme-theme: <?php echo htmlentities((isset($config['home_theme']) && ($config['home_theme'] !== '')?$config['home_theme']:'#133ab3')); ?>;
            --theme-background: <?php echo htmlentities((isset($config['home_background']) && ($config['home_background'] !== '')?$config['home_background']:'#fafafa')); ?>;
            --theme-other_background: <?php echo htmlentities((isset($config['other_background']) && ($config['other_background'] !== '')?$config['other_background']:'#ffffff')); ?>;
        }
        <?php echo htmlentities($config['home_css']); ?>
    </style>
</head>
<body>
    <div class="headBg" style="background-image: url(<?php echo htmlentities($config['home_bg']); ?>);"></div>
    <div id="app" v-cloak>
        <div class="headerBox">
    <div class="bg" <?php if(!(empty($fixed) || (($fixed instanceof \think\Collection || $fixed instanceof \think\Paginator ) && $fixed->isEmpty()))): ?>:style="{ opacity: elementOpacity }"<?php endif; ?>></div>
    <div class="box">
        <a href="/" class="logoBox" <?php if(!(empty($fixed) || (($fixed instanceof \think\Collection || $fixed instanceof \think\Paginator ) && $fixed->isEmpty()))): ?>:style="{ opacity: elementOpacity }"<?php endif; ?>>
            <?php if(!(empty($config['logo']) || (($config['logo'] instanceof \think\Collection || $config['logo'] instanceof \think\Paginator ) && $config['logo']->isEmpty()))): ?>
            <img class="logo" src="<?php echo htmlentities($config['logo']); ?>"></img>
            <?php endif; if($config['app_name'] && $config['app_name_hide']!=1): ?>
            <div class="title"><?php echo htmlentities($config['app_name']); ?></div>
            <?php endif; ?>
        </a>
        <div class="search" <?php if(!(empty($fixed) || (($fixed instanceof \think\Collection || $fixed instanceof \think\Paginator ) && $fixed->isEmpty()))): ?>:style="{ opacity: elementOpacity }"<?php endif; ?>>
            <input type="text" v-model="keyword" placeholder="输入关键字进行搜索" @keyup.enter="searchBtn" confirm-type="search" @confirm="searchBtn">
            <div class="btn" @click="searchBtn">
                <i class="iconfont icon-sousuo"></i>
            </div>
        </div>
        <div class="navs">
            <?php if(!(empty($config['qcode']) || (($config['qcode'] instanceof \think\Collection || $config['qcode'] instanceof \think\Paginator ) && $config['qcode']->isEmpty()))): ?>
            <div class="item" @click="qcodeVisible = true">加入群聊</div>
            <?php endif; if(empty($config['app_demand']) || (($config['app_demand'] instanceof \think\Collection || $config['app_demand'] instanceof \think\Paginator ) && $config['app_demand']->isEmpty())): ?>
            <div class="item" @click="layerVisible = true">提交需求</div>
            <?php endif; ?>
            <div class="btns" v-html="`<?php echo htmlentities($config['app_links']); ?>`"></div>
            
            <div class="iconfont icon-caidan" @click="drawer = true"></div>
        </div>
    </div>
</div>
<div class="headerKox"></div>


<el-dialog
    v-model="qcodeVisible"
    width="300"
  >
    <img src="<?php echo htmlentities($config['qcode']); ?>" style="width: 100%" />
</el-dialog>

<el-dialog
    v-model="layerVisible"
    width="300"
  >
    <div class="layerBox">
		<div class="vname">提交需求</div>
	    <el-input
            v-model="content"
            placeholder="请输入你想看的资源信息~"
            type="textarea"
            resize='none'
          ></el-input>
		<div class="vbtn" @click="saveBtn">提交</div>
	</div>
</el-dialog>
<el-dialog
    v-model="drawer"
    width="300"
    center
  >
    <div class="drawer">
        <?php if(!(empty($config['qcode']) || (($config['qcode'] instanceof \think\Collection || $config['qcode'] instanceof \think\Paginator ) && $config['qcode']->isEmpty()))): ?>
        <div class="item" @click="qcodeVisible = true">加入群聊</div>
        <?php endif; if(empty($config['app_demand']) || (($config['app_demand'] instanceof \think\Collection || $config['app_demand'] instanceof \think\Paginator ) && $config['app_demand']->isEmpty())): ?>
        <div class="item" @click="layerVisible = true">提交需求</div>
        <?php endif; ?>
        <div class="btns" v-html="`<?php echo htmlentities($config['app_links']); ?>`"></div>
    </div>
</el-dialog>
        <div class="searchBox searchList">
            <div class="search">
                 <div class="select" @click="selectBtn">
                    <?php if($category_id == ''): ?>全部<?php endif; foreach($category as $key=>$vo): if($category_id == $vo['id']): ?><?php echo htmlentities($vo['name']); ?><?php endif; ?>
                    <?php endforeach; ?>
                    <i class="iconfont icon-xiala" style="font-size: 3vw"></i>
                 </div> 
                <input type="text" v-model="keyword" placeholder="输入关键字进行搜索" @keyup.enter="searchBtn" confirm-type="search" @confirm="searchBtn">
                <div class="btn" @click="searchBtn">
                    <i class="iconfont icon-sousuo"></i>
                </div>
            </div>
        </div>
        <div class="listBox">
            <div class="screen">
                <div class="fixed">
                    <h3>筛选</h3>
                    <div class="box">
                        <a href="/s/<?php echo htmlentities($keyword); ?>.html" class="<?php if($category_id == ''): ?>active<?php endif; ?>">全部</a>
                        <?php foreach($category as $key=>$vo): ?>
                        <a href="/s/<?php echo htmlentities($keyword); ?>-1-<?php echo htmlentities($vo['id']); ?>.html" class="<?php if($category_id == $vo['id']): ?>active<?php endif; ?>"><?php echo htmlentities($vo['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="left">
                <h3>为您找到【<span><?php echo htmlentities($keyword); ?></span>】相关资源<span> <?php echo htmlentities($list['total_result']); ?> </span>条</h3>
                <div class="box">
                    <?php if($list['total_result']>0): ?>
                    <div class="list">
                        <?php foreach($list['items'] as $key=>$vo): ?>
                        <a class="item" target="_blank" href="<?php echo htmlentities($vo['url']); ?>">
                            <div class="title">
                                <?php echo $vo['name']; ?>
                            </div>
                            <div class="type time"><?php echo htmlentities($vo['times']); ?></div>
                            <div class="type">
                                <?php if($vo['is_type']==1): ?>
                                <span>来源：阿里云盘</span>
                                <?php elseif($vo['is_type']==2): ?>
                                <span>来源：百度网盘</span>
                                <?php elseif($vo['is_type']==3): ?>
                                <span>来源：UC网盘</span>
                                <?php elseif($vo['is_type']==4): ?>
                                <span>来源：迅雷网盘</span>
                                <?php else: ?>
                                <span>来源：夸克网盘</span>
                                <?php endif; if(!(empty($vo['code']) || (($vo['code'] instanceof \think\Collection || $vo['code'] instanceof \think\Paginator ) && $vo['code']->isEmpty()))): ?>
                                <span>提取码：<span><?php echo htmlentities($vo['code']); ?></span></span>
                                <?php endif; ?>
                            </div>
                            <div class="btns">
                                <div class="btn" @click.stop="copyText($event,'<?php echo htmlentities($vo['title']); ?>','<?php echo htmlentities($vo['url']); ?>','<?php echo htmlentities($vo['code']); ?>')"><i class="iconfont icon-fenxiang1"></i>复制分享</div>
                                <div class="btn" @click.stop="goLink($event,'<?php echo htmlentities($vo['id']); ?>')"><i class="iconfont icon-fangwen"></i>查看详情</div>
                                <div class="btn">
                                    <img src="/static/index/images/<?php echo htmlentities($vo['is_type']); ?>.png" class="icon" />
                                    立即访问
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="page">
                        <?php if(!(empty($list['total_result']) || (($list['total_result'] instanceof \think\Collection || $list['total_result'] instanceof \think\Paginator ) && $list['total_result']->isEmpty()))): ?>
                        <el-pagination background layout="prev, pager, next" :pager-count="3" :default-current-page="<?php echo htmlentities($page_no); ?>" :default-page-size="<?php echo htmlentities($page_size); ?>" :total="<?php echo htmlentities($list['total_result']); ?>" @change="changeBtn"></el-pagination>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <el-empty style="margin-top: 10%;" :image-size="200" image="<?php echo isset($config['search_bg']) ? htmlentities($config['search_bg']) : ''; ?>" description="<?php echo htmlentities((isset($config['search_tips']) && ($config['search_tips'] !== '')?$config['search_tips']:'未找到，可换个关键词尝试哦~')); ?>"></el-empty>
                    <?php endif; ?>
                </div>
            </div>
            <div class="right">
                <block v-for="(item,index) in rankList" :key="index">
                    <div class="nav">
                        <img :src="item.image" v-if="item.image">
                        {{item.name}}
                    </div>
                    <div class="box" v-if="item.list && item.list.length>0">
                        <div class="list">   
                            <a :href="'/s/'+vo.title+'.html'" v-for="(vo,i) in item.list" :key="i" class="item" v-show="i<5">
                                <p>
                                    <span>{{i+1}}</span>
                                    {{vo.title}}
                                </p>
                            </a>
                        </div>
                    </div>
                </block>
            </div>
        </div>
        <div class="footerBox">
    <div class="box">
        <p><?php echo $config['footer_dec']; ?></p>
        <p>
            <?php echo $config['footer_copyright']; ?>
            <a href="/sitemap.xml" target="_blank">网站地图</a>
        </p>
    </div>
</div>
    </div>
    <script src="/static/index/js/vue.global.min.js"></script>
<script src="/static/index/js/index.full.min.js"></script>
<script src="/static/index/js/axios.min.js"></script>
<script>
    const { createApp, ref, onMounted, onUnmounted } = Vue;
    const { ElButton, ElMessage  } = ElementPlus;
    const app = createApp({
        setup() {
            // 定义响应式数据
            const elementOpacity = ref(0);
            const scrollThreshold = ref(150); // 动态设置的滚动阈值
            const keyword = ref('<?php echo isset($keyword) ? htmlentities($keyword) : ''; ?>');
            const qcodeVisible = ref(false);
            const layerVisible = ref(false);
            const content = ref('');
            const load = ref(false)
            const drawer = ref(false)
            const rankList = ref([]);
            const rankDj = ref([]);
            const is_m = ref(0);
            
            
            // 公共消息方法
            const showMessage = (message, type = 'info') => {
                ElMessage({
                    message,
                    type,
                    plain: true,
                });
            };


             // 滚动监听方法
            const handleScroll = () => {
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                elementOpacity.value = scrollTop >= scrollThreshold.value
                    ? Math.min((scrollTop - scrollThreshold.value) / 100, 1)
                    : Math.max(1 - (scrollThreshold.value - scrollTop) / 100, 0);
    
                const boxElement = document.querySelector('.listBox .screen .fixed .box');
                if (boxElement.style.display === 'block' && is_m.value) {
                    boxElement.style.display = 'none'; // 隐藏元素
                }
            };

            // 搜索按钮点击事件
            const searchBtn = () => {
                if (!keyword.value) {
                    return showMessage('请输入你要搜索的内容~', 'error');
                }
                const currentUrl = window.location.href;
                const targetUrl = `/s/${keyword.value}.html`;
                if (currentUrl.includes('/s/') || currentUrl.includes('/d/')) {
                    window.location.href = targetUrl;
                } else {
                    window.open(targetUrl, '_blank');
                }
            };
            
            // 保存按钮点击事件
            const saveBtn = async () => {
                if (!content.value) {
                    return showMessage('请输入你想看的资源信息~', 'error');
                }
                if (load.value) return;
    
                load.value = true;
                try {
                    const response = await axios.post('/api/tool/feedback', { content: content.value });
                    showMessage(response.data.message, response.data.code === 200 ? 'success' : 'error');
                    if (response.data.code === 200) {
                        layerVisible.value = false;
                        content.value = '';
                    }
                } finally {
                    load.value = false;
                }
            };
            
            const setnum = (num) => (num / 10000).toFixed(2) + 'W';
            
            const goLink = (event,id) => {
                event.preventDefault();
                window.location.href = `/d/${id}.html`;
            }
            
            const changeBtn = (e) => {
                const category_id = `<?php echo htmlentities($category_id); ?>`;
                if(category_id){
                    window.location.href = `/s/${keyword.value}-${e}-${category_id}.html`;
                }else{
                    window.location.href = `/s/${keyword.value}-${e}.html`;
                }
            };
            
            const copyText = async(event,title,url,code) => {
                event.preventDefault();
                var text = '标题：'+title+'\n链接：'+url
                if (code) text += `\n提取码：${code}`;
                text += `\n由【${'<?php echo htmlentities($config['app_name']); ?>'}${window.location.hostname}】供网盘分享链接`;
                
                
                try {
                    // 优先使用 navigator.clipboard
                    await navigator.clipboard.writeText(text);
                    showMessage('复制成功', 'success');
                } catch (err) {
                    // 如果 navigator.clipboard 失败，使用 document.execCommand 作为回退
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    textArea.style.position = 'fixed';  // 避免滚动
                    textArea.style.opacity = 0;
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
            
                    try {
                        const successful = document.execCommand('copy');
                        if (successful) {
                            showMessage('复制成功', 'success');
                        } else {
                            throw new Error('复制失败');
                        }
                    } catch (err) {
                        showMessage('复制失败，请手动复制', 'error');
                    }
            
                    document.body.removeChild(textArea);
                }
            }
            
            const selectBtn = () => {
                const boxElement = document.querySelector('.listBox .screen .fixed .box');
                // 切换 display 属性，显示或隐藏
                if (boxElement.style.display === 'none' || boxElement.style.display === '') {
                    boxElement.style.display = 'block'; // 显示
                } else {
                    boxElement.style.display = 'none'; // 隐藏
                }
            }
            
            const handleDeviceType = () => {
                const isMobile = window.matchMedia('(max-width: 768px)').matches;
                if (isMobile) {
                    // 手机端的逻辑
                    is_m.value = 1
                } else {
                    // 电脑端的逻辑
                    is_m.value = 0
                }
            };


            // 组件挂载时添加滚动监听
            onMounted(() => {
                handleDeviceType();
                
                window.addEventListener('scroll', handleScroll);
                window.addEventListener('resize', handleDeviceType);
            });

            // 组件卸载时移除滚动监听
            onUnmounted(() => {
                window.removeEventListener('scroll', handleScroll);
                window.removeEventListener('resize', handleDeviceType);
            });

            // 返回数据和方法
            return { elementOpacity, scrollThreshold, keyword, searchBtn, rankList, setnum, qcodeVisible, layerVisible, content, saveBtn, rankDj,goLink,changeBtn,copyText,drawer,selectBtn,is_m };
        }
    })
    .use(ElementPlus) // 使用 Element Plus
    .mount('#app'); // 挂载 Vue 实例
</script>
    <script type="text/javascript" charset="utf-8">
        // if(!app.is_m){
            app.rankList = JSON.parse('<?php echo json_encode($rankList, JSON_UNESCAPED_UNICODE); ?>');
            for (const item of app.rankList) {
                axios.get('/api/tool/ranking',{
                    params: {
                      channel: item.name,
                      is_m: app.is_m
                    }
                })
                  .then(function (res) {
                        item.list = res.data.data
                  })
            }
        // }
    </script>
</body>
</html>