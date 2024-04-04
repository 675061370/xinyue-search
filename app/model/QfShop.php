<?php

namespace app\model;

use think\Model;
use think\helper\Str;
use app\model\Token as TokenModel;

/**
 * QfShop 数据模型基类
 */
class QfShop extends Model
{
    /**
     * 默认分页获取条数
     *
     * @var int 默认分页获取条数
     */
    public $page_size = 40;
    public $per_page = 10;

    /**
     * 翻页搜索器
     * @access public
     * @param object $query
     * @param mixed  $value
     * @param mixed  $data
     */
    public function searchPageAttr($query, $value, $data)
    {
        $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;
        $pageSize = isset($data['page_size']) ? $data['page_size'] : $this->page_size;
        $query->page($pageNo, $pageSize);
    }
    
    /**
     * 排序搜索器
     * @access public
     * @param object $query
     * @param mixed  $value
     * @param mixed  $data
     */
    public function searchOrderAttr($query, $value, $data)
    {
        $order = [];
        if (!empty($data['order_field']) || !empty($data['order_type'])) {
            $order[$data['order_field']] = $data['order_type'];
        } else {
            $order = $this->defaultOrder;
        }
        if (!empty($this->fixedOrder)) {
            // 固定排序必须在前,否则将导致自定义排序无法覆盖
            $order = array_merge($this->fixedOrder, $order);
            if (!empty($data['order_field']) && $this->isReverse) {
                $order = array_reverse($order);
            }
        }
        if (!empty($order)) {
            $query->order($order);
        }
    }

    /**
     * 设置默认排序
     * @access public
     * @param array $order   默认排序
     * @param array $fixed   固定排序
     * @param bool  $reverse 是否调整顺序
     * @return $this
     */
    public function setDefaultOrder(array $order, $fixed = [], $reverse = false)
    {
        $this->defaultOrder = $order;
        $this->fixedOrder = $fixed;
        $this->isReverse = $reverse;
        return $this;
    }

    /**
     * 模型验证器
     * @access public
     * @param array|object $data     验证数据
     * @param string|null  $scene    场景名
     * @param bool         $clean    是否清理规则键值不存在的$data
     * @param string       $validate 验证器规则或类
     * @return bool
     */
    public function validateData(array &$data, $scene = null, $clean = false, $validate = '')
    {
        try {
            // 确定规则来源
            if (empty($validate)) {
                $class = '\\app\\validate\\' . $this->getName();
                if ($scene) {
                    $v = new $class();
                    $v->extractScene($data, $scene, $clean, $this->getPk());
                } else {
                    $v = validate($class);
                }
            } else {
                $v = validate($validate);
                if ($scene) {
                    $v->extractScene($data, $scene, $clean, $this->getPk());
                }
            }

            if ($clean) {
                $keys = $v->getRuleKey();
                foreach ($data as $key => $value) {
                    if (!in_array($key, $keys, true)) {
                        unset($data[$key]);
                    }
                }

                unset($key, $value);
            }

            $v->failException(true)->check($data);
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        return true;
    }

    /**
     * 检测是否存在相同值
     * @access public
     * @param array $map 查询条件
     * @return bool false:不存在
     */
    public static function checkUnique(array $map)
    {
        if (empty($map)) {
            return true;
        }

        $count = self::where($map)->count();
        if (is_numeric($count) && $count <= 0) {
            return false;
        }

        return true;
    }

    /**
     * 后台使用分页获取数据
     *
     * @param  array 筛选数组
     * @param  string 排序方式
     * @param  string 搜索字段
     * @return void
     */
    public function getListByPage($maps, $order = null, $field = "*")
    {
        $resource = $this->field($field);
        foreach ($maps as $map) {
            switch (count($map)) {
                case 1:
                    $resource = $resource->where($map[0]);
                    break;
                case 2:
                    $resource = $resource->where($map[0], $map[1]);
                    break;
                case 3:
                    $resource = $resource->where($map[0], $map[1], $map[2]);
                    break;
                default:
            }
        }
        if ($order) {
            $resource = $resource->order($order);
        }
        return $resource->paginate($this->per_page);
    }


    /**
     * 替换数组中的驼峰键名为下划线
     * @access public
     * @param array  $name 需要修改的键名
     * @param array &$data 源数据
     */
    public static function keyToSnake(array $name, array &$data)
    {
        if (!is_array($name)) {
            return;
        }

        foreach ($name as $value) {
            foreach ($data as &$item) {
                if (!array_key_exists($value, $item)) {
                    continue;
                }

                $temp = $item[$value];
                unset($item[$value]);

                $item[Str::snake($value)] = $temp;
            }
        }
    }

    /**
     * 将数组键名驼峰转下划线
     * @access public
     * @param array $data 数据
     * @return array
     */
    public static function snake(array $data)
    {
        if (empty($data)) {
            return [];
        }

        foreach ($data as $itemKey => $item) {
            foreach ($item as $valueKey => $value) {
                $data[$itemKey][Str::snake($valueKey)] = $value;
                unset($data[$itemKey][$valueKey]);
            }
        }

        return $data;
    }

}
