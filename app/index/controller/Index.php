<?php

namespace app\index\controller;

use think\App;
use think\facade\View;
use think\facade\Request;
use think\facade\Cache;
use app\index\QfShop;
use app\model\Source as SourceModel;
use app\model\SourceCategory as SourceCategoryModel;


class Index extends QfShop
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->SourceModel = new SourceModel();
        $this->SourceCategoryModel = new SourceCategoryModel();
    }

    /**
     * @description: 首页
     * @param {*}
     * @return {*}
     */    
    public function index()
    {
        $rankList = $this->SourceCategoryModel->field('name,image')->where([['status','=',0],['is_sys','=',1]])->order('sort desc')->select();
        $newList = [];
        
        if(config("qfshop.ranking_type") == 0 && config("qfshop.home_new") == 0){
            //最新榜
            $map[] = ['status', '=', 1];
            $map[] = ['is_time', '=', 0];
            $map[] = ['is_delete', '=', 0];
            
            $newList = $this->SourceModel->order(['create_time' => 'desc'])
                ->field('title,create_time as time')
                ->where($map)
                ->limit(Config('qfshop.ranking_num') ?? 1)
                ->select()->each(function($item,$key){
                    $item['times'] = substr($item['time'], 5, 5);
                    unset($item['time']); 
                    return $item;
                });
        }
        
        
        View::assign('newList', $newList);
        View::assign('config', config("qfshop"));
        View::assign('rankList', $rankList);
        View::assign('fixed', 1);
        View::assign('category_id', 0);
        return View::fetch('/news/index');
    }
    
    
     /**
     * @description: 搜索列表
     * @param {*}
     * @return {*}
     */    
    public function list($name,$page=1,$cate='')
    {
        $data['page_no'] = $page;
        $data['page_size'] = 10;
        $data['title'] = $name;
        $data['category_id'] = $cate;
        $list = $this->SourceModel->getList($data);
        
        
        $rankList = $this->SourceCategoryModel->field('name,image')->where([['status','=',0],['is_sys','=',1]])->order('sort desc')->select();
        
        $category = $this->SourceCategoryModel->field('name,source_category_id as id')->where([['status','=',0]])->order('sort desc')->select();
        
        
        View::assign('rankList', $rankList);
        View::assign('category', $category);
        View::assign('list', $list);
        View::assign('config', config("qfshop"));
        View::assign('keyword', $data['title']);
        View::assign('page_size', $data['page_size']);
        View::assign('page_no', $data['page_no']);
        View::assign('category_id', $data['category_id']);
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

        
        View::assign('rankList', $rankList);
        View::assign('detail', $detail);
        View::assign('config', config("qfshop"));
        View::assign('category_id', 0);
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
