<?php

namespace app\admin\controller;

use think\App;
use app\admin\QfShop;
use app\model\SourceCategory as SourceCategoryModel;

class SourceCategory extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //查询列表时允许的字段
        $this->selectList = "*";
        //查询详情时允许的字段
        $this->selectDetail = "*";
        //筛选字段
        $this->searchFilter = [
            "source_category_id" => "=",
            "name"=>"like"
        ];
        $this->insertFields = [
            //允许添加的字段列表
            "name","sort","status","image","is_update"
        ];
        $this->updateFields = [
            //允许更新的字段列表
            "name","sort","status","image","is_update"
        ];
        $this->insertRequire = [
            //添加时必须填写的字段
            // "字段名称"=>"该字段不能为空"
            "name"=>"分类名称必须填写",
        ];
        $this->updateRequire = [
            //修改时必须填写的字段
            // "字段名称"=>"该字段不能为空"
            "name"=>"分类名称必须填写",
        ];
        $this->model = new SourceCategoryModel();
    }


    /**
     * 获取列表接口
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
        //查询数据
        $dataList = $this->model->order('sort', 'desc')->select();
        return jok('数据获取成功', $dataList);
    }

    /**
     * 获取详情基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function detail()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $source_category_id = input("source_category_id");
        if (!$source_category_id) {
            return jerr("ID参数必须填写", 400);
        }
        //根据主键获取一行数据
        $item = $this->model->where("source_category_id", $source_category_id)->field($this->selectDetail)->find();
        if (empty($item)) {
            return jerr("没有查询到数据", 404);
        }
        return jok('数据加载成功', $item);
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
        //添加这行数据
        $data['create_time'] = time();
        $data['update_time'] = time();
        $this->model->insertGetId($data);
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
        $source_category_id = input("source_category_id");
        if (!$source_category_id) {
            return jerr("ID参数必须填写", 400);
        }
        
        //根据主键获取一行数据
        $item = $this->model->where("source_category_id", $source_category_id)->field($this->selectDetail)->find();
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
        //根据主键更新这条数据
        $data['update_time'] = time();
        $this->model->where("source_category_id", $source_category_id)->update($data);
        return jok('修改成功');
    }

    /**
     * 删除接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
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
        $source_category_id = input("source_category_id");
        if (!$source_category_id) {
            return jerr("ID参数必须填写", 400);
        }
        
        //根据主键获取一行数据
        $item = $this->model->where("source_category_id", $source_category_id)->field($this->selectDetail)->find();
        
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        
        if ($item['is_sys']==1) {
            return jerr("该类别不能删除");
        }
        
        //单个操作
        $map = ["source_category_id" => $source_category_id];
        $this->model->where($map)->delete();
       
        return jok('删除成功');
    }

    /**
     * 禁用接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function disable()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $source_category_id = input("source_category_id");
        if (!$source_category_id) {
            return jerr("ID参数必须填写", 400);
        }

        
        $d = [
            "status" => 1,
            "update_time" => time(),
        ];
        if(input("type") == 1){
            $d = [
                "is_update" => 0,
                "update_time" => time(),
            ];
        }

        if (isInteger($source_category_id)) {
            //根据主键获取一行数据
            $item = $this->model->where("source_category_id", $source_category_id)->field($this->selectDetail)->find();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            //单个操作
            $map = ["source_category_id" => $source_category_id];
            $this->model->where($map)->update($d);
        } else {
            //批量操作
            $list = explode(',', $source_category_id);
            $this->model->where("source_category_id", 'in', $list)->update($d);
        }
        return jok("禁用成功");
    }


    /**
     * 启用接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function enable()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $source_category_id = input("source_category_id");
        if (!$source_category_id) {
            return jerr("ID参数必须填写", 400);
        }

        $d = [
            "status" => 0,
            "update_time" => time(),
        ];
        if(input("type") == 1){
            $d = [
                "is_update" => 1,
                "update_time" => time(),
            ];
        }

        if (isInteger($source_category_id)) {
            //根据主键获取一行数据
            $item = $this->model->where("source_category_id", $source_category_id)->field($this->selectDetail)->find();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            //单个操作
            $map = ["source_category_id" => $source_category_id];
            $this->model->where($map)->update($d);
        } else {
            //批量操作
            $list = explode(',', $source_category_id);
            $this->model->where("source_category_id", 'in', $list)->update($d);
        }
        return jok("启用成功");
    }


    public function setStatus()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $source_category_id = input("source_category_id");
        if (!$source_category_id) {
            return jerr("ID参数必须填写", 400);
        }

        $d = [
            input("type") => input("status")==1?0:1,
            "update_time" => time(),
        ];

        //根据主键获取一行数据
        $item = $this->model->where("source_category_id", $source_category_id)->field($this->selectDetail)->find();
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        //单个操作
        $map = ["source_category_id" => $source_category_id];
        $this->model->where($map)->update($d);

        return jok("操作成功");
    }

}
