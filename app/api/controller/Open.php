<?php

namespace app\api\controller;

use think\App;
use think\facade\Cache;
use app\api\QfShop;
use app\model\Source as SourceModel;

class Open extends QfShop
{
    /**
     * @return void
     */
    public function transfer()
    {
        if(Config('qfshop.api_key') != input('api_key')){
            return jerr('api_key错误');
        }
        $urlData = [
            'expired_type' => input('expired_type')??1,  // 1正式资源 2临时资源
            'url' => input("url")?? '',
            'code' => input('code')??'',
            'isType' => input('isType')??0,
        ];
        if(empty($urlData['url'])){
            return jerr('资源地址不能为空');
        }

        $transfer = new \netdisk\Transfer();
        $res = $transfer->transfer($urlData);

        if($res['code'] !== 200){
            return jerr($res['message']);
        }

        $isSave = input('isSave')??0;
        if($isSave == 1){
            $data = [
                "title" => $res['data']['title'],
                "url" => $res['data']['share_url'],
                "is_type" => determineIsType($res['data']['share_url']),
                "code" => $res['data']['code'] ?? $urlData['code'] ?? '',
                "is_time" => $urlData['expired_type']==2 ? 1 : 0,
                "update_time" => time(),
                "create_time" => time(),
                "fid" => is_array($res['data']['fid'] ?? '') ? json_encode($res['data']['fid']) : ($res['data']['fid'] ?? '')
            ];
            $SourceModel = new SourceModel();
            $SourceModel->insertGetId($data);
        }
        return jok("获取成功",$res['data']);
    }

}
