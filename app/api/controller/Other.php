<?php

namespace app\api\controller;

use think\App;
use think\facade\Cache;
use think\facade\Request;
use app\api\QfShop;
use app\model\Source as SourceModel;
use app\model\Days as DaysModel;

class Other extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new SourceModel();
    }
    
    /**
     * 全网搜索 该接口仅用于微信自动回复
     * 
     * @return void
     */
    public function all_search($param='')
    {
        $title = $param ?: input('post.title', '');
        if (empty($title)) {
            return jerr("请输入要看的内容");
        }
        
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];
        $map[] = ['is_time', '=', 1];
        $map[] = ['title|description', 'like', '%' . trim($title) . '%'];
            
        $urls = $this->model->where($map)->field('source_id as id, title, url,is_time')->order('update_time', 'desc')->limit(5)->select()->toArray();
        if (!empty($urls)) {
            $ids = array_column($urls, 'id');
            $this->model->whereIn('source_id', $ids)->update(['update_time' => time()]);
            return !empty($param) ? $urls : jok('临时资源获取成功', $urls);
        }

        //同一个搜索内容锁机
        if (Cache::has($title)) {
            // 检查缓存中是否已有结果
            return !empty($param) ? Cache::get($title) : jok('临时资源获取成功', Cache::get($title));
        }
        
        // 检查是否有正在处理的请求
        if (Cache::has($title . '_processing')) {
            // 如果当前正在处理相同关键词的请求，等待结果
            $startTime = time(); // 记录开始时间
            while (Cache::has($title . '_processing')) {
                usleep(1000000); // 暂停1秒
        
                // 检查是否超过60秒
                if (time() - $startTime > 60) {
                    return !empty($param) ? [] : jok('临时资源获取成功', []);
                }
            }
            return !empty($param) ? Cache::get($title) : jok('临时资源获取成功', Cache::get($title));
        }

        
        // 设置处理状态为正在处理
        Cache::set($title . '_processing', true, 60); // 锁定60秒
        
        $searchList = []; //查询的结果集
        $datas = []; //最终数据
        $num_total = 2; //最多想要几条结果
        $num_success = 0;
        
        // 定义源的顺序
        $sources = ['source2', 'source4', 'source5', 'source3', 'source1'];
        
        foreach ($sources as $source) {
            if ($num_success >= $num_total) {
                break;
            }
    
            foreach ($source($title) as $value) {
                if ($num_success >= $num_total) {
                    break;
                }
    
                if (!$this->urlExists($searchList, $value['url'])) {
                    $searchList[] = $value;
                    $this->processUrl($value, $num_success, $datas);
                }
            }
        }
        
        Cache::set($title, $datas, 60); // 缓存结果60秒
        Cache::delete($title . '_processing'); // 解锁
        
        return !empty($param) ? $datas : jok('临时资源获取成功', $datas);
    }
    
    // 检查 URL 是否已存在（忽略查询参数）
    public function urlExists($searchList, $urlToCheck) {
        // 解析待检查的 URL
        $parsedUrlToCheck = parse_url($urlToCheck);
    
        foreach ($searchList as $item) {
            $parsedUrl = parse_url($item['url']);
    
            // 比较 scheme, host 和 path
            if ($parsedUrlToCheck['scheme'] === $parsedUrl['scheme'] &&
                $parsedUrlToCheck['host'] === $parsedUrl['host'] &&
                $parsedUrlToCheck['path'] === $parsedUrl['path']) {
                return true;
            }
        }
    
        return false;
    }
    
    /**
     * 临时资源转存
     * 
     * @return void
     */
    public function processUrl($value, &$num_success, &$datas) 
    {
        $substring = strstr($value['url'], 's/');
        if ($substring === false) {
            return; // 模拟 continue 行为
        }
        
        $pwd_id = substr($substring, 2); // 去除 's/' 部分
    
        $urlData = array(
            'url' => $value['url'],
            'expired_type' => 2,
            'ad_fid' => '', //分享时带上这个文件
        );

        $transfer = new \netdisk\Transfer();
        $res = $transfer->transfer($urlData);

        if($res['code'] !== 200){
            return; // 模拟 continue 行为
        }
    
        $patterns = '/^\d+\./';
        $title = preg_replace($patterns, '', $value['title']);
        // 添加资源到系统中
        $data["title"] =$title;
        $data["url"] =$res['data']['share_url'];
        $data["is_type"] = determineIsType($data["url"]);
        $dataFid = $res['data']['fid']??'';
        $data["fid"] = is_array($dataFid) ? json_encode($dataFid) : $dataFid;
        $data["is_time"] = 1;
        $data["update_time"] = time();
        $data["create_time"] = time();
        $data["id"] = $this->model->insertGetId($data);
        $datas[] =$data;
        $num_success++;
    }
    
    
    /**
     * 30分钟后清除临时资源
     * 
     * @return void
     */
    public function delete_search()
    {
        // 搜索条件
        $map[] = ['is_time', '=', 1];
        $map[] = ['update_time', '<=', time() - (30 * 60)];
        $abc = $this->model->where($map)->select();
        
        
        $this->model->where($map)->chunk(100, function ($order) {
            foreach ($order as $value) {
                $deles = $value->toArray();

                $fid = $deles['fid'];

                // 尝试解码，如果是有效的 JSON 数组则使用，否则转为单元素数组
                $filelist = (is_string($fid) && ($decodedFid = json_decode($fid, true)) && is_array($decodedFid)) ? $decodedFid : (array)$fid;
               
                $this->model->where('fid', $deles['fid'])->delete();
                $transfer = new \netdisk\Transfer();
                $transfer->deletepdirFid($deles['is_type'],$filelist);
            }
        });
        
        return jok('临时资源删除成功',$abc);
    }

}
