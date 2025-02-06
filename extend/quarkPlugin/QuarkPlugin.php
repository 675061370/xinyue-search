<?php

namespace QuarkPlugin;

use think\facade\Db;
use think\facade\Request;
use think\Exception;
use app\model\Source as SourceModel;
use app\model\SourceLog as SourceLogModel;

class QuarkPlugin
{
    protected $url;
    protected $model;
    protected $SourceLogModel;

    public function __construct()
    {
        //第三方转存接口地址
        $this->url = "";
        $this->model = new SourceModel();
        $this->SourceLogModel = new SourceLogModel();
        $this->source_category_id = 0;
    }

    public function getFiles($quark_cookie)
    {
        $urlData = array(
            'cookie' => $quark_cookie??'',
        );
        $res = curlHelper(Request::domain()."/api/open/getFiles", "POST", $urlData)['body'];
        $res = json_decode($res, true);

        if($res['code'] !== 200){
            return jerr($res['message']);
        }

        return $res['data'];
    }
    
    public function import($allData,$source_category_id)
    {
        $this->source_category_id = $source_category_id;

        $length = count($allData);

        $logId = $this->SourceLogModel->addLog('批量转入链接',$length);
        $isType = 1;
        $this->processDataConcurrently($allData, $logId, $length,10,$isType);
        $this->SourceLogModel->editLog($logId,$length,'','',3);
    }

    public function transfer($allData,$source_category_id)
    {
        $this->source_category_id = $source_category_id;

        $length = count($allData);

        $logId = $this->SourceLogModel->addLog('批量转存他人链接',$length);

        $this->processDataConcurrently($allData, $logId, $length);
        $this->SourceLogModel->editLog($logId,$length,'','',3);
    }

    public function transferAll($source_category_id,$day=0)
    {
        @set_time_limit(999999);
        
        $this->source_category_id = $source_category_id;

        // 分页转存
        $page_no = 1;
        $dataList = '';
        $logId = '';
        $allData = [];
        while ($dataList=='' || !empty($dataList['items'])) {
            $searchData =  array(
                'page_no' => $page_no,
                'page_size' => 10000,
                'type' => 2, //从旧到新排序  也就是先采集旧数据
                'day' => $day, //等2时 用于每日更新
                'category_id' => $this->source_category_id
            );
            $res = curlHelper($this->url."/api/search", "POST", $searchData)['body'];
            $res = json_decode($res, true);

            $page_no++;

            if($res['code'] == 200){
                $dataList = $res['data'];
                $allData = array_merge($allData, $dataList['items']??[]);

                if($logId == ''){
                    $name = $day==2?'每日更新':'全部转存';
                    $logId = $this->SourceLogModel->addLog($name,$dataList['total_result']);
                }

            }
            // 可以添加一个最大重试次数的限制，防止无限循环
            if ($page_no > 1000) {
                break;
            }
        }

        $this->processDataConcurrently($allData, $logId, $dataList['total_result']);

        $this->SourceLogModel->editLog($logId,$dataList['total_result'],'','',3);
    }
    
    function processDataConcurrently($allData, $logId=0, $total_result=0,$batchSize=1,$isType=0) {
        // $batchSize每批最多处理 5 个请求，并发量切勿设置过大 防止风控
        $multiCurl = curl_multi_init();
        $curlHandles = [];
    
        foreach ($allData as $key => $value) {
            // 如已有此资源 跳过
            $detail = $this->model->where('title', $value['title'])->find();
            if (!empty($detail)) {
                if(!empty($logId)){
                    $this->SourceLogModel->editLog($logId, $total_result, 'skip_num', '重复跳过转存');
                }
                continue;
            }
    
            // 处理 URL
            $url = $value['url'];
            $substring = strstr($url, 's/');
            if ($substring !== false) {
                $pwd_id = substr($substring, 2); // 去除 's/' 部分
            } else {
                if(!empty($logId)){
                    $this->SourceLogModel->editLog($logId, $total_result, 'fail_num', '资源地址格式有误');
                }
                continue;
            }
    
            // 生成请求数据
            $urlData = [
                'cookie' => Config('qfshop.quark_cookie') ?? '',
                'Authorization' => Config('qfshop.Authorization') ?? '',
                'expired_type' => 1, // 1正式资源 2临时资源
                'to_pdir_fid' => '', //存入目标文件
                'url' => $url,
                'code' => $value['code']??'',
                'isType' => $isType??0
            ];
    
            // 创建单个 cURL 句柄
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, Request::domain() . "/api/open/transfer");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($urlData));
    
            // 将句柄添加到 multiCurl 中
            curl_multi_add_handle($multiCurl, $ch);
            $curlHandles[$key] = $ch;
    
            // 控制并发数量，达到 $batchSize 处理一次
            if (count($curlHandles) >= $batchSize) {
                $this->executeAndHandleCurlMulti($multiCurl, $curlHandles, $allData, $logId, $total_result);
            }
        }
    
        // 处理剩余未满批次的请求
        if (!empty($curlHandles)) {
            $this->executeAndHandleCurlMulti($multiCurl, $curlHandles, $allData, $logId, $total_result);
        }
    
        curl_multi_close($multiCurl);
    }
    
    function executeAndHandleCurlMulti($multiCurl, &$curlHandles, $allData, $logId, $total_result) {
        // 执行 cURL 多请求
        $running = null;
        do {
            curl_multi_exec($multiCurl, $running);
            curl_multi_select($multiCurl);
        } while ($running > 0);
    
        // 获取结果并处理
        foreach ($curlHandles as $key => $ch) {
            $response = curl_multi_getcontent($ch);
            curl_multi_remove_handle($multiCurl, $ch);
            curl_close($ch);
    
            // 立即处理每个请求的返回结果
            $res = json_decode($response, true);
            $value = $allData[$key];
    
            if ($res['code'] !== 200) {
                if ($res['message'] == 'capacity limit[{0}]') {
                    if(!empty($logId)){
                        $this->SourceLogModel->editLog($logId, $total_result, 'fail_num', $res['message']);
                    }
                    break;
                } else {
                    if(!empty($logId)){
                        $this->SourceLogModel->editLog($logId, $total_result, 'fail_num', $res['message']);
                    }
                    continue;
                }
            }

            if(empty($value['title'])){
                $patterns = '/^\d+\./';
                $title = preg_replace($patterns, '', $res['data']['title']);
            }else{
                $title = $value['title'];
            }
            
            if(!empty($value['source_category_id'])){
                $source_category_id = $value['source_category_id']??0;
            }else{
                $source_category_id = $this->source_category_id;
            }
            

            // 添加资源到系统中
            $data["title"] = $title;
            $data["url"] = $res['data']['share_url'];
            $data["is_type"] = determineIsType($data["url"]);
            $data["code"] = $value['code']??'';
            $data["source_category_id"] = $source_category_id;
            $data["update_time"] = time();
            $data["create_time"] = time();
            $dataFid = $res['data']['fid']??'';
            $data["fid"] = is_array($dataFid) ? json_encode($dataFid) : $dataFid;
            
            $this->model->insertGetId($data);
            if(!empty($logId)){
                $this->SourceLogModel->editLog($logId, $total_result, 'new_num', '');
            }
        }
    
        // 清空句柄
        $curlHandles = [];
    }
    
    

}
