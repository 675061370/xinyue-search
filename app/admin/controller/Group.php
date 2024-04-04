<?php

namespace app\admin\controller;

use think\App;
use app\admin\QfShop;
use app\model\Group as GroupModel;

class Group extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //筛选字段
        $this->searchFilter = [
            "group_id" => "=", //相同筛选
            "group_name" => "like", //相似筛选
        ];
        $this->insertFields = [
            "group_name", "group_desc"
        ];
        $this->updateFields = [
            "group_name", "group_desc"
        ];
        $this->insertRequire = [
            'group_name' => "组名称必须填写"
        ];
        $this->updateRequire = [
            'group_name' => "组名称必须填写"
        ];
        $this->model = new GroupModel();
    }
    /**
     * 修改用户组
     *
     * @return void
     */
    public function update()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        if (!isInteger($this->pk_value)) {
            return jerr("修改失败,参数错误", 400);
        }
        $item = $this->model->where($this->pk, $this->pk_value)->find();
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        if ($item[$this->pk] == 1) {
            return jerr("无法操作超级管理员组信息");
        }
        foreach ($this->updateRequire as $k => $v) {
            if (!input($k)) {
                return jerr($v);
            }
        }
        $data = [];
        foreach (input('post.') as $k => $v) {
            if (in_array($k, $this->updateFields)) {
                $data[$k] = $v;
            }
        }
        if (!input($this->table . "_name")) {
            return jerr("组名称必须填写", 400);
        }
        $data[$this->table . "_updatetime"] = time();
        $this->model->where($this->pk, $this->pk_value)->update($data);
        return jok('用户组信息更新成功');
    }

    /**
     * 禁用用户组
     *
     * @return void
     */
    public function disable()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            $map = [$this->pk => $this->pk_value];
            $item = $this->model->where($map)->find();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            if ($item[$this->pk] == 1) {
                return jerr("无法操作超级管理员组信息");
            }
            $this->model->where($map)->update([
                $this->table . "_status" => 1,
                $this->table . "_updatetime" => time(),
            ]);
        } else {
            $list = explode(',', $this->pk_value);
            $this->model->where($this->pk, 'in', $list)->where("group_id > 1")->update([
                $this->table . "_status" => 1,
                $this->table . "_updatetime" => time(),
            ]);
        }
        return jok("禁用用户组成功");
    }

    /**
     * 启用用户组
     *
     * @return void
     */
    public function enable()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            $map = [$this->pk => $this->pk_value];
            $item = $this->model->where($map)->find();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            if ($item[$this->pk] == 1) {
                return jerr("无法操作超级管理员组信息");
            }
            $this->model->where($map)->update([
                $this->table . "_status" => 0,
                $this->table . "_updatetime" => time(),
            ]);
        } else {
            $list = explode(',', $this->pk_value);
            $this->model->where($this->pk, 'in', $list)->where("group_id > 1")->update([
                $this->table . "_updatetime" => time(),
            ]);
        }
        return jok("启用用户组成功");
    }

    /**
     * 删除用户组
     *
     * @return void
     */
    public function delete()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            $item = $this->getRowByPk();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            if ($item[$this->pk] == 1) {
                return jerr("无法删除超级管理员组");
            }
            $this->deleteBySingle();
            //删除对应ID的授权记录
            $this->authModel->where([
                "auth_group" => $this->pk_value
            ])->delete();
        } else {
            $list = explode(',', $this->pk_value);
            $this->model->where($this->pk, 'in', $list)->where("group_id > 1")->delete();
            //删除对应ID的授权记录
            $this->authModel->where("auth_group", "in", $list)->delete();
        }
        return jok('删除用户组成功');
    }

    /**
     * 为用户组授权节点
     *
     * @return void
     */
    public function authorize()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        if (!isInteger($this->pk_value)) {
            return jerr("修改失败,参数错误", 400);
        }
        $item = $this->getRowByPk();
        if (empty($item)) {
            return jerr("用户组信息查询失败，授权失败", 404);
        }
        $this->authModel->where([
            "auth_group" => $this->pk_value
        ])->delete();
        if ($item[$this->pk] == 1) {
            return jerr("超级管理组无需授权！");
        }
        $node_ids = explode(",", input("node_ids"));
        foreach ($node_ids as $node_id) {
            if (intval($node_id) == 0) {
                continue;
            }
            $this->authModel->insert([
                "auth_group" => $this->pk_value,
                "auth_node" => $node_id,
                "auth_createtime" => time(),
                "auth_updatetime" => time()
            ]);
        }
        return jok('用户组授权成功');
    }
    /**
     * 获取用户组拥有的权限
     *
     * @return void
     */
    public function getAuthorize()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        if (!isInteger($this->pk_value)) {
            return jerr("修改失败,参数错误", 400);
        }
        $item = $this->getRowByPk();
        if (empty($item)) {
            return jerr("用户组信息查询失败，授权失败", 404);
        }
        $myAuthorizeList = $this->authModel->where("auth_group", $this->pk_value)->select();
        return jok('ok', $myAuthorizeList);
    }
    /**
     * 获取所有用户组
     *
     * @return void
     */
    public function getList()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $dataList = $this->model->select();
        return jok('用户组列表获取成功', $dataList);
    }
}
