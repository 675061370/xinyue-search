<?php

namespace app\model;

use app\model\QfShop;

use Lizhichao\Word\VicWord;

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
     * hasOne qf_source_category
     * @access public
     * @return mixed
     */
    public function category()
    {
        return $this
            ->hasOne(SourceCategory::class, 'source_category_id', 'source_category_id')
            ->joinType('left')
            ->field('source_category_id,name');
    }


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
        $field = 'source_id as id,source_category_id,title,url,create_time as time,vod_content,vod_pic,is_type';
        $result = $this->with('category')->where($map)->field($field)->find();
        if(!is_null($result)){
            $result->inc('page_views')->update();
            $result['times'] = substr($result['time'], 0, 10);
        }
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
        
        //默认排序
        $order = ['source_id' => 'desc'];
        
        //需要高亮的词 仅分词搜索使用
        $searchTitle = [];
        
        $map[] = ['status', '=', 1];
        $map[] = ['is_time', '=', 0];
        $map[] = ['is_delete', '=', 0];

        if(!empty($data['day']) && $data['day']==2){
            // 获取今天的时间戳范围
            $todayStart = strtotime(date('Y-m-d'));
            $todayEnd = $todayStart + 86400; // 86400 秒 = 24 小时
            // 获取昨天的时间戳范围
            $yesterdayStart = $todayStart - 86400;
            $yesterdayEnd = $todayStart;
            // 添加日期范围条件
            $map[] = ['create_time', 'between', [$yesterdayStart, $todayEnd]];
            
             //记录站点更新日志
            $ip = $_SERVER['REMOTE_ADDR'];
            $ips = Log::where(['ip'=>$ip])->find();
            if(empty($ips)){
                $log = new Log();
                $log->save(['name' => '访问记录','ip'=>$ip]);
            }else{
                Log::where('id', $ips['id'])->update(['update_time' => time()]);
            }
        }
        
        if(!empty($data['is_time']) && $data['is_time']==1){
            unset($map[array_search(['is_time', '=', 0], $map)]);
        }
        
        if(!empty($data['category_id'])){
            // 将逗号分隔的字符串转换为数组
            $categoryIds = explode(',', $data['category_id']);
            // 使用 in 查询
            $map[] = ['source_category_id', 'in', $categoryIds];
        }
        
        if(!empty($data['type']) && $data['type']==2){
            $map[] = ['is_type', '=', 0];
        }
        
        
        // 如果存在 title，则进行分词
        if (!empty($data['title'])) {
            $search_type = config('qfshop.search_type')??1;
            
            if($search_type == 0){
                $map[] = ['title|description', 'like', '%' . trim($data['title']) . '%'];
                $query = $this->where($map);
            }else{
                $fc = new VicWord();
                $keywords = $fc->getAutoWord($data['title']);
                $keywords = filterAndExtractWords($keywords);
                
                // 如果分词后有关键词
                if (count($keywords) > 1) {
                    if($search_type == 1){
                        //分词同时满足才搜索的到！
                        foreach ($keywords as $keyword) {
                            $map[] = ['title|description', 'like', '%' . $keyword . '%'];
                        }
                        $query = $this->where($map);
                    }else{
                        // 分词只要满足其一就可以搜索到！
                        $weightExpr = [];
                        foreach ($keywords as $keyword) {
                            $weightExpr[] = "IF(title LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%', 1, 0)";
                            $searchTitle[] = $keyword;
                        }
                        $weightExpr = implode(' + ', $weightExpr);
            
                        // 在查询中添加权重计算和排序
                        $query = $this->alias('a')
                            ->field('a.*, (' . $weightExpr . ') as weight')->where($map)
                            ->where(function($query) use ($keywords) {
                                foreach ($keywords as $keyword) {
                                    $query->whereOr('title', 'like', '%' . trim($keyword) . '%')
                                          ->whereOr('description', 'like', '%' . trim($keyword) . '%');
                                }
                            });
                        $order = ['weight' => 'desc', 'source_id' => 'desc'];
                    }
                } else {
                    // 如果没有关键词，仍然使用原来的 title 查询
                    $map[] = ['title|description', 'like', '%' . trim($data['title']) . '%'];
                    $query = $this->where($map);
                }
            }
        }else{
            // 构建查询
            $query = $this->where($map);
        }
        
        
        
        if(!empty($data['type']) && $data['type']==2){
            $order = ['source_id' => 'asc'];
        }
        

        $result['total_result'] = $query->count();
        if ($result['total_result'] <= 0) {
            $result['items'] = [];
            return $result;
        }

        // 获取分页数据
        $result['items'] = $query->order($order)
            ->field('source_id as id, source_category_id, title, is_type,  code, url, update_time as time, is_time')
            ->with('category')
            ->withSearch(['page', 'order'], $data)
            ->select()->each(function($item) use ($searchTitle) {
                $item['name'] = highlightKeywords($item['title'], $searchTitle);
                $item['times'] = substr($item['time'], 0, 10);
                unset($item['time']); 
                return $item;
            })
            ->toArray();
            
        // 如果 $data['is_time'] == 1，则更新 update_time 字段
        if (!empty($data['is_time']) && $data['is_time'] == 1) {
    
            // 获取所有需要更新的ID
            $ids = [];
            foreach ($result['items'] as $item) {
                if ($item['is_time'] == 1) {
                    $ids[] = $item['id'];
                }
            }
    
            // 更新数据库中的 update_time 字段
            if (!empty($ids)) {
                $this->whereIn('source_id', $ids)->update(['update_time' => time()]);
            }
        }
    
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

        $result['items'] = $this->setDefaultOrder(['create_time' => 'desc'])
            ->field('title,create_time as time')
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
        $urlData = array(
            'endDay' => date("Y-m-d", strtotime("-1 day")), 
            'startDay' => date("Y-m-d", strtotime("-1 day"))
        );
        $urlHeader = array('Content-Type: application/json');
        
        //线路2
        $res = curlHelper("https://sycsp-prd.matesec.net/api/sp/miniApp/seriesRankList", "POST", json_encode($urlData),$urlHeader)['body'];
        $res = json_decode($res, true);
        if($res['code'] !== 200){
            return jerr($res['msg']);
        }
        if(empty($res['data']['seriesHeatRankList'])){
            $urlData = array(
                'endDay' => date("Y-m-d", strtotime("-2 day")), 
                'startDay' => date("Y-m-d", strtotime("-2 day"))
            );
            $urlHeader = array('Content-Type: application/json');
            
            //线路2
            $res = curlHelper("https://sycsp-prd.matesec.net/api/sp/miniApp/seriesRankList", "POST", json_encode($urlData),$urlHeader)['body'];
            $res = json_decode($res, true);
            if($res['code'] !== 200){
                return jerr($res['msg']);
            }
        }
        
        $ranking = 1;
        $result = [];
        foreach ($res['data']['seriesHeatRankList'] as $value) {
            $result[] = [
              'ranking' => $ranking++,
              'title' => $value['seriesName']??'',
              'hot' => $value['heatCount']??0,
              'hots' => $value['heatCountDisplay']??'',
            ];
        }
        return $result;
    }
    
    
}
