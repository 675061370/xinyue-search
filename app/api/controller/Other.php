<?php

namespace app\api\controller;

use think\App;
use app\api\QfShop;
use app\model\Source as SourceModel;
use app\model\Days as DaysModel;

class Other extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //第三方转存接口地址
        $this->url = "https://pan.xinyuedh.com";
        $this->model = new SourceModel();
        $this->cookie = Config('qfshop.quark_cookie');
    }
    
    public function Alone_search2()
    {
        $searchdata = input('');
        if (empty($searchdata['alone_title'])) {
            return jerr("请输入要看的内容");
        }
        
        $type = $searchdata['type']??0;
        $title = $searchdata['alone_title'];
        
        $searchList = []; //查询的结果集
        $num_total = $type?2:20; //最多想要几条结果
        $num_success = 0;
        
        // 处理第2个源
        foreach (source2($title) as $value) {
            if ($num_success >= $num_total) {
                break; // 有效结果数量已达到，则跳出循环
            }
            // 如果 URL 不存在则新增 $value
            if (!$this->urlExists($searchList, $value['url'])) {
                $searchList[] = $value;
                $num_success++;
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
                    $num_success++;
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
                    $num_success++;
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
                    $num_success++;
                }
            }
        }
            
        return jok('临时资源获取成功',$searchList);
    }
    
    
    /**
     * 全网搜索 该接口仅用于微信自动回复
     * 
     * @return void
     */
    public function Alone_search()
    {
        
        $searchdata = input('post.');
        if (empty($searchdata['alone_title'])) {
            return jerr("请输入要看的内容");
        }
        $title = $searchdata['alone_title'];
        
        
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];
        $map[] = ['is_time', '=', 1];
        $map[] = ['title|description', 'like', '%' . trim($title) . '%'];
            
        $urls = $this->model->where($map)->field('source_id as id, title, url')->order('update_time', 'desc')->limit(5)->select()->toArray();
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
            'to_pdir_fid' => '61bad1e1380d47c78d6f5d86c877efb9', //存入目标文件
            'ad_fid' => '3b57147245774d88bafba7f43152f4bd', //分享时带上这个文件
        );
        $res = curlHelper($this->url."/api/open/transfer", "POST", $urlData)['body'];
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
        $this->model->insertGetId($data);
        $datas[] =$data;
        $num_success++;
    }
    
    
    /**
     * 十分钟后清除临时资源
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
    
    
    public function alone_zhuanshu22222()
    {
        $list = [];
        try {
            $result = curlHelper("https://duanju.niurl.cn/api.php", "GET")['body'];
            $list = json_decode($result, true);
            $list = $list['data'];
        } catch (Exception $e) {
        }
        
        if (count($list) > 100) {
            $list = array_slice($list, 0, 100); // 如果超过100位，则截取前100位
        }
        $list = array_reverse($list);
        
        foreach ($list as $key => $value){
            //如果资源不是今天或者昨天的 就跳过  只采集今天昨天的资源
            if($value['addtime'] != date('Y-m-d') && $value['addtime'] != date('Y-m-d', strtotime('-1 day'))){
                 continue;
            }
            
            
            //如已有此资源 跳过
            $detail = $this->model->where('title', $value['name'])->find();
            if(!empty($detail)){
                continue;
            }
       
            
            $url = $value['url'];
            $substring = strstr($url, 's/');
            if ($substring !== false) {
                $pwd_id = substr($substring, 2); // 去除 's/' 部分
            } else {
                //资源地址格式有误
                continue;
            }
            $urlData =  array(
                'cookie' => Config('qfshop.quark_cookie'),
                'url' => $url,
            );
            $res = curlHelper($this->url."/api/open/transfer", "POST", $urlData)['body'];
            $res = json_decode($res, true);
            if($res['code'] !== 200){
                continue;
            }
            
            //添加资源到系统中
            $data["title"] = $value['name'];
            $data["url"] = $res['data']['share_url'];
            $data["update_time"] = time();
            $data["create_time"] = time();
            $this->model->insertGetId($data);
        }
        
        return jok('123456');
    }
    
    public function alone_zhuanshu()
    {
        $list = [];
        $add_num = 0;
        $parameters = array(
            '',
            '',
            '',
            '6fbefc1f535f48a38cbb580f9553fcea', //4月夸克文件夹fid
            '6da509da98124e1d98cd3aa70f1ad7fa',
            '2b805566aee0430ca6a3a0ba995685bd',
            '93996a5d87b64c36849c442374fd1bbf',
            '499e29a766704e00ac0e6e3d1c7b252a', 
            '4c69edb70454496688813de3cd6a27f8', 
            'fed17385093446b78e8c2f286f683100', 
            '38233cc7b14148fea33ea9998c19dc22',
            '70c3c5d7a1cc442eb998428893b00e94'
        );
        
        // try {
        //     $result = curlHelper("https://duanju.niurl.cn/api.php", "GET")['body'];
        //     $list = json_decode($result, true);
        //     $list = $list['data'];
            
        // } catch (Exception $e) {
        // }
        try {
            $list1 = [];
            $result = curlHelper("https://kuoapp.com/duanju/get.php?day=".date('Y-m-d'), "GET")['body'];
            $res1 = json_decode($result, true);
            if (is_array($res1) && array_keys($res1) === range(0, count($res1) - 1)) {
                $list1 = $res1;
            }
            
            $list2 = [];
            $result = curlHelper("https://kuoapp.com/duanju/get.php?day=".date('Y-m-d', strtotime('-1 day')), "GET")['body'];
            $res2 = json_decode($result, true);
            if (is_array($res2) && array_keys($res2) === range(0, count($res2) - 1)) {
                $list2 = $res2;
            }
            $list = array_merge($list1, $list2); // 合并两个数组
        } catch (Exception $e) {
        }
        
        if (count($list) > 100) {
            $list = array_slice($list, 0, 100); // 如果超过100位，则截取前100位
        }
        $list = array_reverse($list);
  
        
        foreach ($list as $key => $value){
            // 如果资源不是今天或者昨天的 就跳过  只采集今天昨天的资源
            if($value['addtime'] != date('Y-m-d') && $value['addtime'] != date('Y-m-d', strtotime('-1 day'))){
                 continue;
            }
            
            
            //如已有此资源 跳过
            $value['name'] = str_replace(["\u0000", "\x00", "\0"], '', $value['name']);
            $detail = $this->model->where('title', $value['name'])->find();
            if(!empty($detail)){
                continue;
            }
            
            //整理数据 按天创建夸克文件夹
            $dateString = $value['addtime']; // 哪一天的资源  如：2024-04-01
            $timestamp = strtotime($dateString); // 将日期转换为时间戳
            $newDateString = date('n月j日', $timestamp); // 夸克文件夹的名称 格式： 4月1日
            $month = date('n', $timestamp);  //月份 格式：4
            
            $DaysModel = new DaysModel();
            
            $fids = $DaysModel->where(["time"=>$dateString])->find();
            if (!$fids){
                //去创建文件夹获取fid
                $urlData =  array(
                    'dir_init_lock' => false, 
                    'dir_path' => "", 
                    'file_name' => $newDateString, 
                    'pdir_fid' => $parameters[$month-1], 
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
                    'cookie: '.Config('qfshop.quark_cookie'),
                );
                $quark = curlHelper("https://drive-pc.quark.cn/1/clouddrive/file?pr=ucpro&fr=pc&uc_param_str=", "POST", json_encode($urlData), $urlHeader)['body'];
                $quark = json_decode($quark, true);
                if($quark['status'] !== 200){
                    continue;
                }
                
                $fid = $quark['data']['fid'];
                
                $DaysModel->insert([
                    "fid" => $fid,
                    "time" => $dateString,
                ]);
                $fids = $DaysModel->where(["time"=>$dateString])->find();
            }
            //最终我要获取 $fids['fid']  这个参数
            
            $url = $value['url'];
            $substring = strstr($url, 's/');
            if ($substring !== false) {
                $pwd_id = substr($substring, 2); // 去除 's/' 部分
            } else {
                //资源地址格式有误
                continue;
            }
            $urlData =  array(
                'cookie' => Config('qfshop.quark_cookie'),
                'url' => $url,
                'to_pdir_fid' => $fids['fid']??''
            );
            $res = curlHelper($this->url."/api/open/transfer", "POST", $urlData)['body'];
            $res = json_decode($res, true);
            if($res['code'] !== 200){
                continue;
            }
            
            //添加资源到系统中
            $data["title"] = $value['name'];
            $data["url"] = $res['data']['share_url'];
            $addtime = time();
            if(!empty($value['addtime'])){
                $addtime = $value['addtime'].' '.date('H:i');
                $addtime = strtotime($addtime);
            }
            $data["update_time"] = $addtime;
            $data["create_time"] = $addtime;
            $data["source_category_id"] = 1;
            $this->model->insertGetId($data);
            $add_num++;
        }
        
        return jok(date('Y-m-d H:i').' Added number of resources',$add_num);
    }
    
    public function aaaa()
    {
        $dateString = date('Y-m-d');
        $timestamp = strtotime($dateString); // 将日期转换为时间戳
        $newDateString = date('n月j日', $timestamp); // 夸克文件夹的名称 格式： 6月1日
        $month = date('n', $timestamp);  //月份 格式：6
        
        $list = [];
        try {
            $list1 = [];
            $result = curlHelper("https://kuoapp.com/duanju/get.php?day=".date('Y-m-d'), "GET")['body'];
            $res1 = json_decode($result, true);
            if (is_array($res1) && array_keys($res1) === range(0, count($res1) - 1)) {
                $list1 = $res1;
            }
            
            $list2 = [];
            $result = curlHelper("https://kuoapp.com/duanju/get.php?day=".date('Y-m-d', strtotime('-1 day')), "GET")['body'];
            $res2 = json_decode($result, true);
            if (is_array($res2) && array_keys($res2) === range(0, count($res2) - 1)) {
                $list2 = $res2;
            }
            $list = array_merge($list1, $list2); // 合并两个数组
        } catch (Exception $e) {
        }
        
        return jok('123456',date('H:i'));
    }
    
    
    
     /**
     * 在线观看接口 该接口仅用于微信自动回复
     * 
     * @return void
     */
    public function Alone_online()
    {
        if(!Config('qfshop.mp4_online')){
            return jok('在线资源地址', []);
        }
        // 获取输入数据
        $searchdata = input('');
        if (empty($searchdata['alone_title'])) {
            return jerr("请输入要看的内容");
        }
        $title = $searchdata['alone_title'];
    
        // 定义请求头
        $urlHeader = [
            'cookie: thinkphp_show_page_trace=0|0; pwmd5=9c2c9cc26c93ed2d7560cfc4ae4d4ac3; userid=312; usergroup=1; username=18339988501; usertime=2024-08-06+15%3A41%3A26; usersin=04e882c0b3f468fba62a5d1f03174aea',
            'Content-Type: application/json'
        ];
    
        // 查询参数
        $queryParams = [
            'page' => 1,
            'limit' => 1,
            'name' => $title,
            'type' => '',
        ];
    
        // 发送GET请求
        $res = curlHelper("https://vvc.qeduanju.cn/promote/list.html", "GET", json_encode([]), $urlHeader, $queryParams)['body'];
        $res = json_decode($res, true);
    
        // 检查响应并处理数据
        if ($res && $res['code'] == 0 && $res['count'] > 0) {
            $data = [];
            $ids = [];
    
            foreach ($res['data'] as $value) {
                $data[] = [
                    'id' => $value['id'],
                    'title' => $value['name']
                ];
                $ids[] = $value['id'];
            }
    
            // POST请求的数据
            $urlData = [
                "id" => $ids,
                "pzid" => 283
            ];
    
            // 发送POST请求
            $res2 = curlHelper("https://vvc.qeduanju.cn/promote/dwz.html", "POST", json_encode($urlData), $urlHeader, $queryParams)['body'];
            $res2 = json_decode($res2, true);
    
            if ($res2 && $res2['code'] == 0) {
                $finalData = [];
                foreach ($res2['data'] as $value) {
                    foreach ($data as &$item) {
                        if ($item['id'] == $value['id']) {
                            $finalData[] = [
                                'title' => '【在线观看】'.$item['title'],
                                'url' => $value['url']
                            ];
                        }
                    }
                }
                return jok('在线资源地址', $finalData);
            }
        }
    
        // 如果没有找到匹配数据，返回空数据
        return jok('在线资源地址', []);
    }
}
