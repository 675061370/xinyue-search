<?php


namespace app\api\controller;

use think\App;
use app\api\QfShop;
use think\facade\Cache;
use Carbon\Carbon;

use app\model\Source as SourceModel;
use app\model\SourceLog as SourceLogModel;

class Source extends QfShop
{   
    public function __construct(App $app)
    {
        parent::__construct($app);
        //第三方转存接口地址
        $this->url = "https://pan.xinyuedh.com";

        $this->model = new SourceModel();
        $this->SourceLogModel = new SourceLogModel();
    }
    public function day()
    {
        // 当前日期
        $currentDate = Carbon::today()->toDateString();
        // 缓存键名
        $cacheKey = 'api_alone_date_' . $currentDate;

        // 检查缓存中是否存在该键
        if (Cache::has($cacheKey)) {
            return jerr("该接口今天已经执行过，请明天再试！");
        }

        Cache::set($cacheKey, time(), 43200);

        ini_set('max_execution_time', -1);
        //分页转存
        $page_no = 1;
        $dataList = '';
        $logId = '';
        while ($dataList=='' || !empty($dataList['items'])) {
            $searchData =  array(
                'page_no' => $page_no,
                'page_size' => 100,
                'type' => 2,
                'day' => 2,
            );
            $res = curlHelper($this->url."/api/search", "POST", $searchData)['body'];
            $res = json_decode($res, true);
            
            if($res['code'] !== 200){
                ini_set('max_execution_time', 300);
                return jerr($res['message']);
            }
            $dataList = $res['data'];
            $page_no++;

            if($logId == ''){
                $logId = $this->SourceLogModel->addLog('每日更新',$dataList['total_result']);
            }

            foreach ($dataList['items'] as $key => $value) {
                //如已有此资源 跳过
                $detail = $this->model->where('title', $value['title'])->find();
                if(!empty($detail)){
                    $this->SourceLogModel->editLog($logId,$dataList['total_result'],'skip_num','重复跳过转存');
                    continue;
                }

                $url = $value['url'];
                $substring = strstr($url, 's/');
    
                if ($substring !== false) {
                    $pwd_id = substr($substring, 2); // 去除 's/' 部分
                } else {
                    $this->SourceLogModel->editLog($logId,$dataList['total_result'],'fail_num','资源地址格式有误');
                    continue;
                }
    
                $urlData =  array(
                    'cookie' => Config('qfshop.quark_cookie'),
                    'url' => $url,
                );
                $res = curlHelper($this->url."/api/open/transfer", "POST", $urlData)['body'];
                $res = json_decode($res, true);
    
                if($res['code'] !== 200){
                    if($res['message'] == 'capacity limit[{0}]'){
                        $this->SourceLogModel->editLog($logId,$dataList['total_result'],'fail_num',$res['message']);
                        break;
                    }else{
                        $this->SourceLogModel->editLog($logId,$dataList['total_result'],'fail_num',$res['message']);
                        continue;
                    }
                }
    
                //添加资源到系统中
                $data["title"] = $value['title'];
                $data["url"] = $res['data']['share_url'];
                $data["update_time"] = time();
                $data["create_time"] = time();
                $this->model->insertGetId($data);
                $this->SourceLogModel->editLog($logId,$dataList['total_result'],'new_num','');
            }
            // 可以添加一个最大重试次数的限制，防止无限循环
            if ($page_no > 1000) {
                break;
            }
        }

        $this->SourceLogModel->editLog($logId,$dataList['total_result'],'','',3);
        ini_set('max_execution_time', 300);
        return jok('已提交任务，稍后查看结果',$dataList);
    }
}
