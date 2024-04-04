<?php

namespace app\index\controller;

use app\index\QfShop;

class Jssdk extends QfShop
{
    protected $ServiceToken = 'QfShop';
    protected $wechat;
    public function index()
    {
        $this->easyWeChat->jssdk->setUrl(input('url'));
        $ret = $this->easyWeChat->jssdk->buildConfig([
            'scanQRCode',
            'closeWindow',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'updateAppMessageShareData',
            'updateTimelineShareData'
        ], false, false, false);
        return $ret;
    }
}
