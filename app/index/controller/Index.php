<?php

namespace app\index\controller;

use think\App;
use think\facade\View;
use think\facade\Request;
use think\facade\Cache;
use app\index\QfShop;


class Index extends QfShop
{

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @description: 报名页面
     * @param {*}
     * @return {*}
     */    
    public function index()
    {
        return View::fetch();
    }
}
