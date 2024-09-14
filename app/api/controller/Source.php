<?php


namespace app\api\controller;

use think\App;
use app\api\QfShop;
use think\facade\Cache;
use Carbon\Carbon;
use quarkPlugin\QuarkPlugin;
use app\model\SourceCategory as SourceCategoryModel;

class Source extends QfShop
{   
    public function __construct(App $app)
    {
        parent::__construct($app);
    }
    public function day()
    {
        // 当前日期
        $currentDate = Carbon::today()->toDateString();
        // 缓存键名
        $cacheKey = 'api_alone_date_' . $currentDate;

        // 检查缓存中是否存在该键
        if (Cache::has($cacheKey)) {
            return jerr("该接口今天已经执行过，请1小时后再试！");
        }

        Cache::set($cacheKey, time(), 2400);
        
        
        
        $SourceCategoryModel = new SourceCategoryModel();
        $map[] = ['is_update', '=', 1];
        $data = $SourceCategoryModel->where($map)->column('source_category_id');
        $ids = implode(',', $data);

        $quarkPlugin = new QuarkPlugin();
        $quarkPlugin->transferAll($ids,2);
        return jok('已提交任务，稍后查看结果');
    }
}
