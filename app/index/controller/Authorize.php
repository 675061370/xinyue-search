<?php

namespace app\index\controller;

use app\index\QfShop;

class Authorize extends QfShop
{
    public function index()
    {
        $callback = '';
        if (input('callback')) {
            $callback = urldecode(input('callback'));
        }
        $callbackWechat = urlencode(getFullDomain() . "/index/authorize/callback/");
        return redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->wechat_appid . "&redirect_uri=" . $callbackWechat . "&response_type=code&scope=snsapi_base&state=" . urlencode($callback) . "#wechat_redirect");
    }
    public function callback()
    {
        $callback = '/index';
        if (input('state')) {
            $callback = urldecode(input('state'));
        }
        if (!input('code')) {
            return redirect($callback);
        }
        $code = input('code');
        $retStr = curlHelper("https://api.weixin.qq.com/sns/oauth2/access_token?appid=" .  $this->wechat_appid . "&secret=" . $this->wechat_appkey . "&code={$code}&grant_type=authorization_code")['body'];
        $retObj = json_decode($retStr);
        if (isset($retObj->errcode)) {
            return redirect($callback);
        } else {
            $access_token = $retObj->access_token;
            $openid = $retObj->openid;
            if (!$this->updateWechatUserInfo($openid)) {
                return redirect($callback);
            }
            cookie('user_id', $this->user['user_id'], 3600000);
            cookie('user_ticket', getTicket($this->user['user_id']), 3600000);
            return redirect($callback);
        }
    }
}
