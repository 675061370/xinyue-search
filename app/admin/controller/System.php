<?php

namespace app\admin\controller;

use think\App;
use think\facade\Db;
use think\facade\Cache;
use app\admin\QfShop;
use app\model\Validate as ValidateModel;

class System extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }
    /**
     * 获取图形验证码
     *
     * @return void
     */
    public function getCaptcha()
    {
        // $error = $this->access();
        // if ($error) {
        //     return $error;
        // }
        $validateModel = new ValidateModel();
        $imgData = $validateModel->getImg();
        $code = strtoupper($validateModel->getCode());
        $token = sha1($code .  time()) . rand(100000, 999999);
        cache($token, $code, 60);
        return jok('验证码生成成功', [
            'img' => $imgData,
            'token' => $token
        ]);
    }
    /**
     * 清除缓存
     *
     * @return void
     */
    public function clean()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        Cache::clear();
        
        if($this->del_dir("../runtime/")){
            return jok('缓存已清空');
        }else{
            return jerr('缓存清除失败');
        }
    }

    function del_dir($dir) {
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    @unlink($fullpath);
                } else {
                    $this->del_dir($fullpath);
                }
            }
        }
        closedir($dh);
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}
