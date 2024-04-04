<?php

declare(strict_types=1);

namespace app\index;

use think\App;
use EasyWeChat\Factory;
use app\model\Conf as ConfModel;
use app\model\User as UserModel;

/**
 * 控制器基础类
 */
abstract class QfShop
{
    protected $confModel;
    protected $UserModel;
    protected $access_token;
    protected $wechat_appid;
    protected $wechat_appkey;

    protected $easyWeChat;
    //微信用户数据数组
    protected $user;
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    protected $module;
    protected $controller;
    protected $action;

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
        // $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        $this->module = "index";
        $this->controller = "index";
        $this->action = strtolower($this->request->action()) ? strtolower($this->request->action()) : "index";
        $this->confModel = new ConfModel();
        $this->UserModel = new UserModel();

        $configs = $this->confModel->select()->toArray();
        $c = [];
        foreach ($configs as $config) {
            $c[$config['conf_key']] = $config['conf_value'];
        }
        config($c, 'qfshop');
        $this->initWechatConfig();
    }
    /**
     * 微信服务登录 $this->user将为用户数据
     *
     * @param  mixed $openid
     * @return void
     */
    protected function updateWechatUserInfo($openid)
    {
        $user = $this->easyWeChat->user->get($openid);
        if (array_key_exists("errcode", $user)) {
            return false;
        } else {
            $nickname = $user['nickname']??"";
            $sex = $user['sex']??"";
            $headimgurl = empty($user['headimgurl'])?'' : str_replace("http://", 'https://', $user['headimgurl']);
            $this->user = $this->UserModel->where('openid', $openid)->find();
            if (!$this->user) {
                //注册
                $data = ["openid" => $openid, "nickname" => $nickname, "head_pic" => $headimgurl, "sex" => $sex, "create_time" => time(), "update_time" => time()];
                $this->UserModel->insert($data);
            } else {
                //更新
                $this->UserModel->where('openid', $openid)->update(["nickname" => $nickname, "head_pic" => $headimgurl, "sex" => $sex,"update_time" => time()]);
            }
            $this->user = $this->UserModel->where('openid', $openid)->find();
            return $this->user;
        }
    }
    protected function initWechatConfig()
    {
        $this->wechat_appid = config("qfshop.mp_appid");
        $this->wechat_appkey = config("qfshop.mp_appsecret");
        if (!$this->wechat_appid || !$this->wechat_appkey) {
            die('Input wechat appid and appkey first!');
        }
        $this->wechat_config = [
            'app_id' => $this->wechat_appid,
            'secret' => $this->wechat_appkey,
            'token' => 'qfshop',
            'aes_key' => 'qfshop',
            //必须添加部分
            'http' => [ // 配置
                'verify' => false,
                'timeout' => 4.0,
            ],
        ];
        $this->easyWeChat = Factory::officialAccount($this->wechat_config);
        $user_id = cookie('user_id');
        $user_ticket = cookie('user_ticket');
        if ($user_ticket == getTicket($user_id)) {
            $this->user = $this->UserModel->where('user_id', $user_id)->find();
            if ($this->user) {
                $this->user = $this->user->toArray();
            }
        }
    }
    /**
     * 调用微信授权
     *
     * @return void
     */
    protected function authorize()
    {
        if ($this->user) {
            return null;
        }
        //生成授权所需要的回调地址 并重定向到Authorize控制器进行微信授权
        $callback = '/';
        if ($this->module != "index") {
            $callback .= strtolower($this->module) . '/';
        } else {
            if ($this->controller != "Index" && $this->action != "index") {
                $callback .= strtolower($this->module) . '/';
            }
        }
        if ($this->controller != "Index") {
            $callback .= strtolower($this->controller) . '/';
        } else {
            if ($this->action != "index") {
                $callback .= strtolower($this->controller) . '/';
            }
        }
        if ($this->action != "index") {
            $callback .= strtolower($this->action) . '/';
        }
        $i = 0;
        foreach (input('get.') as $k => $v) {
            if ($i == 0) {
                $callback .= "?";
            } else {
                $callback .= "&";
            }
            if (!in_array($k, ['code', 'state', 'from', 'isappinstalled'])) {
                $callback .= $k . "=" . $v;
            }
            $i++;
        }
        return redirect('/index/authorize?callback=' . urlencode($callback));
    }
}
