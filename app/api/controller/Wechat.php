<?php

namespace app\api\controller;

use think\App;
use app\api\QfShop;

use EasyWeChat\Factory;
use EasyWeChat\OfficialAccount\Application;

use app\model\Source as SourceModel;

class Wechat extends QfShop
{
    /*
        微信公众平台配置如下：
        微信公众平台-基本配置-生成AppSecret并设置ip白名单
        启用服务器配置即可
        URL：你的域名/api/wechat/serve
        Token：自行生成
        EncodingAESKey：随机生成即可
        消息加解密方式：推荐即可
    */
    private $config = [
        'app_id' => '',//公众号appid
        'secret' => '',//公众号secret
        'token'   => '', //Token 自行生成32字符串 英文或数字
        'aes_key' =>  '' //微信公众平台生成后填写到这里
    ];
 
    public function serve()
    {
        // 创建一个微信公众账号实例，使用指定的配置
        $app = Factory::officialAccount($this->config);
        
        // 设置消息处理回调函数
        $app->server->push(function ($message) {
            // 检查用户消息内容中是否包含“搜剧”关键字
            if (strpos($message['Content'], '搜') !== false) {
                
                // 去除“搜剧”关键字和空格，提取用户输入的剧名
                $newString = str_replace(['搜剧', ' '], '', $message['Content']);
                $newString = str_replace(['搜', ' '], '', $message['Content']);
                
                // 实例化资源模型，并搜索匹配的剧名
                $SourceModel = new SourceModel();
                $list = $SourceModel->where('title', 'like', '%' . $newString . '%')->limit(5)->select();
                
                // 构建回复内容
                if (!$list->isEmpty()) {
                    $content = "";
                    foreach ($list as $item) {
                        if ($content) {
                            $content = $content."\n".$item['title']."\n".$item['url']."\n --------------------";
                        } else {
                            $content = $item['title']."\n".$item['url']."\n --------------------";
                        }
                    }
                    // 添加操作步骤说明
                    $content = $content."\n 步骤：点击上方链接-打开网盘-点立即查看-点右下角保存-打开文件-按文件名排序即可从第一集开始-自动-全集播放";
                } else {
                    // 如果没有找到匹配的剧名，提示用户减少关键词尝试搜索
                    $content = "未找到，减少关键词尝试搜索。";
                }
                
                return $content;  // 返回匹配结果或提示信息
            }
    
            // 如果不匹配条件，返回空字符串或不做任何回复
            return '';
        });
    
        // 开始处理微信服务器的请求并返回响应
        $response = $app->server->serve();
        $response->send();  // 发送响应
    }

}
