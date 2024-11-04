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
        $this->cookie = Config('qfshop.quark_cookie');
    }
    
    /**
     * 全网搜索 该接口仅用于微信自动回复
     * 
     * @return void
     */
    public function all_search()
    {
        $searchdata = input('post.');
        if (empty($searchdata['title'])) {
            return jerr("请输入要看的内容");
        }
        $title = $searchdata['title'];
        
        
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];
        $map[] = ['is_time', '=', 1];
        $map[] = ['title|description', 'like', '%' . trim($title) . '%'];
            
        $urls = $this->model->where($map)->field('source_id as id, title, url,is_time')->order('update_time', 'desc')->limit(5)->select()->toArray();
        if (!empty($urls)) {
        
            // 获取所有需要更新的ID
            $ids = [];
            foreach ($urls as $item) {
                $ids[] = $item['id'];
            }
            
            // 更新数据库中的 update_time 字段
            if (!empty($ids)) {
                $this->model->whereIn('source_id', $ids)->update(['update_time' => time()]);
            }
            
            return jok('临时资源获取成功',$urls);
        }


         //同一个搜索内容锁机
         if (Cache::has($title)) {
            // 检查缓存中是否已有结果
            return jok('临时资源获取成功1', Cache::get($title));
        }
        
        
        // 检查是否有正在处理的请求
        if (Cache::has($title . '_processing')) {
            // 如果当前正在处理相同关键词的请求，等待结果
            $startTime = time(); // 记录开始时间
            while (Cache::has($title . '_processing')) {
                usleep(1000000); // 暂停1秒
        
                // 检查是否超过60秒
                if (time() - $startTime > 60) {
                    return jok('临时资源获取成功3', []); // 返回空数组
                }
            }
            return jok('临时资源获取成功2', Cache::get($title));
        }

        
        // 设置处理状态为正在处理
        Cache::set($title . '_processing', true, 60); // 锁定60秒
        
        $searchList = []; //查询的结果集
        $datas = []; //最终数据
        $num_total = 2; //最多想要几条结果
        $num_success = 0;
        
        // 处理第2个源
        foreach (source2($title) as $value) {
            if ($num_success >= $num_total) {
                break; // 有效结果数量已达到，则跳出循环
            }
            // 如果 URL 不存在则新增 $value
            if (!$this->urlExists($searchList, $value['url'])) {
                $searchList[] = $value;
                $this->processUrl($value, $num_success, $datas);
            }
        }
        
        
        
        // 处理第4个源
        if ($num_success < $num_total) {
            foreach (source4($title) as $value) {
                if ($num_success >= $num_total) {
                    break; // 有效结果数量已达到，则跳出循环
                }
                // 如果 URL 不存在则新增 $value
                if (!$this->urlExists($searchList, $value['url'])) {
                    $searchList[] = $value;
                    $this->processUrl($value, $num_success, $datas);
                }
            }
        }
        
        
        // 处理第3个源
        if ($num_success < $num_total) {
            foreach (source3($title) as $value) {
                if ($num_success >= $num_total) {
                    break; // 有效结果数量已达到，则跳出循环
                }
                // 如果 URL 不存在则新增 $value
                if (!$this->urlExists($searchList, $value['url'])) {
                    $searchList[] = $value;
                    $this->processUrl($value, $num_success, $datas);
                }
            }
        }
        
        
        // 处理第1个源 第一个源放最后
        if ($num_success < $num_total) {
            foreach (source1($title) as $value) {
                if ($num_success >= $num_total) {
                    break; // 有效结果数量已达到，则跳出循环
                }
                // 如果 URL 不存在则新增 $value
                if (!$this->urlExists($searchList, $value['url'])) {
                    $searchList[] = $value;
                    $this->processUrl($value, $num_success, $datas);
                }
            }
        }

        Cache::set($title, $datas, 60); // 缓存结果60秒
        Cache::delete($title . '_processing'); // 解锁
        
        return jok('临时资源获取成功',$datas);
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
            'cookie' => $this->cookie,
            'url' => $value['url'],
            'expired_type' => 2,
            'to_pdir_fid' => '', //存入目标文件
            'ad_fid' => '', //分享时带上这个文件
        );
        $res = curlHelper(Request::domain()."/api/open/transfer", "POST", $urlData)['body'];
        $res = json_decode($res, true);
    
        if($res['code'] !== 200){
            return; // 模拟 continue 行为
        }
    
        $patterns = '/^\d+\./';
        $title = preg_replace($patterns, '', $value['title']);
        // 添加资源到系统中
        $data["title"] =$title;
        $data["url"] =$res['data']['share_url'];
        $data["is_type"] = determineIsType($data["url"]);
        $data["fid"] =$res['data']['fid']??'';
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
                $filelist = [];
                $filelist[] = $deles['fid'];
                
                
                $urlData =  array(
                    'action_type' => 2,
                    'exclude_fids' => [],
                    'filelist' => $filelist,
                );
                $urlHeader = array(
                    'Accept: application/json, text/plain, */*',
                    'Accept-Language: zh-CN,zh;q=0.9',
                    'content-type: application/json;charset=UTF-8',
                    'sec-ch-ua: "Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "Windows"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: same-site',
                    'Referer: https://pan.quark.cn/',
                    'Referrer-Policy: strict-origin-when-cross-origin',
                    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'cookie: '.$this->cookie,
                );
                $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/file/delete?pr=ucpro&fr=pc&uc_param_str=", "POST", json_encode($urlData), $urlHeader)['body'];
                $res = json_decode($res, true);
                if($res['status'] == 200){
                    $this->model->where('fid', $deles['fid'])->delete();
                }
            }
        });
        
        return jok('临时资源删除成功',$abc);
    }

}
