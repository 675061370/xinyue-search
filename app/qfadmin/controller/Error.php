<?php

namespace app\qfadmin\controller;

use app\qfadmin\QfShop;
use think\facade\View;

class Error extends QfShop
{
    /**
     * 监听所有请求 渲染对应控制器下方法的页面
     */
    public function __call($method, $args)
    {
        // 判断是否是登录/注册/找回密码
        // 否则进行accesss授权验证 如错误 直接返回
        if (!(strtolower($this->controller) == "admin" && in_array(strtolower($this->action), ['login', 'resetpassword', 'reg']))) {
            $error = $this->access();
            if ($error) {
                return $error;
            }
        }else{
            cookie('access_token', null);
        }
        if (key_exists('callback', $args)) {
            View::assign('callback', $args['callback']);
        } else {
            View::assign('callback', '/qfadmin');
        }
        View::assign('datas', $args);
        return View::fetch();
    }
}
