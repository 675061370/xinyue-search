<?php

namespace app\admin\controller;

use think\App;
use app\admin\QfShop;
use app\model\Node as NodeModel;

class Node extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //筛选字段
        $this->searchFilter = [
            "node_id" => "=", //相同筛选
            "node_show" => "=", //相同筛选
            "node_title" => "like", //相似筛选
            "node_desc" => "like", //相似筛选
            "node_module" => "like", //相似筛选
            "node_controller" => "like", //相似筛选
            "node_action" => "like", //相似筛选
        ];
        $this->insertFields = [
            "node_title", "node_desc", "node_module", "node_action", "node_controller", "node_icon", "node_show", "node_pid", "node_order", "node_login", "node_access"
        ];
        $this->updateFields = [
            "node_title", "node_desc", "node_module", "node_action", "node_controller", "node_icon", "node_show", "node_pid", "node_order", "node_login", "node_access"
        ];
        $this->insertRequire = [
            'node_title' => "节点名称必须填写",
            'node_module' => "节点模块必须填写",
        ];
        $this->updateRequire = [
            'node_title' => "节点名称必须填写",
            'node_module' => "节点模块必须填写",
        ];
        $this->model = new NodeModel();
    }

    /**
     * 添加节点
     *
     * @return void
     */
    public function add()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $error = $this->validateInsertFields();
        if ($error) {
            return $error;
        }
        $data = $this->getInsertDataFromRequest();
        $data['node_module'] = strtolower($data['node_module']);
        $data['node_controller'] = input("node_controller") ? strtolower($data['node_controller']) : "";
        $data['node_action'] = input("node_action") ? strtolower($data['node_action']) : "";
        $this->insertRow($data);
        return jok('用户添加成功');
    }
    /**
     * 更新节点
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
        $item = $this->getRowByPk();
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        $error = $this->validateUpdateFields();
        if ($error) {
            return $error;
        }
        $data = $this->getUpdateDataFromRequest();
        if($item['node_id'] == $data['node_pid']){
            return jerr("不能选择自己为上级菜单", 404);
        }
        $data['node_module'] = strtolower($data['node_module']);
        $data['node_controller'] = input("node_controller") ? strtolower($data['node_controller']) : "";
        $data['node_action'] = input("node_action") ? strtolower($data['node_action']) : "";
        $this->updateByPk($data);
        return jok('节点信息更新成功');
    }


    /**
     * 删除节点
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
            return jerr($this->pk . "必须填写");
        }
        if (isInteger($this->pk_value)) {
            $map = [$this->pk => $this->pk_value];
            $item = $this->model->where($map)->find();
            if (empty($item)) {
                return jerr("数据查询失败");
            }
            $this->deleteBySingle();
            //删除对应ID的授权记录
            $this->authModel->where("auth_node", $this->pk_value)->delete();
        } else {
            $this->deleteByMultiple();
            //删除对应ID的授权记录
            $list = explode(',', $this->pk_value);
            $this->authModel->where("auth_node", 'in', $list)->delete();
        }
        return jok('删除节点成功');
    }

    /**
     * 获取所有节点
     *
     * @return void
     */
    public function getList()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $order = $this->table . "_order desc," . $this->pk . " asc";
        $map = [
            "node_pid" => 0
        ];
        $datalist = $this->model->where($map)->order($order)->select();
        $subMap = $this->getDataFilterFromRequest();
        for ($i = 0; $i < count($datalist); $i++) {
            $subDatalist = $this->model->field($this->selectList)->where($subMap)->where($this->table . "_pid", $datalist[$i][$this->pk])->order($order)->select();
            $datalist[$i]['sub'] = $subDatalist;
            for ($j = 0; $j < count($datalist[$i]['sub']); $j++) {
                $subDatalist2 = $this->model->field($this->selectList)->where($subMap)->where($this->table . "_pid", $datalist[$i]['sub'][$j][$this->pk])->order($order)->select();
                $datalist[$i]['sub'][$j]['sub'] = $subDatalist2;
            }
        }
        return jok('success', [
            'data'  => $datalist,
            'map'   => $map
        ]);
    }
    /**
     * 显示到菜单中
     *
     * @return void
     */
    public function show_menu()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (isInteger($this->pk_value)) {
            $this->model->where($this->pk, $this->pk_value)->update([
                $this->table . "_show" => 1,
                $this->table . "_updatetime" => time(),
            ]);
        } else {
            $list = explode(',', $this->pk_value);
            $this->model->where($this->pk, 'in', $list)->update([
                $this->table . "_show" => 1,
                $this->table . "_updatetime" => time(),
            ]);
        }
        return jok("显示成功");
    }

    /**
     * 从菜单中隐藏
     *
     * @return void
     */
    public function hide_menu()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (isInteger($this->pk_value)) {
            $this->model->where($this->pk, $this->pk_value)->update([
                $this->table . "_show" => 0,
                $this->table . "_updatetime" => time(),
            ]);
        } else {
            $list = explode(',', $this->pk_value);
            $this->model->where($this->pk, 'in', $list)->update([
                $this->table . "_show" => 0,
                $this->table . "_updatetime" => time(),
            ]);
        }
        return jok("隐藏成功");
    }
}
