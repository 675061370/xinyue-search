<?php

namespace app\model;

use think\facade\Db;

use app\model\QfShop;

class Auth extends QfShop
{
    /**
     * 判断用户组是否获得某节点的授权
     *
     * @param int 用户组ID
     * @param int 节点ID
     * @return void
     */
    public function auth($auth_group, $auth_node)
    {
        $auth = $this->where([
            "auth_group" => $auth_group,
            "auth_node" => $auth_node
        ])->find();
        return $auth ? true : false;
    }

    /**
     * 根据用户组 获取管理后台菜单
     *
     * @param int 用户组ID
     * @return void
     */
    public function getAdminMenuListByAdminId($group_id)
    {
        if ($group_id == 1) {
            //超级管理员组
            $list =  Node::where([
                    "node_pid"   =>  0,
                    "node_show"   =>  1,
                ])
                ->order("node_order desc,node_id asc")
                ->select();
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['subList'] = $this->getSubAdminListByPid($list[$i]['node_id'], $group_id);
                for ($j = 0; $j < count($list[$i]['subList']); $j++) {
                    $list[$i]['subList'][$j]['subList'] = $this->getSubAdminListByPid($list[$i]['subList'][$j]['node_id'], $group_id);
                }
            }
            return $list;
        } else {
            //其他组
            $list =  Node::where([
                    "node_pid"   =>  0,
                    "node_show"   =>  1,
                ])
                ->order("node_order desc,node_id asc")
                ->select();
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['subList'] = $this->getSubAdminListByPid($list[$i]['node_id'], 1);
                for ($j = 0; $j < count($list[$i]['subList']); $j++) {
                    $list[$i]['subList'][$j]['subList'] = $this->getSubAdminListByPid($list[$i]['subList'][$j]['node_id'], 1);
                }
            }
            if($list){
                $list = $list->toArray();
            }
            $list2 = Node::alias("node")
                ->view('node', '*')
                ->view('auth', '*', 'node.node_id=auth.auth_node', 'left')
                ->where([
                    "node_module"   =>  "qfadmin",
                    "node_show"   =>  1,
                    "auth_group"    => $group_id
                ])
                ->order("node_order desc,node_id asc")
                ->select()->toArray();

            foreach ($list2 as $k => $v) {
                //一级
                foreach ($list as $key => $value) {
                    if($v['node_id'] == $value['node_id']){
                        $list[$key]['select'] = 1;
                    }
                    //2级
                    foreach ($value['subList'] as $key2 => $value2) {
                        if($v['node_id'] == $value2['node_id']){
                            $list[$key]['subList'][$key2]['select'] = 1;
                        }
                        //3级
                        foreach ($value2['subList'] as $key3 => $value3) {
                            if($v['node_id'] == $value3['node_id']){
                                $list[$key]['subList'][$key2]['subList'][$key3]['select'] = 1;
                            }
                        }
                    }
                }
            }
            foreach ($list as $key => $value) {
                //一级
                if(empty($value['select'])){
                    if(!empty($value['subList'])){
                        foreach ($value['subList'] as $key2 => $value2) {
                            //2级
                            if(empty($value2['select'])){
                                if(!empty($value2['subList'])){
                                    foreach ($value2['subList'] as $key3 => $value3) {
                                        //3级
                                        if(empty($value3['select'])){
                                            if(empty($value3['subList'])){
                                                unset($list[$key]['subList'][$key2]['subList'][$key3]);
                                                if(empty($list[$key]['subList'][$key2]['subList'])){
                                                    unset($list[$key]['subList'][$key2]);
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    unset($list[$key]['subList'][$key2]);
                                    if(empty($list[$key]['subList'])){
                                        unset($list[$key]);
                                    }
                                }
                            }
                        }
                    }else{
                        unset($list[$key]);
                    }
                }
            }
            $list = array_values($list);
            foreach ($list as $key => $value) {
                if(!empty($value['subList'])){
                    $list[$key]['subList'] = array_values($list[$key]['subList']);
                    foreach ($value['subList'] as $key2 => $value2) {
                        if(!empty($value2['subList'])){
                            try {
                                $list[$key]['subList'][$key2]['subList'] = array_values($list[$key]['subList'][$key2]['subList']);
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                    }
                }
            }
            return $list;
        }
    }

    /**
     * 根据节点ID 获取用户组的子菜单
     *
     * @param int 节点ID
     * @param int 用户组ID
     * @return void
     */
    public function getSubAdminListByPid($node_id, $group_id = 1)
    {
        if ($group_id == 1) {
            //超级管理员组
            return Node::where([
                    "node_pid"   =>  $node_id,
                    "node_show"   =>  1
                ])
                ->order("node_order desc,node_id asc")
                ->select();
        } else {
            //其他组
            return Node::alias("node")
                ->view('node', '*')
                ->view('auth', '*', 'node.node_id=auth.auth_node', 'left')
                ->where([
                    "node_pid"   =>  $node_id,
                    "node_show"   =>  1,
                    "auth_group"    => $group_id
                ])
                ->order("node_order desc,node_id asc")
                ->select();
        }
    }
    /**
     * 删除授权记录
     *
     * @return void
     */
    public function cleanAuth()
    {
        //清空auth表
        Db::execute("truncate table " . config('database.connections.mysql.prefix') . "auth");
        return true;
    }
}
