<?php

namespace app\api\controller;

use think\App;
use app\api\QfShop;
use app\model\User as Usermodel;
use app\model\Ads as Adsmodel;
use app\model\Feedback as FeedbackModel;
use app\model\SourceCategory as SourceCategoryModel;

class Tool extends QfShop
{
    /**
     * 系统配置参数
     *
     * @return void
     */
    public function getConfig()
    {
        $data = [
            'app_name'        => Config('qfshop.app_name'),
            'qcode'   => getimgurl(Config('qfshop.qcode')),
            'logo'   => getimgurl(Config('qfshop.logo')),
            'app_description'   => Config('qfshop.app_description'),
        ];
        return jok('获取成功',$data);
    }
    /**
     * 上传图片
     *
     * @return void
     */
    public function Upload()
    {
        // 获取当前登录的用户信息
        $userInfo = $this->getLoginUser();
        
        try {
            $file = request()->file('file');
        } catch (\Exception $error) {
            return jerr('上传文件失败，请检查你的文件！');
        }
        $Usermodel = new Usermodel();
        $data = $Usermodel->Upload($file, $userInfo);
        return jok('上传成功',$data);
    }

    /**
     * 根据广告位关键词获取广告图片列表
     * 
     * @return void
     */
    public function getAdsCode()
    {
        $Adsmodel = new Adsmodel();
        $data = $Adsmodel->getAdsCode(input(''));
        return jok('获取成功',$data);
    }

    /**
     * 用户反馈
     * 
     * @return void
     */
    public function feedback()
    {
        $data = input('');
        if (empty($data['content'])) {
            return jerr("请输入要看的内容");
        }
        $FeedbackModel = new FeedbackModel();
        $FeedbackModel->save(['content' => $data['content']]);
        return jok('已反馈');
    }
    
    

    /**
     * 获取首页排行榜数据
     *
     * @return void
     */
    public function ranking()
    {
        $channel = input('channel');
        $is_m = input('is_m')??0;
        
        if (empty($channel)) {
            return [];
        }
    
        // 使用 ThinkPHP 提供的 runtime_path() 函数获取 runtime 目录路径
        $cacheDir = runtime_path('cache'); // runtime/cache 目录
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true); // 确保缓存目录存在
        }
    
        // 根据 channel 值生成缓存文件名
        $cacheFile = $cacheDir . "ranking_data_{$channel}.cache";
        $cacheTime = 12*3600; // 缓存时间为 12 小时
    
        // 检查缓存文件是否存在且在缓存时间内
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            // 从缓存中读取数据
            $data = json_decode(file_get_contents($cacheFile), true);
        } else {
            $data = [];
            if (!empty($channel)) {
                $queryParams =  array(
                    "area" =>  "全部",
                    "year" =>  "全部",
                    "channel" =>  $channel,
                    "rank_type" =>  "最热",
                    "cate" =>  "全部",
                    "from" =>  "hot_page",
                    "start" =>  0,
                    "hit" =>  Config('qfshop.ranking_num') ?? 1,
                );
                $res = curlHelper("https://biz.quark.cn/api/trending/ranking/getYingshiRanking", "GET", null, [], $queryParams)['body'];
                $res = json_decode($res, true);
                try {
                    foreach ($res['data']['hits']['hit']['item'] as $key => $value) {
                        $data[] = array(
                            "title" => $value['title'],
                            "src" => $value['src'],
                            "ranking" => $value['ranking'],
                            "hot_score" => $value['hot_score'],
                            "desc" => $value['desc'],
                        );
                    }
                } catch (Exception $error) {
                    $data = [];
                }
    
                // 将数据缓存到文件中
                file_put_contents($cacheFile, json_encode($data));
            }
        }
        
        if($is_m==1){
             $ranking_m_num = Config('qfshop.ranking_m_num') ?? 6;
            $data = array_slice($data, 0, $ranking_m_num);
        }
       
        return jok('获取成功', $data);
    }


}
