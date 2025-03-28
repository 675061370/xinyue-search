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
        // 第三方转存接口地址
        $this->url = "";
        $this->model = new SourceModel();
        $this->SourceLogModel = new SourceLogModel();
        $this->source_category_id = 0;
    }

    public function getFiles($type=0,$pdir_fid=0)
    {
        $transfer = new \netdisk\Transfer();
        return $transfer->getFiles($type,$pdir_fid);
    }
    
    public function import($allData, $source_category_id)
    {
        $this->source_category_id = $source_category_id;

        $length = count($allData);
        $logId = $this->SourceLogModel->addLog('批量转入链接', $length);

        foreach ($allData as $data) {
            $this->processSingleData($data, $logId, $length, 1);
        }

        $this->SourceLogModel->editLog($logId, $length, '', '', 3);
    }

    public function transfer($allData, $source_category_id)
    {
        $this->source_category_id = $source_category_id;

        $length = count($allData);
        $logId = $this->SourceLogModel->addLog('批量转存他人链接', $length);

        foreach ($allData as $data) {
            $this->processSingleData($data, $logId, $length);
        }

        $this->SourceLogModel->editLog($logId, $length, '', '', 3);
    }

    public function transferAll($source_category_id, $day = 0)
    {
        if(empty($this->url)){
            return jerr('未配置转存接口地址');
        }

        @set_time_limit(999999);
        
        $this->source_category_id = $source_category_id;

        // 分页转存
        $page_no = 1;
        $allData = [];
        $logId = '';

        while (true) {
            $searchData = [
                'page_no' => $page_no,
                'page_size' => 10000,
                'type' => 2, //从旧到新排序  也就是先采集旧数据
                'day' => $day,  //等2时 用于每日更新  默认0是全部数据
                'category_id' => $this->source_category_id
            ];
            $res = curlHelper($this->url . "/api/search", "POST", $searchData)['body'];
            $res = json_decode($res, true);

            if ($res['code'] !== 200 || empty($res['data']['items'])) {
                break;
            }

            $dataList = $res['data'];
            $allData = array_merge($allData, $dataList['items']);
            $page_no++;

            if ($logId == '') {
                $name = $day == 2 ? '每日更新' : '全部转存';
                $logId = $this->SourceLogModel->addLog($name, $dataList['total_result']);
            }

            if ($page_no > 1000) {
                break;
            }
        }

        foreach ($allData as $data) {
            $this->processSingleData($data, $logId, count($allData));
        }

        $this->SourceLogModel->editLog($logId, count($allData), '', '', 3);
    }

    function processSingleData($value, $logId = 0, $total_result = 0, $isType = 0)
    {
        $detail = $this->model->where('title', $value['title'])>where('is_type', determineIsType($value['url']))->find();
        if (!empty($detail)) {
            if (!empty($logId)) {
                $this->SourceLogModel->editLog($logId, $total_result, 'skip_num', '重复跳过转存');
            }
            return;
        }

        $url = $value['url'];
        $substring = strstr($url, 's/');
        if ($substring === false) {
            if (!empty($logId)) {
                $this->SourceLogModel->editLog($logId, $total_result, 'fail_num', '资源地址格式有误');
            }
            return;
        }

        $urlData = [
            'expired_type' => 1,  // 1正式资源 2临时资源
            'url' => $url,
            'code' => $value['code'] ?? '',
            'isType' => $isType
        ];

        $transfer = new \netdisk\Transfer();
        $res = $transfer->transfer($urlData);

        if ($res['code'] !== 200) {
            if (!empty($logId)) {
                $this->SourceLogModel->editLog($logId, $total_result, 'fail_num', $res['message']);
            }
            return;
        }

        $title = empty($value['title']) ? preg_replace('/^\d+\./', '', $res['data']['title']) : $value['title'];
        $source_category_id = $value['source_category_id'] ?? $this->source_category_id;

        $data = [
            "title" => $title,
            "url" => $res['data']['share_url'],
            "is_type" => determineIsType($res['data']['share_url']),
            "code" => $res['data']['code'] ?? $value['code'] ?? '',
            "source_category_id" => $source_category_id,
            "update_time" => time(),
            "create_time" => time(),
            "fid" => is_array($res['data']['fid'] ?? '') ? json_encode($res['data']['fid']) : ($res['data']['fid'] ?? '')
        ];

        $this->model->insertGetId($data);
        if (!empty($logId)) {
            $this->SourceLogModel->editLog($logId, $total_result, 'new_num', '');
        }
    }
}
