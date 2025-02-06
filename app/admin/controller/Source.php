<?php

namespace app\admin\controller;

use think\App;
use think\facade\Filesystem;
use app\admin\QfShop;
use app\model\Source as SourceModel;
use app\model\SourceLog as SourceLogModel;
use quarkPlugin\QuarkPlugin;

class Source extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //查询列表时允许的字段
        $this->selectList = "*";
        //查询详情时允许的字段
        $this->selectDetail = "*";
        //筛选字段
        $this->searchFilter = [];
        $this->insertFields = [
            //允许添加的字段列表
            "source_category_id","title","description","url","status","is_delete","sort","is_top","vod_content","is_type"
        ];
        $this->updateFields = [
            //允许更新的字段列表
            "source_category_id","title","description","url","status","is_delete","sort","is_top","vod_content","is_type"
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
        empty(input('keyword')) ?: $map[] = ['title|description', 'like', '%' . input('keyword') . '%'];
        //从请求中获取排序方式
        $order = $this->getorderfromRequest();
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
        $data["is_type"] = determineIsType($data["url"]);
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
        $data["is_type"] = determineIsType($data["url"]);
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

        if (isInteger($this->pk_value)){
            //根据主键获取一行数据
            $item = $this->getRowByPk();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            $this->model->where($this->pk, $this->pk_value)->delete();
        }else{
            $list = explode(',', $this->pk_value);
            $this->model->where($this->pk, 'in', $list)->delete();
        }
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
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("qfshop.upload_max_file") . '|fileExt:' . config("qfshop.upload_file_type")])
                    ->check(['file' => $file]);
                $saveName = Filesystem::putFile('excel', $file, 'excel.csv');

                ini_set("memory_limit",-1);
                
                $file_name = app()->getRootPath()."public/uploads/".$saveName;
                
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                if ($extension == 'csv') {
                    return jerr('转成xlsx格式吧');
                    $PHPReader = new \PHPExcel_Reader_CSV();
                    $encoding = $this->detectFileEncoding($file_name);
                    // if (!$encoding || strtoupper($encoding) == 'UTF-8') {
                    //     $encoding = 'GBK'; // 尝试将其强制转为GBK
                    // }
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
                $existing_data = [];

                // 先查询数据库中所有已存在的 title 和 is_type 组合
                $existing_records = $this->model->field('title, is_type')->select()->toArray();
                // 将查询结果转换为关联数组用于快速查找
                foreach ($existing_records as $record) {
                    $existing_data[$record['title'] . '_' . $record['is_type']] = true;
                }

                 //删除这个文件
                unlink("./uploads/".$saveName);

                foreach ($excel_array as $k => $v) {
                    $patterns = '/^\d+\.|\d+\-/';
                    $title = '';
                    $url = '';

                    for ($index = 1; $index <= 3; $index++) {
                        // 检查 $v[$index] 是否存在
                        if (isset($v[$index]) && preg_match('/http[^ ]+/', $v[$index], $matches)) {
                            // 检查 $v[$index - 1] 是否存在
                            if (isset($v[$index - 1])) {
                                $title = preg_replace($patterns, '', $v[$index - 1]);
                            }
                            $url = $matches[0];
                            break;
                        }
                    }
                    
                    $is_type = $url?determineIsType($url):0;

                    $key = $title . '_' . $is_type;

                    // 先检查内存缓存的 existing_data，避免重复插入
                    if (!isset($existing_data[$key]) && $url) {
                        $data[$k]['title'] = $title;
                        $data[$k]['url'] = $url;
                        $data[$k]["is_type"] = $is_type;
                        $data[$k]['source_category_id'] = input('source_category_id')??0;
                        $data[$k]['update_time'] = time();
                        $data[$k]['create_time'] = time();

                        // 将新插入的记录加入缓存，防止后续重复处理
                        $existing_data[$key] = true;
                        $i++;
                    }
                }
                

                $this->model->insertAll($data);
                if($i == 0){
                    return jok('无可导入的资源，请检查表格格式');
                }
                return jok('导入成功'.$i.'个资源');
                
            } catch (ValidateException $e) {
                return jerr($e->getMessage());
            }
        } catch (\Exception $error) {
            return jerr('上传文件失败，请检查你的文件！');
        }
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
        if(empty(input("type")) || empty(input("urls"))){
            return jerr('参数不能为空');
        }
        
        $source_category_id = input('source_category_id')??0;
        
        $urls = input("urls");

        $urls = explode("\n", $urls);

        // 去掉数组元素中的空白字符
        $urls = array_map('trim', $urls);

        // 过滤掉空值的数组元素
        $urls = array_filter($urls);
        
        $allData = array_values(array_filter(array_map(function ($item) {
            // 提取 URL
            if (!preg_match('/https?:\/\/[^\s]+/', $item, $matches)) {
                return null; // 没有匹配到 URL，直接丢弃
            }
        
            $url = trim($matches[0]);
            $code = '';
        
            // 提取提取码（?pwd= 或 , 分割）
            if (preg_match('/\?pwd=([^,\s]+)/', $item, $pwdMatch)) {
                $code = trim($pwdMatch[1]);
            } elseif (preg_match('/,(.+)$/', $item, $commaMatch)) {
                $code = trim($commaMatch[1]);
            }
        
            // 返回结果时，确保 title 保持为空字符串
            return [
                'url' => $url,
                'title' => '',
                'code' => $code
            ];
        }, $urls)));
        
        
        // 去重，使用 'url' 字段来去重
        $uniqueUrls = [];
        $allData = array_filter($allData, function($item) use (&$uniqueUrls) {
            if (!in_array($item['url'], $uniqueUrls)) {
                $uniqueUrls[] = $item['url'];  // 添加到已处理的 URL 列表
                return true;  // 保留此项目
            }
            return false;  // 去掉重复的项目
        });
        
        $quarkPlugin = new QuarkPlugin();
        if(input("type")==2){
            //转存分享导入
            $res = $quarkPlugin->transfer($allData,$source_category_id);
        }else{
            // 直接导入
            $res = $quarkPlugin->import($allData,$source_category_id);
        }
        
        return jok('已提交任务，稍后查看结果2',$res);
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
        if(empty(input('source_category_id'))){
            return jerr('参数异常');
        }
        $quarkPlugin = new QuarkPlugin();
        $quarkPlugin->transferAll(input('source_category_id'));
        return jok('已提交任务，稍后查看结果');
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

        $quarkPlugin = new QuarkPlugin();
        $result = $quarkPlugin->getFiles(Config('qfshop.quark_cookie'));
        return jok('获取成功',$result);
    }
    
}
