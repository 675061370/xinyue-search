<?php

namespace app\admin\controller;

use think\App;
use think\facade\Filesystem;
use app\admin\QfShop;
use app\model\Feedback as FeedbackModel;

class Feedback extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //查询列表时允许的字段
        $this->selectList = "*";
        //查询详情时允许的字段
        $this->selectDetail = "*";
        $this->model = new FeedbackModel();
    }


    /**
     * 获取列表接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function getList()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        //从请求中获取筛选数据的数组
        $map = $this->getDataFilterFromRequest();
        //从请求中获取排序方式
        $order = $this->getorderfromRequest();
        //设置Model中的 per_page
        $this->setGetListPerPage();
        //查询数据
        $dataList = $this->model->getListByPage($map, $order, $this->selectList);
        return jok('数据获取成功', $dataList);
    }
    
}
