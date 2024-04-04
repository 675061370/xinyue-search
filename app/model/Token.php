<?php

namespace app\model;

use app\model\QfShop;

class Token extends QfShop
{
    /**
     * 创建更新一个新的授权
     *
     * @param [int] 用户ID
     * @param [plat] 授权平台
     * @return 授权信息|false
     */
    public function createAccess($user_id, $platform)
    {
        $token = $this->where([
            "user_id" => $user_id,
            "platform" => $platform
        ])->find();
        //生成一个新的Access_token
        $access_token = sha1(time()) . rand(100000, 99999) . sha1(time());
        $expires = time() + (30 * 24 * 60 * 60); // 30天
        if($token){
            //如果已有token 执行更新
            $token_id = $token['token_id'];
            $this->where('token_id',$token_id)->update([
                "token"         => $access_token,
                "ip"            => request()->ip(),
                "token_expires" => $expires,
                "create_time" => time(),
            ]);
        }else{
            //如果没有token 执行新增
            $token_id = $this->insertGetId([
                "user_id"       => $user_id,
                "platform"      => $platform,
                "token"         => $access_token,
                "ip"            => request()->ip(),
                "token_expires" => $expires,
                "create_time" => time(),
            ]);
        }
        $access = $this->where("token_id", $token_id)->find();
        return $access ?? false;
    }

    /**
     * 授权校验
     *
     * @param string token
     * @return void
     * 校验成功 更新授权码过期时间
     */
    public function getToken($access_token)
    {
        $token = $this->where([
            "token" => $access_token
        ])->find();
        if ($token) {
            if (time() > $token['token_expires']) {
                return false;
            }
            $expires = time() + (30 * 24 * 60 * 60); // 30天
            $this->where([
                "token_id" => $token['token_id'],
            ])->update([
                'token_expires' => $expires
            ]);
            $user = User::field("user_id,status,mobile")->where(["user_id" => $token['user_id'],"is_delete" => 0])->find();
            if(!$user){
                return jerr("账号不存在");
            }
            if ($user['status'] == 0) {
                return jerr("你的账户被禁用");
            }
            return $user;
        } else {
            return false;
        }
    }
}
