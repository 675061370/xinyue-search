<?php

namespace app\model;

use app\model\QfShop;

class Access extends QfShop
{
    /**
     * 创建一个新的授权
     *
     * @param [int] AdminID
     * @param [plat] 授权平台
     * @return 授权信息|false
     */
    public function createAccess($access_admin, $access_plat)
    {
        //将该平台下所有授权记录标记为失效
        // $this->where([
        //     "access_admin" => $access_admin,
        //     "access_plat" => $access_plat
        // ])->update(['access_status' => 1]);
        //生成一个新的Access_token
        $access_token = sha1(time()) . rand(100000, 99999) . sha1(time());
        $access_id = $this->insertGetId([
            "access_admin" => $access_admin,
            "access_plat" => $access_plat,
            "access_token" => $access_token,
            "access_ip" => request()->ip(),
            "access_createtime" => time(),
            "access_updatetime" => time()
        ]);
        $access = $this->where("access_id", $access_id)->find();
        return $access ?? false;
    }
}
