<?php

namespace app\model;

use app\model\QfShop;

class Admin extends QfShop
{
    /**
     * 用户登录
     *
     * @param string 帐号
     * @param string 密码
     * @return void
     */
    public function login($admin_account, $admin_password)
    {
        $admin = $this->where([
            "admin_account" => $admin_account,
        ])->find();
        if ($admin) {
            //判断密码是否正确
            $salt = $admin['admin_salt'];
            $password = $admin['admin_password'];
            if ($password != encodePassword($admin_password, $salt)) {
                return false;
            }
            return $admin->toArray() ?? false;
        } else {
            return false;
        }
    }

    public function getListByPage($maps, $order = null, $field = "*")
    {
        $resource = $this->view('admin', $field)->view('group', '*', 'group.group_id = admin.admin_group', 'left');
        foreach ($maps as $map) {
            switch (count($map)) {
                case 1:
                    $resource = $resource->where($map[0]);
                    break;
                case 2:
                    $resource = $resource->where($map[0], $map[1]);
                    break;
                case 3:
                    $resource = $resource->where($map[0], $map[1], $map[2]);
                    break;
                default:
            }
        }
        if ($order) {
            $resource = $resource->order($order);
        }
        return $resource->paginate($this->per_page);
    }

    /**
     * 重置密码
     *
     * @param string UID
     * @param string 密码
     * @return void
     */
    public function motifyPassword($admin_id, $password)
    {
        $access = new Access();
        //将所有授权记录标记为失效
        $access->where('access_admin', $admin_id)->update(['access_status' => 1]);
        $salt = getRandString(4);
        $password = encodePassword($password, $salt);
        return $this->where([
            "admin_id" => $admin_id
        ])->update([
            "admin_password" => $password,
            "admin_salt" => $salt,
        ]);
    }

    /**
     * 通过帐号获取用户信息
     *
     * @param  string 帐号/手机号
     * @return void
     */
    public function getAdminByAccount($admin_account)
    {
        $admin = $this->where([
            "admin_account" => $admin_account
        ])->find();
        if ($admin) {
            return $admin->toArray() ?? false;
        } else {
            return false;
        }
    }
    /**
     * AccessToken获取用户信息
     *
     * @param string access_token
     * @return void
     */
    public function getAdminByAccessToken($access_token)
    {
        $Access = new Access();
        $access = $Access->where([
            "access_token" => $access_token,
            "access_status" => 0,
        ])->find();
        if ($access) {
            if (time() > $access['access_updatetime'] + 7200) {
                return false;
            }
            if ($access['access_updatetime'] - $access['access_createtime'] > 86400) {
                return false;
            }
            $Access->where([
                "access_id" => $access['access_id'],
            ])->update([
                'access_updatetime' => time()
            ]);
            $this->where("admin_id", $access['access_admin'])->update([
                'admin_updatetime' => time()
            ]);
            $admin = $this->where("admin_id", $access['access_admin'])->find();
            return $admin->toArray() ?? false;
        } else {
            return false;
        }
    }
}
