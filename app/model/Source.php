<?php

namespace app\model;

use app\model\QfShop;

class Source extends QfShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'source_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'source_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'source_id'    => 'integer',
        'is_delete'      => 'integer',
        'status'      => 'integer',
        'time'  =>  'timestamp',
    ];


    /**
     * @description: 获取一个信息
     * @param {*} $code
     * @return {*}
     */
    public function getDetail(array $data)
    {
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];
        $map[] = ['source_id', '=', $data['id']];
        $field = 'source_id as id,title,url,update_time as time';
        $result = $this->where($map)->field($field)->find();
        if(!is_null($result)){
            $result->inc('page_views')->update();
        }
        $result['times'] = substr($result['time'], 0, 10);
        unset($result['time']); 
        return $result;
    }
    
    /**
     * 获取列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getList(array $data)
    {

        // 搜索条件
        $map = [];
        empty($data['title']) ?: $map[] = ['title', 'like', '%' . $data['title'] . '%'];

        $map[] = ['status', '=', 1];
        $map[] = ['is_time', '=', 0];

        if(!empty($data['day']) && $data['day']==2){
            // 获取今天的时间戳范围
            $todayStart = strtotime(date('Y-m-d'));
            $todayEnd = $todayStart + 86400; // 86400 秒 = 24 小时
            // 获取昨天的时间戳范围
            $yesterdayStart = $todayStart - 86400;
            $yesterdayEnd = $todayStart;
            // 添加日期范围条件
            $map[] = ['create_time', 'between', [$yesterdayStart, $todayEnd]];
        }

        if(!empty($data['is_time']) && $data['is_time']==1){
            unset($map[array_search(['is_time', '=', 0], $map)]);
        }

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        $order = ['source_id' => 'desc'];
        if(!empty($data['type']) && $data['type']==2){
            $order = ['source_id' => 'asc'];
        }

        $result['items'] = $this->setDefaultOrder($order)
            ->field('source_id as id,title,url,update_time as time,is_time')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()->each(function($item,$key){
                $item['times'] = substr($item['time'], 0, 10);
                unset($item['time']); 
                return $item;
            })
            ->toArray();
        return $result;
    }
    
    /**
     * 获取最新
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getNew(array $data)
    {

        // 搜索条件
        $map = [];

        $map[] = ['status', '=', 1];
        $map[] = ['is_time', '=', 0];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        $result['items'] = $this->setDefaultOrder(['update_time' => 'desc'])
            ->field('title,update_time as time')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()->each(function($item,$key){
                $item['times'] = substr($item['time'], 5, 5);
                unset($item['time']); 
                return $item;
            })
            ->toArray();
        return $result;
    }
    
    
     /**
     * 获取最热
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getHot(array $data)
    {
        // 搜索条件
        $map = [];

        $map[] = ['status', '=', 1];
        $map[] = ['is_time', '=', 0];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        $result['items'] = $this->setDefaultOrder(['page_views' => 'desc'])
            ->field('title,update_time as time')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()->each(function($item,$key){
                $item['times'] = substr($item['time'], 5, 5);
                unset($item['time']); 
                return $item;
            })
            ->toArray();
        return $result;
    }
    
    
}
