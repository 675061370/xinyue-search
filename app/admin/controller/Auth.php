<?php

namespace app\admin\controller;

use think\App;
use app\admin\QfShop;
use app\model\Auth as AuthModel;

class Auth extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //筛选字段
        $this->searchFilter = [
            "auth_id" => "=", //相同筛选
        ];
        $this->model = new AuthModel();
    }

    /**
     * 清除访问日志
     *
     * @return void
     */
    public function clean()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $this->model->cleanAuth();
        return jok('授权信息清理成功');
    }
}
