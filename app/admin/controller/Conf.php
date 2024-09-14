<?php

namespace app\admin\controller;

use think\App;
use app\admin\QfShop;
use app\model\Conf as ConfModel;

class Conf extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //筛选字段
        $this->searchFilter = [
            "conf_id" => "=", //相同筛选
            "conf_key" => "like", //相似筛选
            "conf_value" => "like", //相似筛选
            "conf_title" => "like", //相似筛选
            "conf_status" => "=", //相同筛选
            "conf_type" => "=", //相同筛选
        ];
        $this->insertFields = [
            "conf_key", "conf_value", "conf_title", "conf_desc", "conf_status", "conf_type", "conf_spec", "conf_content", "conf_sort", "conf_system"
        ];
        $this->updateFields = [
            "conf_key", "conf_value", "conf_title", "conf_desc", "conf_status", "conf_type", "conf_spec", "conf_content", "conf_sort", "conf_system"
        ];
        $this->insertRequire = [
            'conf_title' => "参数名称必须填写",
            'conf_key' => "参数字段必须填写",
        ];
        $this->updateRequire = [
            'conf_title' => "参数名称必须填写",
            'conf_key' => "参数字段必须填写",
        ];
        $this->model = new ConfModel();
    }
    /**
     * 获取列表接口基类
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
        $order = "conf_sort desc, conf_id asc";
        //设置Model中的 per_page
        $this->setGetListPerPage();
        //查询数据
        $dataList = $this->model->getListByPage($map, $order, $this->selectList);
        return jok('数据获取成功', $dataList);
    }
    /**
     * 读取基本配置
     *
     * @return void
     */
    public function getBaseConfig()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $datalist = $this->model->where('conf_status', 1)->order("conf_sort desc ".$this->pk . " asc")->select();
        foreach ($datalist as $key => $value) {
            if($value['conf_content']){
                $value['conf_content'] = explode("\n",$value['conf_content']);
            }
        }
        return jok('', $datalist);
    }
    /**
     * 更新基础配置
     *
     * @return void
     */
    public function updateBaseConfig()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        foreach (input("post.") as $k => $v) {
            $map["conf_key"] = $k;
            $item = $this->model->where($map)->find();
            if (empty($item)) {
                continue;
            }
            if(is_array($v)){
                $v = implode(",",$v);
            }
            $this->model->where("conf_key", $k)->update(["conf_value" => $v]);
        }
        return jok("配置修改成功");
    }

    /**
     * 添加接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function add()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        //校验Insert字段是否填写
        $error = $this->validateInsertFields();
        if ($error) {
            return $error;
        }
        //从请求中获取Insert数据
        $data = $this->getInsertDataFromRequest();

        $res = $this->model->where('conf_key',$data['conf_key'])->find();
        if ($res) {
            return jerr("参数字段已存在");
        }
        
        //添加这行数据
        $data['conf_value'] = '';
        $this->insertRow($data);
        return jok('添加成功');
    }

    /**
     * 修改接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function update()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        //根据主键获取一行数据
        $item = $this->getRowByPk();
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        //校验Update字段是否填写
        $error  = $this->validateUpdateFields();
        if ($error) {
            return $error;
        }
        //从请求中获取Update数据
        $data = $this->getUpdateDataFromRequest();

        $res = $this->model->where('conf_key',$data['conf_key'])->find();
        if ($res['conf_id']!= input("conf_id") && $res) {
            return jerr("参数字段已存在");
        }
        //根据主键更新这条数据
        $this->updateByPk($data);
        return jok('修改成功');
    }

    /**
     * 删除接口基类
     *
     * @return void
     */
    public function delete()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            //根据主键获取一行数据
            $item = $this->getRowByPk();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            if($item['conf_system']==1){
                return jerr("系统参数，禁止删除", 404);
            }
            //单个操作
            $this->deleteBySingle();
        } else {
            //批量操作
            return jerr("暂不支持批量删除", 400);
        }
        return jok('删除成功');
    }
}
