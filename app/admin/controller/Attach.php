<?php

namespace app\admin\controller;

use think\App;
use think\facade\Filesystem;
use think\exception\ValidateException;
use app\admin\QfShop;
use app\model\Attach as AttachModel;

class Attach extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //筛选字段
        $this->searchFilter = [
            "attach_id" => "=", //相同筛选
            "attach_key" => "like", //相似筛选
            "attach_value" => "like", //相似筛选
            "attach_desc" => "like", //相似筛选
            "attach_readonly" => "=", //相似筛选
        ];
        $this->model = new AttachModel();
    }
    /**
     * 上传图片
     *
     * @return void
     */
    public function uploadImage()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("qfshop.upload_max_image") . '|fileExt:' . config("qfshop.upload_image_type")])
                    ->check(['file' => $file]);
                $saveName = Filesystem::putFile('image', $file);
                $attach_data = array(
                    'attach_path' => "/uploads/".$saveName,
                    'attach_name' => $file->getOriginalName(),
                    'attach_type' => $file->extension(),
                    'attach_size' => $file->getSize(),
                    'attach_admin' => $this->admin['admin_id']
                );
                $attach_id = $this->insertRow($attach_data);
                $attach_data = $this->getRowByPk($attach_id);
                if (input("?extend")) {
                    $attach_data['extend'] = input("extend");
                }
                return jok('上传成功！', $attach_data);
            } catch (ValidateException $e) {
                return jerr($e->getMessage());
            }
        } catch (\Exception $error) {
            return jerr('上传文件失败，请检查你的文件！');
        }
    }
    /**
     * 删除图片
     *
     * @return void
     */
    public function delete()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            //根据主键获取一行数据
            $item = $this->getRowByPk();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            //单个操作
            $map = [$this->pk => $this->pk_value];
            $res = $this->model->where($map)->delete();
            if($res){
                try {
                    unlink("./uploads/".$item['attach_path']);
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        } else {
            //批量操作
            $list = explode(',', $this->pk_value);
            foreach ($list as $key => $value) {
                $item = $this->model->where("attach_id",$value)->find();
                if($item){
                    try {
                        unlink("./uploads/".$item['attach_path']);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
            $this->model->where($this->pk, 'in', $list)->delete();
        }
        return jok('删除成功');
    }
    /**
     * 上传文件
     *
     * @return void
     */
    public function uploadFile()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("qfshop.upload_max_file") . '|fileExt:' . config("qfshop.upload_file_type")])
                    ->check(['file' => $file]);
                $saveName = Filesystem::putFile('normal', $file);
                $attach_data = array(
                    'attach_path' => $saveName,
                    'attach_type' => $file->extension(),
                    'attach_size' => $file->getSize(),
                    'attach_admin' => $this->admin['admin_id']
                );
                $attach_id = $this->insertRow($attach_data);
                $attach_data = $this->getRowByPk($attach_id);
                if (input("?extend")) {
                    $attach_data['extend'] = input("extend");
                }
                return jok('上传成功！', $attach_data);
            } catch (ValidateException $e) {
                return jerr($e);
            }
        } catch (\Exception $error) {
            return jerr('上传文件失败，请检查你的文件！');
        }
    }

    /**
     * 富文本上传图片
     *
     * @return void
     */
    public function uploads()
    {
        header("Content-Type: text/html; charset=utf-8");
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./static/admin/UEditor/config.json")), true);
        $action = $_GET['action'];

        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                try {
                    $file = request()->file('upfile');
                    try {
                        validate(['file' => 'filesize:' . config("qfshop.upload_max_image") . '|fileExt:' . config("qfshop.upload_image_type")])
                            ->check(['file' => $file]);
                        $saveName = Filesystem::putFile('normal', $file);
                        $result = json_encode(array(
                            'original'=> $file->getOriginalName(),
                            'state'=> "SUCCESS",
                            'title'=> $file->getOriginalName(),
                            'url'=> "/uploads/".$saveName,
                            'type'=> $file->extension(),
                        ));
                    } catch (ValidateException $e) {
                        $result = json_encode(array(
                            'state'=> $e->getMessage()
                        ));
                    }
                } catch (\Exception $error) {
                    $result = json_encode(array(
                        'state'=> '上传文件失败，请检查你的文件！'
                    ));
                }
                break;
            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
        die;
    }
}
