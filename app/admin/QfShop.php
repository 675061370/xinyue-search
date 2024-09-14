<?php

declare(strict_types=1);

namespace app\admin;

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
    protected $model = null;
    //搜索字段
    protected $selectList = '*';
    protected $selectDetail = '*';
    //筛选字段
    protected $searchFilter = [];
    //更新字段
    protected $updateFields = [];
    //更新时的必须字段
    protected $updateRequire = [];
    //添加字段
    protected $insertFields = [];
    //添加时的必须字段
    protected $insertRequire = [];
    //excel查询字段 用来查询
    protected $excelField = [
        "join_id" => "编号",
    ];
    //excel 表头
    protected $excelTitle = "数据导出表";
    //EXCEL 单元格字母
    protected $excelCells = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD'];

    //主键key
    protected $pk = '';
    //表名称
    protected $table = '';
    //主键value
    protected $pk_value = 0;


    //模型
    protected $adminModel;
    protected $accessModel;
    protected $authModel;
    protected $nodeModel;
    protected $groupModel;
    protected $confModel;

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
    protected $plat = 'all';
    protected $version = 0;


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
        $this->initialize();
    }

    //TODO !!!如有特殊需求请重写下面的方法到子类，请勿修改此处!!! BEGIN.........//
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
        $this->insertRow($data);
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
        $this->updateByPk($data);
        return jok('修改成功');
    }
    /**
     * 禁用接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function disable()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            //根据主键获取一行数据
            $item = $this->getRowByPk();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            //单个操作
            $this->disableBySingle();
        } else {
            //批量操作
            $this->disableByMultiple();
        }
        return jok("禁用成功");
    }
    /**
     * 启用接口基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function enable()
    {
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            //根据主键获取一行数据
            $item = $this->getRowByPk();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            //单个操作
            $this->enableBySingle();
        } else {
            //批量操作
            $this->enableByMultiple();
        }
        return jok("启用成功");
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
        if (isInteger($this->pk_value)) {
            //根据主键获取一行数据
            $item = $this->getRowByPk();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            //单个操作
            $this->deleteBySingle();
        } else {
            //批量操作
            $this->deleteByMultiple();
        }
        return jok('删除成功');
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
        //从请求中获取排序方式
        $order = $this->getorderfromRequest();
        //设置Model中的 per_page
        $this->setGetListPerPage();
        //查询数据
        $dataList = $this->model->getListByPage($map, $order, $this->selectList);
        return jok('数据获取成功', $dataList);
    }
    /**
     * 获取详情基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function detail()
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
            return jerr("没有查询到数据", 404);
        }
        return jok('数据加载成功', $item);
    }
    /**
     * 导出Excel基类 子类自动继承 如有特殊需求 可重写到子类 请勿修改父类方法
     *
     * @return void
     */
    public function excel()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $this->exportExcelData();
    }
    // !!!如有特殊需求请重写下面的方法到子类，请勿修改此处!!! END.........//

    // 初始化
    protected function initialize()
    {
        $this->module = "api";
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
        config($c, 'qfshop');
    }
    /**
     * 检测授权
     *
     * @return void
     */
    protected function access()
    {
        if (!input("plat")) {
            return jerr("plat参数为必须", 400);
        }
        $this->plat = input('plat');
        if (!input("version")) {
            return jerr("version参数为必须", 400);
        }
        $this->version = input('version');
        
        if (!input("access_token")) {
            return jerr("AccessToken为必要参数", 400);
        }
        $access_token = input("access_token");
        $this->admin = $this->adminModel->getAdminByAccessToken($access_token);
        if (!$this->admin) {
            return jerr("登录过期，请重新登录", 401);
        }
        if ($this->admin['admin_status'] == 1) {
            return jerr("你的账户被禁用，登录失败", 401);
        }
    }
    /**
     * 从请求中获取Request数据
     *
     * @return void
     */
    protected function getInsertDataFromRequest()
    {
        $data = [];
        foreach (input('post.') as $k => $v) {
            if (in_array($k, $this->insertFields)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }
    /**
     * 校验Insert的字段
     *
     * @return void
     */
    protected function validateInsertFields()
    {
        foreach ($this->insertRequire as $k => $v) {
            if (!input($k)) {
                return jerr($v, 400);
            }
        }
        return null;
    }
    /**
     * 从请求中获取Update数据
     *
     * @return void
     */
    protected function getUpdateDataFromRequest()
    {
        $data = [];
        foreach (input('post.') as $k => $v) {
            if (in_array($k, $this->updateFields)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }
    /**
     * 校验Update的字段
     *
     * @return void
     */
    protected function validateUpdateFields()
    {
        foreach ($this->updateRequire as $k => $v) {
            if (!input($k)) {
                return jerr($v, 400);
            }
        }
        return null;
    }
    /**
     * 按主键集合批量禁用 1,2,3,4
     *
     * @return void
     */
    protected function disableByMultiple()
    {
        $list = explode(',', $this->pk_value);
        $this->model->where($this->pk, 'in', $list)->update([
            $this->table . "_status" => 1,
            $this->table . "_updatetime" => time(),
        ]);
    }
    /**
     * 单个禁用 可传入自定义$map
     * 默认按主键ID禁用
     *
     * @param  array $map
     * @return void
     */
    protected function disableBySingle($map = null)
    {
        if ($map == null) {
            $map = [$this->pk => $this->pk_value];
        }
        $this->model->where($map)->update([
            $this->table . "_status" => 1,
            $this->table . "_updatetime" => time(),
        ]);
    }
    /**
     * 按主键集合批量启用 1,2,3,4
     *
     * @return void
     */
    protected function enableByMultiple()
    {
        $list = explode(',', $this->pk_value);
        $this->model->where($this->pk, 'in', $list)->update([
            $this->table . "_status" => 0,
            $this->table . "_updatetime" => time(),
        ]);
    }
    /**
     * 单个启用 可传入自定义$map
     * 默认按主键ID启用
     *
     * @param  array $map
     * @return void
     */
    protected function enableBySingle($map = null)
    {
        if ($map == null) {
            $map = [$this->pk => $this->pk_value];
        }
        $this->model->where($map)->update([
            $this->table . "_status" => 0,
            $this->table . "_updatetime" => time(),
        ]);
    }
    /**
     * 按主键集合批量删除1,2,3,4
     *
     * @return void
     */
    protected function deleteByMultiple()
    {
        $list = explode(',', $this->pk_value);
        $this->model->where($this->pk, 'in', $list)->delete();
    }
    /**
     * 单个删除 默认主键ID
     *
     * @param  array $map
     * @return void
     */
    protected function deleteBySingle($map = null)
    {
        if ($map == null) {
            $map = [$this->pk => $this->pk_value];
        }
        $this->model->where($map)->delete();
    }
    /**
     * 根据主键ID获取一行数据
     *
     * @param  int|null 主键ID
     * @return array|null
     */
    protected function getRowByPk($pk_value = null)
    {
        if (!$pk_value) {
            $pk_value = $this->pk_value;
        }
        $item  = $this->model->where($this->pk, $pk_value)->field($this->selectDetail)->find();
        return $item ? $item->toArray() : null;
    }
    /**
     * 根据主键ID更新数据
     *
     * @param  array 需要更新的KV数组
     * @param  int|null 主键ID 默认$this->pk_value
     * @param  bool 是否更新_updatetime字段 默认TRUE
     * @return void
     */
    protected function updateByPk($data, $pk_value = null, $auto_updatetime = true)
    {
        if (!$pk_value) {
            $pk_value = $this->pk_value;
        }
        if ($auto_updatetime) {
            $data[$this->table . "_updatetime"] = time();
        }
        $this->model->where($this->pk, $this->pk_value)->update($data);
    }
    /**
     * 添加一行数据
     *
     * @param  array 需要添加的KV数组
     * @param  bool 是否自动记录_createtime和_updatetime字段 默认true
     * @return int 添加返回的主键ID
     */
    protected function insertRow($data, $auto_inserttime = true)
    {
        if ($auto_inserttime) {
            $data[$this->table . "_updatetime"] = time();
            $data[$this->table . "_createtime"] = time();
        } else {
            $data[$this->table . "_updatetime"] = 0;
            $data[$this->table . "_createtime"] = 0;
        }
        $id = $this->model->insertGetId($data);
        return $id;
    }
    /**
     * 从请求中获取查询排序(默认主键DESC)
     *
     * @return void
     */
    protected function getOrderFromRequest()
    {
        if (input('order')) {
            $order = urldecode(input('order'));
        } else {
            $order = strtolower($this->controller) . "_id desc";
        }
        return $order;
    }
    /**
     * 设置分页查询每页数量
     *
     * @param  int|null 每页数量
     * @return void
     */
    protected function setGetListPerPage($per_page = null)
    {
        if ($per_page) {
            $this->model->per_page = intval($per_page);
        } else if (input('per_page')) {
            $this->model->per_page = input('per_page');
        } else {
            $this->model->per_page = 10;
        }
    }
    /**
     * 获取查询列表的Where参数
     *
     * @return array
     */
    protected function getDataFilterFromRequest()
    {
        $map = [];
        $filter = input('post.');
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
        return $map;
    }
    protected function getExcelFields()
    {
        $excelField = [];
        foreach ($this->excelField as $k => $v) {
            if ($k == "*") {
                continue;
            } else {
                array_push($excelField, [
                    $k, $v
                ]);
            }
        }
        return $excelField;
    }
    /**
     * 导出Excel
     *
     * @return string 下载文件名
     */
    protected function exportExcelData($data)
    {
        $datalist = $data ? $data->toArray() : [];
        $excelField = $this->getExcelFields();
        $PHPExcel = new \PHPExcel(); //实例化
        $PHPExcel
            ->getProperties()  //获得文件属性对象，给下文提供设置资源  
            ->setCreator("qfshop")                 //设置文件的创建者  
            ->setLastModifiedBy("qfshop")          //设置最后修改者  
            ->setDescription("Export by qfshop"); //设置备注  

        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle($this->excelTitle); //给当前活动sheet设置名称

        $PHPSheet->mergeCells('A1:' . $this->excelCells[count($excelField) - 1] . "1");
        $PHPSheet->setCellValue('A1', $this->excelTitle);
        $PHPSheet->getRowDimension(1)->setRowHeight(40);
        $PHPSheet->getStyle('A1')->getFont()->setSize(16)->setBold(true); //字体大小

        $PHPSheet->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    //水平方向上对齐  
        $PHPSheet->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);       //垂直方向上中间居中  
        $PHPSheet->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);       //垂直方向上中间居中  

        if (count($excelField) > count($this->excelCells)) {
            echo 'Error and you need check Excel Cells Keys...';
            die;
        }
        $PHPSheet->getRowDimension(2)->setRowHeight(30);
        for ($column = 0; $column < count($excelField); $column++) {
            $PHPSheet->setCellValue($this->excelCells[$column] . "2", $excelField[$column][1]);
            $PHPSheet->getStyle($this->excelCells[$column])->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            for ($line = 0; $line < count($datalist); $line++) {
                $string = $datalist[$line][$excelField[$column][0]];
                $PHPSheet->getColumnDimension($this->excelCells[$column])->setWidth(20);
                $PHPSheet->setCellValueExplicit($this->excelCells[$column] . ($line + 3), $string, \PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        //***********************画出单元格边框*****************************
        $styleArray = array(
            'borders' => array(
                'inside' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN, //细边框
                    //'color' => array('argb' => 'FFFF0000'),
                ),
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK, //边框是粗的
                    //'color' => array('argb' => 'FFFF0000'),
                ),
            ),
        );
        $PHPSheet->getStyle('A2:' . $this->excelCells[count($excelField) - 1] . (count(
            $datalist
        ) + 2))->applyFromArray($styleArray);
        //***********************画出单元格边框结束*****************************

        //设置全部居中对齐
        $PHPSheet->getStyle('A1:' . $this->excelCells[count($excelField) - 1] . (count(
            $datalist
        ) + 2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //设置全部字体
        $PHPSheet->getStyle('A1:' . $this->excelCells[count($excelField) - 1] . (count(
            $datalist
        ) + 2))->getFont()->setName('微软雅黑');
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header('Content-Disposition: attachment;filename="' . $this->excelTitle . "_" . date('Y-m-d_H:i:s') . '.xlsx"'); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
        exit;
        return $this->excelTitle . "_" . date('Y-m-d_H:i:s') . '.xlsx"';
    }
    public function __call($method, $args)
    {
        return jerr("访问异常", 404);
    }
}
