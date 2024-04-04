<?php

namespace app\job;
use think\queue\Job;
use app\model\Source as SourceModel;
use app\model\SourceLog as SourceLogModel;

class InsertDataJob
{
    // 处理具体的插入操作
    public function fire(Job $job, $value)
    {
        try {
            $this->model = new SourceModel();
            $this->SourceLogModel = new SourceLogModel();
            $logId = $value['logId'];

            $detail = $this->model->where('title', $value['title'])->find();
            if(empty($detail)){
                $url = $value['url'];
                $substring = strstr($url, 's/');

                if ($substring !== false) {
                    $pwd_id = substr($substring, 2); // 去除 's/' 部分

                    $urlData =  array(
                        'cookie' => Config('qfshop.quark_cookie'),
                        'url' => $url,
                    );
                    $res = curlHelper($value['urls']."/api/open/transfer", "POST", $urlData)['body'];
                    $res = json_decode($res, true);
    
                    if($res['code'] !== 200){
                        $this->SourceLogModel->editLog($logId,'fail_num',$res['message']);
                        return;
                    }
    
                    //添加资源到系统中
                    $data["title"] = $value['title'];
                    $data["url"] = $res['data']['share_url'];
                    $data["update_time"] = time();
                    $data["create_time"] = time();
                    $this->model->insertGetId($data);
                    $this->SourceLogModel->editLog($logId,'new_num','');
                } else {
                    $this->SourceLogModel->editLog($logId,'fail_num','资源地址格式有误');
                }
            }else{
                //如已有此资源 跳过
                $this->SourceLogModel->editLog($logId,'skip_num','重复跳过转存');
            }

            // 处理完成后可以删除任务，避免重复执行
            $job->delete();
        } catch (\Exception $e) {
            // 处理异常情况，例如记录日志等
            \think\facade\Log::error('数据插入失败：' . $e->getMessage());

            // 记录日志后可以选择重新放回队列，等待下次重试
            // $job->release(60); // 重新放回队列，延迟 60 秒后重试
        }
    }
}