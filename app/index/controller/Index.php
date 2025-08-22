<?php

namespace app\index\controller;

use think\App;
use think\facade\View;
use think\facade\Request;
use think\facade\Cache;
use app\index\QfShop;
use app\model\Source as SourceModel;
use app\model\SourceCategory as SourceCategoryModel;
use app\model\ApiList as ApiListModel;

use Lizhichao\Word\VicWord;


class Index extends QfShop
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->SourceModel = new SourceModel();
        $this->SourceCategoryModel = new SourceCategoryModel();
        $this->ApiListModel = new ApiListModel();
    }

    /**
     * @description: 首页
     * @param {*}
     * @return {*}
     */    
    public function index()
    {
        $rankList = $this->SourceCategoryModel->field('source_category_id,name,image,is_sys,is_type')->where([['status','=',0]])->order('sort desc')->select();
        $newList = [];
        
        $map[] = ['status', '=', 1];
        $map[] = ['is_time', '=', 0];
        $map[] = ['is_delete', '=', 0];
        if(config("qfshop.home_new") == 0){
            //最新榜
            $newList = $this->SourceModel->order(['create_time' => 'desc'])
                ->field('title,create_time as time,source_id as id')
                ->where($map)
                ->limit(Config('qfshop.ranking_num') ?? 1)
                ->select()->each(function($item,$key){
                    $item['times'] = substr($item['time'], 5, 5);
                    unset($item['time']); 
                    return $item;
                });
        }
        
        //热门排行榜数据
        $hotList = [];
        $cacheDir = root_path('runtime/api/cache'); // runtime/cache 目录
        foreach ($rankList as $value) {
            if($value['is_sys'] == 1 && $value['is_type'] == 0){
                $cacheFile = $cacheDir . "ranking_data_{$value['name']}.cache";
                if (file_exists($cacheFile)) {
                    $hotList[] = array(
                        'name'=> $value['name'],
                        'image'=> $value['image'],
                        'list'=> json_decode(file_get_contents($cacheFile), true),
                    );
                }
            }else{
                $list = $this->SourceModel->order(['create_time' => 'desc'])
                        ->field('title,create_time as time,source_id as id')
                        ->where($map)
                        ->where(['source_category_id' => $value['source_category_id']])
                        ->limit(Config('qfshop.ranking_num') ?? 1)
                        ->select()->each(function($item,$key){
                            $item['times'] = substr($item['time'], 5, 5);
                            unset($item['time']); 
                            return $item;
                        })->toArray();
                $hotList[] = array(
                    'name'=> $value['name'],
                    'image'=> $value['image'],
                    'list'=> $list,
                );
            }
        }
        
        $config = config("qfshop");
        
        View::assign('newList', $newList);
        View::assign('hotList', $hotList);
        View::assign('config', $config);
        View::assign('rankList', $rankList);
        View::assign('fixed', 1);
        View::assign('category_id', 0);
        View::assign('seo_title', $config['app_name'].' - '.$config['app_title']);
        View::assign('seo_keywords', $config['app_keywords']);
        View::assign('seo_description', $config['app_description']);
        return View::fetch('/news/index');
    }
    
    
     /**
     * @description: 搜索列表
     * @param {*}
     * @return {*}
     */    
    public function list($name,$page=1,$cate='')
    {
        $config = config("qfshop");

        // 被屏蔽的关键词，用逗号分隔
        $banKeywords = explode(',', $config['ban_keywords']);

        // 默认$list为空
        $list = [
            'total_result' => 0,
            'items' => []
        ];

        // 检查$name是否包含屏蔽关键词
        $blocked = false;
        foreach ($banKeywords as $keyword) {
            $keyword = trim($keyword);
            if ($keyword !== '' && mb_strpos($name, $keyword) !== false) {
                $blocked = true;
                break;
            }
        }

        $data['page_no'] = $page;
        $data['page_size'] = 10;
        $data['title'] = $name;
        $data['category_id'] = $cate;
        $data['search_type'] = 1;
        $data['is_time'] = 1;
        if (!$blocked) {
            // 没有屏蔽关键词才去查询
            $list = $this->SourceModel->getList($data);
        }
        
        
        $rankList = $this->SourceCategoryModel->field('name,image')->where([['status','=',0],['is_sys','=',1]])->order('sort desc')->select();
        
        $category = $this->SourceCategoryModel->field('name,source_category_id as id')->where([['status','=',0]])->order('sort desc')->select();
        
        
        //热门排行榜数据
        $hotList = [];
        $cacheDir = root_path('runtime/api/cache'); // runtime/cache 目录
        foreach ($rankList as $value) {
            $cacheFile = $cacheDir . "ranking_data_{$value['name']}.cache";
            if (file_exists($cacheFile)) {
                $hotList[] = array(
                    'name'=> $value['name'],
                    'image'=> $value['image'],
                    'list'=> json_decode(file_get_contents($cacheFile), true),
                );
            }
        }

        // 查询数据库，按 weight 排序
        $lines = $this->ApiListModel
            ->field('pantype, COUNT(*) as total, MAX(weight) as max_weight')
            ->where('status', 1)
            ->group('pantype')
            ->order('max_weight desc')
            ->select();

        // 统计数量
        $linesTotal = [];
        foreach ($lines as $item) {
            $linesTotal[$item['pantype']] = $item['total'];
        }

        // 定义名称映射
        $names = [
            0 => '夸克网盘',
            2 => '百度网盘',
            3 => 'UC网盘'
        ];

        // 根据查询结果生成显示列表（顺序和数据库一致）
        $displayList = [];
        foreach ($lines as $item) {
            if (!empty($item['total'])) {
                $displayList[] = [
                    'type' => $item['pantype'],
                    'name' => $names[$item['pantype']] ?? '未知网盘',
                    'total' => $item['total']
                ];
            }
        }

        // 记录第一个 key（如果需要前端默认选中）
        $firstKey = !empty($displayList) ? $displayList[0]['type'] : null;

        // 如果没有任何数据
        if (empty($displayList)) {
            $config['is_quan'] = 0;
        }

        // 传给模板
        View::assign('blocked', $blocked);
        View::assign('displayList', $displayList);
        View::assign('firstKey', $firstKey);
        View::assign('hotList', $hotList);
        View::assign('rankList', $rankList);
        View::assign('category', $category);
        View::assign('list', $list);
        View::assign('config', $config);
        View::assign('keyword', $data['title']);
        View::assign('page_size', $data['page_size']);
        View::assign('page_no', $data['page_no']);
        View::assign('category_id', $data['category_id']);
        View::assign('seo_title', $data['title'].' - '.$config['app_name']);
        View::assign('seo_keywords', $data['title'].','.$config['app_keywords']);
        View::assign('seo_description', $data['title'].' - '.$config['app_description']);
        return View::fetch('/news/list');
    }
    
    
    /**
     * @description: 详情
     * @param {*}
     * @return {*}
     */    
    public function detail($id)
    {
        if(empty($id)){
            return redirect('/');
        }
        
        
        $data['id'] = $id;
        $detail = $this->SourceModel->getDetail($data);
        
        if(empty($detail)){
            return redirect('/');
        }
        
        $rankList = $this->SourceCategoryModel->field('name,image')->where([['status','=',0],['is_sys','=',1]])->order('sort desc')->select();
        
        //热门排行榜数据
        $hotList = [];
        $cacheDir = root_path('runtime/api/cache'); // runtime/cache 目录
        foreach ($rankList as $value) {
            $cacheFile = $cacheDir . "ranking_data_{$value['name']}.cache";
            if (file_exists($cacheFile)) {
                $hotList[] = array(
                    'name'=> $value['name'],
                    'image'=> $value['image'],
                    'list'=> json_decode(file_get_contents($cacheFile), true),
                );
            }
        }
        
        
        //相关资源
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];
        $fc = new VicWord();
        $keywords = $fc->getAutoWord(preg_replace('/[\（\（][^\）]*[\）\）]/u', '', $detail['title']));
        $keywords = filterAndExtractWords($keywords);
        $keywords[] = '';//这个是为了在没有相关资源时不至于获取不到资源
        $weightExpr = [];
        foreach ($keywords as $keyword) {
            $weightExpr[] = "IF(title LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%', 1, 0)";
            $searchTitle[] = $keyword;
        }
        $weightExpr = implode(' + ', $weightExpr);
        // 在查询中添加权重计算和排序
        $query = $this->SourceModel->alias('a')
            ->field('a.*, (' . $weightExpr . ') as weight')->where($map)
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->whereOr('title', 'like', '%' . trim($keyword) . '%')
                          ->whereOr('description', 'like', '%' . trim($keyword) . '%');
                }
            });
        $order = ['weight' => 'desc', 'source_id' => 'desc'];
        $sameList = $query->where('source_id','<>',$detail['id'])->order($order)->limit(10)->select();
        
        

        $config = config("qfshop");
        
        View::assign('sameList', $sameList);
        View::assign('hotList', $hotList);
        View::assign('rankList', $rankList);
        View::assign('detail', $detail);
        View::assign('config', $config);
        View::assign('category_id', 0);

        if($detail['category'] && $detail['category']['name']){
            View::assign('seo_title', $detail['title'].'_'.$detail['category']['name'].' - '.$config['app_name']);
            View::assign('seo_keywords', $detail['title'].'_'.$detail['category']['name'].','.$config['app_keywords']);
            View::assign('seo_description', $detail['title'].'_'.$detail['category']['name'].' - '.$config['app_description']);
        }else{
            View::assign('seo_title', $detail['title'].' - '.$config['app_name']);
            View::assign('seo_keywords', $detail['title'].','.$config['app_keywords']);
            View::assign('seo_description', $detail['title'].' - '.$config['app_description']);
        }
        return View::fetch('/news/detail');
    }
    
    
    public function show()
    {
        $data = input('');
        $this->SourceModel = new SourceModel();
        
        // 搜索条件
        $map = [];

        $map[] = ['status', '=', 1];
        $map[] = ['is_time', '=', 0];
        
        if(!empty($data['type'])){
            // 将 $data['type'] 转换为时间戳
            $dayStart = strtotime($data['type']);
            $dayEnd = $dayStart + 86400; // 86400 秒 = 24 小时
        
            // 添加日期范围条件，只统计所选日期的记录
            $map[] = ['create_time', 'between', [$dayStart, $dayEnd]];
            View::assign('day', date('n月j日', $dayStart));

        }else{
            // 获取今天的时间戳范围
            $todayStart = strtotime(date('Y-m-d'));
            $todayEnd = $todayStart + 86400; // 86400 秒 = 24 小时
            
            // 添加日期范围条件，只统计今天的记录
            $map[] = ['create_time', 'between', [$todayStart, $todayEnd]];
            View::assign('day',date('n月j日'));
        }


        $result = $this->SourceModel->field('source_id as id,source_category_id,title,url,create_time as time,is_time')->where($map)->select()->each(function($item,$key){
                $item['times'] = substr($item['time'], 0, 10);
                unset($item['time']); 
                return $item;
            })->toArray();
        
        
        View::assign('list', $result);
        return View::fetch();
    }
}
