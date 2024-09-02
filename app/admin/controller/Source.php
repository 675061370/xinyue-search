<?php

namespace app\admin\controller;

use think\App;
use think\facade\Filesystem;
use app\admin\QfShop;
use app\model\Source as SourceModel;
use app\model\SourceLog as SourceLogModel;

class Source extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //第三方转存接口地址
        $this->url = "https://pan.xinyuedh.com";
        //查询列表时允许的字段
        $this->selectList = "*";
        //查询详情时允许的字段
        $this->selectDetail = "*";
        //筛选字段
        $this->searchFilter = [
            "source_id" => "=",
            "title"=>"like",
        ];
        $this->insertFields = [
            //允许添加的字段列表
            "source_category_id","title","url","status","is_delete","sort","is_top"
        ];
        $this->updateFields = [
            //允许更新的字段列表
            "source_category_id","title","url","status","is_delete","sort","is_top"
        ];
        $this->insertRequire = [
            //添加时必须填写的字段
            // "字段名称"=>"该字段不能为空"
            "title"=>"资源名称必须填写",
            "url"=>"资源地址必须填写",
        ];
        $this->updateRequire = [
            //修改时必须填写的字段
            // "字段名称"=>"该字段不能为空"
            "source_id"=>"资源ID必须填写",
            "title"=>"资源名称必须填写",
            "url"=>"资源地址必须填写",
        ];
        $this->model = new SourceModel();
        $this->SourceLogModel = new SourceLogModel();
    }


    /**
     * 获取列表接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function getList()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        //从请求中获取筛选数据的数组
        $map = $this->getDataFilterFromRequest();
        $map[] = ['is_delete','=',0];
        if(!empty(input('source_category_id'))){
            $map[] = ['source_category_id','=',input('source_category_id')];
        }
        //从请求中获取排序方式
        $order = ['is_top' => 'desc','sort' => 'desc','source_id' => 'desc'];
        //设置Model中的 per_page
        $this->setGetListPerPage();
        //查询数据
        $dataList = $this->model->getListByPage($map, $order, $this->selectList);
        return jok('数据获取成功', $dataList);
    }


    /**
     * 添加接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function add()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        //校验Insert字段是否填写
        $error = $this->validateInsertFields();
        if ($error) {
            return $error;
        }
        //从请求中获取Insert数据
        $data = $this->getInsertDataFromRequest();
        //添加这行数据
        $data["update_time"] = time();
        $data["create_time"] = time();
        $this->model->insertGetId($data);
        return jok('添加成功');
    }

    /**
     * 修改接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function update()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        //根据主键获取一行数据
        $item = $this->getRowByPk();
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        //校验Update字段是否填写
        $error  = $this->validateUpdateFields();
        if ($error) {
            return $error;
        }
        //从请求中获取Update数据
        $data = $this->getUpdateDataFromRequest();
        //根据主键更新这条数据
        $data["update_time"] = time();
        $this->model->where($this->pk, $this->pk_value)->update($data);
        return jok('修改成功');
    }



    /**
     * 删除接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
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
        
        //根据主键获取一行数据
        $item = $this->getRowByPk();
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        $this->model->where($this->pk, $this->pk_value)->delete();

        return jok('删除成功');
    }
    
    // 判断文件编码
    function detectFileEncoding($filename) {
        $handle = fopen($filename, 'r');
        $firstLine = fread($handle, 1024); // 读取文件的开头一部分内容
        fclose($handle);
        // 尝试使用不同的编码进行解码，并检查是否成功
        if (mb_check_encoding($firstLine, 'UTF-8')) {
            return 'UTF-8';
        } elseif (mb_check_encoding($firstLine, 'GBK')) {
            return 'GBK';
        } else {
            // 如果无法确定编码，则返回默认编码
            return 'UTF-8'; // 或者根据需要返回其他默认编码
        }
    }
        
    
    /**
     * Excel导入
     *
     * @return void
     */
    public function imports()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        // try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("qfshop.upload_max_file") . '|fileExt:' . config("qfshop.upload_file_type")])
                    ->check(['file' => $file]);
                $saveName = Filesystem::putFile('excel', $file, 'excel.csv');

                ini_set("memory_limit",-1);
                
                $file_name = app()->getRootPath()."public/uploads/".$saveName;

                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                if ($extension == 'csv') {
                    $PHPReader = new \PHPExcel_Reader_CSV();
                    $encoding = $this->detectFileEncoding($file_name);
                    $PHPReader->setInputEncoding($encoding);
                    $PHPReader->setDelimiter(',');
                } elseif ($extension == 'xlsx') {
                    $PHPReader = new \PHPExcel_Reader_Excel2007();
                } elseif ($extension == 'xls') {
                    $PHPReader = new \PHPExcel_Reader_Excel5();
                } else {
                    return jerr('不支持的文件类型');
                }
                
                //载入文件
                $objExcel = $PHPReader->load($file_name);
                $excel_array = $objExcel ->getSheet(0)->toArray();
                array_shift($excel_array);  //删除第一个数组(标题);
                $data = [];
                $i = 0;
                $j = 0;

                 //删除这个文件
                unlink("./uploads/".$saveName);
                
                 // 生成二维码
                foreach ($excel_array as $k => $v) {
                    $patterns = '/^\d+\.|\d+\-/';
                    $title = '';
                    if (!empty($v[2]) && preg_match('/http[^ ]+/', $v[2], $matches)) {
                        $title = preg_replace($patterns, '', $v[1]);
                        $url = $matches[0];
                    } else {
                        if (!empty($v[3]) && preg_match('/http[^ ]+/', $v[3], $matches)) {
                            $title = preg_replace($patterns, '', $v[2]);
                            $url = $matches[0];
                        } else {
                            $url = '';
                        }
                    }
                    $map = [];
                    $map[] = ['title', '=',$title];
                    $res = $this->model->where($map)->find();
                    if (empty($res) && $url) {
                        $data[$k]['title'] = $title;
                        $data[$k]['url'] = $url;
                        $data[$k]['source_category_id'] = input('source_category_id')??0;
                        $data[$k]['update_time'] = time();
                        $data[$k]['create_time'] = time();
                        $i++;
                    }else if($url){
                        $this->model->where($map)->update(['url' => $url, 'update_time' => time()]);
                        $j++;
                    }
                }

                $this->model->insertAll($data);
                if($i == 0 && $j == 0){
                    return jok('无可导入的资源，请检查表格格式');
                }
                return jok('导入成功'.$i.'个资源，更新成功'.$j.'个资源');
                
            } catch (ValidateException $e) {
                return jerr($e->getMessage());
            }
        // } catch (\Exception $error) {
        //     return jerr('上传文件失败，请检查你的文件！');
        // }
    }

    /**
     * 一键转存并分享夸克资源
     *
     * @return void
     */
    public function transfer()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }

        $url = input("url");
        $substring = strstr($url, 's/');
        if ($substring !== false) {
            $pwd_id = substr($substring, 2); // 去除 's/' 部分
        } else {
            return jerr("资源地址格式有误");
        }

        
        $logId = $this->SourceLogModel->addLog('一键转存他人链接',1);

        $urlData =  array(
            'cookie' => Config('qfshop.quark_cookie'),
            'url' => $url,
        );
        $res = curlHelper($this->url."/api/open/transfer", "POST", $urlData)['body'];
        $res = json_decode($res, true);

        if($res['code'] !== 200){
            $this->SourceLogModel->editLog($logId,1,'fail_num',$res['message'],1);
            return jerr($res['message']);
        }
        $patterns = '/^\d+\./';
        $title = preg_replace($patterns, '', $res['data']['title']);
        //添加资源到系统中
        $data["title"] = $title;
        $data["url"] = $res['data']['share_url'];
        $data["update_time"] = time();
        $data["create_time"] = time();
        $this->model->insertGetId($data);
        $this->SourceLogModel->editLog($logId,1,'new_num','',1);

        return jok('已提交任务，稍后查看结果',$data);
    }

    /**
     * 全部转存 
     * 转存心悦搜剧资源
     * @return void
     */
    public function transferAll()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        @set_time_limit(999999);
        //分页转存
        $page_no = 1;
        $dataList = '';
        $logId = '';
        while ($dataList=='' || !empty($dataList['items'])) {
            $searchData =  array(
                'page_no' => $page_no,
                'page_size' => 100,
                'type' => 2,
            );
            $res = curlHelper($this->url."/api/search", "POST", $searchData)['body'];
            $res = json_decode($res, true);

            if($res['code'] !== 200){
                return jerr($res['message']);
            }
            $dataList = $res['data'];
            $page_no++;

            if($logId == ''){
                $logId = $this->SourceLogModel->addLog('全部转存',$dataList['total_result']);
            }

            foreach ($dataList['items'] as $key => $value) {
                //如已有此资源 跳过
                $detail = $this->model->where('title', $value['title'])->find();
                if(!empty($detail)){
                    $this->SourceLogModel->editLog($logId,$dataList['total_result'],'skip_num','重复跳过转存');
                    continue;
                }

                $url = $value['url'];
                $substring = strstr($url, 's/');
    
                if ($substring !== false) {
                    $pwd_id = substr($substring, 2); // 去除 's/' 部分
                } else {
                    $this->SourceLogModel->editLog($logId,$dataList['total_result'],'fail_num','资源地址格式有误');
                    continue;
                }
    
                $urlData =  array(
                    'cookie' => Config('qfshop.quark_cookie'),
                    'url' => $url,
                );
                $res = curlHelper($this->url."/api/open/transfer", "POST", $urlData)['body'];
                $res = json_decode($res, true);
    
                if($res['code'] !== 200){
                    if($res['message'] == 'capacity limit[{0}]'){
                        $this->SourceLogModel->editLog($logId,$dataList['total_result'],'fail_num',$res['message']);
                        break;
                    }else{
                        $this->SourceLogModel->editLog($logId,$dataList['total_result'],'fail_num',$res['message']);
                        continue;
                    }
                }
    
                //添加资源到系统中
                $data["title"] = $value['title'];
                $data["url"] = $res['data']['share_url'];
                $data["update_time"] = time();
                $data["create_time"] = time();
                $this->model->insertGetId($data);
                $this->SourceLogModel->editLog($logId,$dataList['total_result'],'new_num','');
            }
            // 可以添加一个最大重试次数的限制，防止无限循环
            if ($page_no > 1000) {
                break;
            }
        }

        $this->SourceLogModel->editLog($logId,$dataList['total_result'],'','',3);
        return jok('已提交任务，稍后查看结果',$dataList);
    }

    /**
     * 获取夸克网盘文件夹
     *
     * @return void
     */
    public function getFiles()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }

        $urlData =  array(
            'cookie' => Config('qfshop.quark_cookie'), 
        );
        $res = curlHelper($this->url."/api/open/getFiles", "POST", $urlData)['body'];
        $res = json_decode($res, true);

        if($res['code'] !== 200){
            return jerr($res['message']);
        }
        return jok('获取成功',$res['data']);
    }


    /**
     * 导出
     *
     * @return void
     */
    public function excel()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }

        //查询数据
        $map = [];
        $filter = input('');
        foreach ($filter as $k => $v) {
            if ($k == 'filter') {
                $k = input('filter');
                $v = input('keyword');
            }
            if ($v === '' || $v === null) {
                continue;
            }
            if (array_key_exists($k, $this->searchFilter)) {
                switch ($this->searchFilter[$k]) {
                    case "like":
                        array_push($map, [$k, 'like', "%" . $v . "%"]);
                        break;
                    case "=":
                        array_push($map, [$k, '=', $v]);
                        break;
                    default:
                }
            }
        }

        $field = 'title,url';
        $dataList = $this->model->field($field)->where($map)->select();
        $excelField = [
            "title" => "资源名称",
            "url" => "资源地址",
        ];
        $data = $dataList->toArray();

        $this->excelField = $excelField;
        $this->exportExcelData($dataList);
        print_r(12);
    }
    
}
