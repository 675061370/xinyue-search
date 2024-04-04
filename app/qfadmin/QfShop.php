<?php

declare(strict_types=1);

namespace app\qfadmin;

use think\App;
use think\facade\View;

use app\model\Admin as AdminModel;
use app\model\Access as AccessModel;
use app\model\Auth as AuthModel;
use app\model\Node as NodeModel;
use app\model\Group as GroupModel;
use app\model\Conf as ConfModel;

/**
 * 控制器基础类
 */
abstract class QfShop
{
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

    //模型
    protected $AdminModel;
    protected $accessModel;
    protected $authModel;
    protected $nodeModel;
    protected $groupModel;
    protected $confModel;

    //主键key
    protected $pk = '';
    //表名称
    protected $table = '';
    //主键value
    protected $pk_value = '';
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
        $this->module = "qfadmin";
        $this->controller = $this->request->controller() ? $this->request->controller() : "Index";
        $this->action = strtolower($this->request->action()) ? strtolower($this->request->action()) : "index";
        View::assign('controller', strtolower($this->controller));
        View::assign('action', strtolower($this->action));

        $this->table = strtolower($this->controller);
        $this->pk = $this->table . "_id";
        $this->pk_value = input($this->pk);

        $this->adminModel = new AdminModel();
        $this->accessModel = new AccessModel();
        $this->authModel = new AuthModel();
        $this->nodeModel = new NodeModel();
        $this->groupModel = new GroupModel();
        $this->confModel = new ConfModel();


        $configs = $this->confModel->select()->toArray();
        $c = [];
        foreach ($configs as $config) {
            $c[$config['conf_key']] = $config['conf_value'];
        }
        config($c, 'yadmin');
    }
    /**
     * 后台简单的身份判断
     *
     * @return void
     */
    protected function access()
    {
        $callback = "/qfadmin";
        if (strtolower($this->controller) != "index") {
            $callback .= "/" . strtolower($this->controller);
        }
        if ($this->action != "index") {
            $callback .= "/" . $this->action;
        }
        $access_token = cookie('access_token');
        if (!$access_token) {
            return redirect('/qfadmin/admin/login/?callback=' . urlencode($callback));
        }
        View::assign("access_token", $access_token);
        $this->admin = $this->adminModel->getAdminByAccessToken($access_token);
        if (!$this->admin) {
            return redirect('/qfadmin/admin/login/?callback=' . urlencode($callback));
        }
        if ($this->admin['admin_status']  > 0) {
            return $this->error("抱歉，你的帐号已被禁用，暂时无法登录系统！");
        }
        cookie("access_token", $access_token);
        View::assign('adminInfo', $this->admin);
        $this->group = $this->groupModel->where('group_id', $this->admin['admin_group'])->find();
        if ($this->group) {
            if ($this->group['group_id'] != 1 && $this->group['group_status'] == 1) {
                return $this->error("抱歉，你所在的用户组已被禁用，暂时无法登录系统");
            } else {
                $menuList = $this->authModel->getAdminMenuListByAdminId($this->group['group_id']);
                View::assign('menuList', $menuList);

                $node = $this->nodeModel->where(['node_module' => $this->module, 'node_controller' => strtolower($this->controller), 'node_action' => $this->action])->find();
                View::assign('node', $node);

                if($node['node_pid']==0){
                    View::assign('menu', 0);
                }else{
                    $res = $this->nodeModel->where('node_id',$node['node_pid'])->find();
                    View::assign('menu', $res['node_pid']);
                }
                $menuLists = [];
                foreach ($menuList as $key => $value) {
                    if($value['node_id'] == $node['node_pid']){
                        $menuLists = $value['subList'];
                    }else{
                        foreach ($value['subList'] as $k => $v) {
                            if($v['node_id'] == $node['node_pid']){
                                $menuLists = $value['subList'];
                            }
                        }
                    }
                }
                View::assign('menuLists', $menuLists);
                View::assign('action', $this->request->action());
            }
        } else {
            return $this->error("抱歉，没有查到你的用户组信息，暂时无法登录系统");
        }
    }
    protected function error($message)
    {
        echo $message;
        die;
    }
}
