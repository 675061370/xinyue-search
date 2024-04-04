<?php

declare(strict_types=1);

namespace app\api;

use think\App;
use think\facade\View;

use app\model\Conf as ConfModel;
use app\model\Token as TokenModel;

/**
 * 控制器基础类
 */
abstract class QfShop
{   
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }
    
    // 初始化
    protected function initialize()
    {
        //访问XMLHttpRequest已被CORS政策阻止
        header('Access-Control-Allow-Origin: *');
        
        $this->confModel = new ConfModel();
        $configs = $this->confModel->select()->toArray();
        $c = [];
        foreach ($configs as $config) {
            $c[$config['conf_key']] = $config['conf_value'];
        }
        config($c, 'qfshop');
    }
    public function __call($method, $args)
    {
        return jerr("API接口方法不存在", 404);
    }

    
    /**
     * 检测授权 获取当前用户登录信息 
     * @param $type false不提示登录失败状态
     * @param user_id 顾客时为用户id
     * @param action 操作者 顾客端为手机号，管理端为登录账号
     * @param client_type 0=顾客 1=管理组 -1=游客
     */
    protected function getLoginUser($type = true)
    {
        $is_login = true;
        // 获取请求中的token
        $access_token = request()->header('X-CSRF-TOKEN');
        if (!$access_token) {
            if($type){
                return jerr("用户未登录", 401);
            }else{
                $is_login = false;
            }
        }
        $Token = new TokenModel();
        $user = $Token->getToken($access_token);
        if (!$user) {
            if($type){
                return jerr("登录过期，请重新登录", 401);
            }else{
                $is_login = false;
            }
        }
        if($is_login){
            $user['action'] = $user['mobile'];
            $user['client_type'] = 0;
            unset($user['mobile']);
            unset($user['status']);
            return $user;
        }else{
            $user['client_type'] = -1;
            return $user;
        }
    }

}
