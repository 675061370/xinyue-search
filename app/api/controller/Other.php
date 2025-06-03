<?php

namespace app\api\controller;

use think\App;
use think\facade\Cache;
use think\facade\Request;
use app\api\QfShop;
use app\model\Source as SourceModel;
use app\model\Days as DaysModel;
use app\model\ApiList as ApiListModel;

class Other extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new SourceModel();
        $this->ApiListModel = new ApiListModel();
    }

    /**
     * 全网搜索 该接口用户网页端使用
     * 
     * @return void
     */
    public function web_search()
    {
        // 设置 SSE 响应头
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // 防止 Nginx 缓冲

        $title = input('title', '');
        if (empty($title)) {
            echo "data: [DONE] 无搜索词\n\n";
            ob_flush();
            flush();
            exit;
        }
        $is_type = input('is_type', 0); //0夸克  2百度
        $is_show = input('is_show', 0); //0加密网址  1显示网址

        // 查找一条可用线路
        $lines = $this->ApiListModel->where('status', 1)->where('pantype', $is_type)->order('weight desc')->select()->toArray();
        
        // 获取自定义线路并合并到线路列表前面
        $lines = array_merge($this->getCustomLines(), $lines);

        if (!$lines || count($lines) == 0) {
            echo "data: [DONE] 暂无可用线路\n\n";
            ob_flush();
            flush();
            exit;
        }
        
        foreach ($lines as $line) {
            $result = [];
            $type = $line['type'] ?? 'api';
            if ($type === 'tg') {
                $result = $this->handleTg($line, $title);
            } else if ($type === 'api') {
                $result = $this->handleApi($line, $title);
            } else if ($type === 'html') {
                $result = $this->handleWeb($line, $title);
            } else if ($type === 'kk') {
                $result = $this->handleKk($line, $title, $line['num']);
            }

            foreach ($result as $item) {
                $item['is_type'] = determineIsType($item['url']);
                if(Config('qfshop.is_quan_zc')==1){
                    //检测是否有效
                    $infoData = $this->verificationUrl($item['url']);
                    if (!empty($infoData['stoken'])) {
                        $item['stoken'] = $infoData['stoken'];
                    }
                    if($infoData === 0) {
                        continue;
                    }
                }
                if(config('qfshop.is_quan_type') != 1 && $is_show != 1){
                    $item['url'] = encryptObject($item['url']);
                }
                echo "data: " . str_replace(["\n", "\r"], '', json_encode($item, JSON_UNESCAPED_UNICODE)) . "\n\n";
                ob_flush();
                flush();
            }
        }
        echo "data: [DONE]\n\n";
        ob_flush();
        flush();
        exit;
    }

    /**
     * 全网搜索 该接口仅用于机器人和微信对话时使用
     * 
     * @return void
     */
    public function all_search($param='')
    {
        $title = $param ?: input('title', '');
        if (empty($title)) {
            return jerr("请输入要看的内容");
        }
        $is_type = 0; //0夸克  2百度

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

        
        $typeV = input('type', 0);

        $searchList = []; //查询的结果集
        $datas = []; //最终转存后的数据
        $num_total = 2; //最多想要几条转存后的结果
        $num_success = 0;

        $datas_zc = []; //最终未转存的数据
        $num_total_zc = $typeV==1?3:0; //最多想要几条未转存的结果
        $num_success_zc = 0;

        // 查找一条可用线路
        $lines = $this->ApiListModel->where('status', 1)->where('pantype', $is_type)->order('weight desc')->select()->toArray();;
        
        // 获取自定义线路并合并到线路列表前面
        $lines = array_merge($this->getCustomLines(), $lines);

        if (!$lines || count($lines) == 0) {
            Cache::set($title, $datas, 60); // 缓存结果60秒
            Cache::delete($title . '_processing'); // 解锁
            return !empty($param) ? $datas : jok('临时资源获取成功', $datas);
        }

        foreach ($lines as $line) {
            if ($num_success >= $num_total && $num_success_zc >= $num_total_zc) {
                break;
            }
            $result = [];
            $type = $line['type'] ?? 'api';
            if ($type === 'tg') {
                $result = $this->handleTg($line, $title);
            } else if ($type === 'api') {
                $result = $this->handleApi($line, $title);
            } else if ($type === 'html') {
                $result = $this->handleWeb($line, $title);
            } else if ($type === 'kk') {
                $result = $this->handleKk($line, $title, $line['num']);
            }

            foreach ($result as $item) {
                if ($num_success < $num_total) {
                    //检测是否有效
                    $infoData = $this->verificationUrl($item['url']);
                    if (!empty($infoData['stoken'])) {
                        $item['stoken'] = $infoData['stoken'];
                    }
                    if ($infoData !== 0) {
                        if (!$this->urlExists($searchList, $item['url'])) {
                            $searchList[] = $item;
                            $this->processUrl($item, $num_success, $datas);
                        }
                    }
                }else if($num_success_zc < $num_total_zc){
                    //检测是否有效
                    $infoData = $this->verificationUrl($item['url']);
                    if (!empty($infoData['stoken'])) {
                        $item['stoken'] = $infoData['stoken'];
                    }
                    if ($infoData !== 0) {
                        if (!$this->urlExists($searchList, $item['url'])) {
                            $titles = array_column($searchList, 'title');
                            if (!in_array($item['title'], $titles)) {
                                $searchList[] = $item;
                                $datas_zc[] = $item;
                                $num_success_zc++;
                            }
                        } 
                    }
                }
            }
        }
        Cache::set($title, $datas, 60); // 缓存结果60秒
        Cache::delete($title . '_processing'); // 解锁

        if($typeV == 1){
            $datas = array_merge($datas, $datas_zc);
        }
        
        return !empty($param) ? $datas : jok('临时资源获取成功', $datas);
    }

    /**
     * 获取自定义线路配置
     * @return array 自定义线路数组
     */
    private function getCustomLines()
    {
        // 自定义线路 - 线路一
        // $customLines = array_map(function ($i) {
        //     return [
        //         'name' => '自定义线路一',
        //         'pantype' => 0,
        //         'type' => 'kk',
        //         'count' => 5,
        //         'num' => $i,
        //     ];
        // }, range(1, 6));
        
        // 可以在这里添加更多自定义线路
        // 例如：
        /*
        $customLines[] = [
            'name' => '自定义线路二',
            'pantype' => 0,
            'type' => 'GG',
            'count' => 5,
        ];
        */
        return $customLines??[];
    }

    /**
     * 接口类型处理
     */
    private function handleApi($line, $title)
    {
        $type = $line['pantype'];
        $maxCount = $line['count'];

        // 根据类型选择搜索参数
        $panType = [
            0 => 'quark',   // 夸克
            2 => 'baidu'    // 百度
        ];
        
        if (!isset($panType[$type]) || $maxCount <= 0) {
            return [];
        }

        $url     = $line['url'];
        $method  = strtoupper($line['method']);
        $headers = json_decode($line['headers'], true) ?? [];
        $params  = json_decode($line['fixed_params'], true) ?? [];

        // 替换 {keyword}
        foreach ($params as &$val) {
            $val = str_replace('{keyword}', $title, $val);
        }

        // headers 转为 curl 格式
        $headerArr = [];
        foreach ($headers as $k => $v) {
            $headerArr[] = "$k: $v";
        }

        // 确保POST请求有正确的Content-Type
        if ($method === 'POST' && !isset($headers['Content-Type'])) {
            $headerArr[] = "Content-Type: application/x-www-form-urlencoded";
        }

        // 简化参数处理
        $queryParams = $method === 'GET' ? $params : [];
        
        // 处理POST数据
        if ($method === 'POST' && !empty($params)) {
            $postData = http_build_query($params);
            $result = curlHelper($url, $method, $postData, $headerArr, $queryParams);
        } else {
            $result = curlHelper($url, $method, $method === 'GET' ? null : $params, $headerArr, $queryParams);
        }

        if (empty($result['body'])) {
            return [];
        }

        $fieldMap = json_decode($line['field_map'], true);
        $response = json_decode($result['body'], true);
        $results = $this->extractList($response, $fieldMap, $type);

        return array_slice($results, 0, $maxCount);
    }

    /**
     * 提取字段
     */
    protected function extractList($response, $fieldMap, $type)
    {
        $listPath = explode('.', $fieldMap['list_path'] ?? '');
        $listData = $response;
        foreach ($listPath as $key) {
            if (isset($listData[$key])) {
                $listData = $listData[$key];
            } else {
                return [];
            }
        }

        $fields = $fieldMap['fields'] ?? [];
        $result = [];
        foreach ($listData as $item) {
            $row = [];
            foreach ($fields as $targetKey => $sourcePath) {
                $value = $item;
                foreach (explode('.', $sourcePath) as $p) {
                    $value = $value[$p] ?? null;
                }
                $row[$targetKey] = $value;

                if($targetKey=='url'){
                    // 将任何类型的值转换为字符串
                    $stringValue = '';
                    
                    if(is_array($value)) {
                        // 原始数组转字符串
                        $stringValue = json_encode($value, JSON_UNESCAPED_UNICODE);
                        // JSON 中链接中的 / 会变成 \/，替换回来
                        $stringValue = str_replace('\/', '/', $stringValue);
                    } else {
                        $stringValue = (string)$value;
                    }

                    // 从字符串中提取夸克网盘链接
                    if($type === 0 && preg_match('/https:\/\/pan\.quark\.cn\/s\/[a-zA-Z0-9]+/', $stringValue, $urlMatch)) {
                        $row['url'] = trim($urlMatch[0]);
                    } 
                    // 从字符串中提取百度网盘链接
                    else if($type === 2 && preg_match('/https:\/\/pan\.baidu\.com\/s\/[a-zA-Z0-9_-]+(\?pwd=[a-zA-Z0-9]+)?/', $stringValue, $urlMatch)) {
                        $row['url'] = trim($urlMatch[0]);

                        // 检查URL中是否已有pwd参数，如果没有但字符串中有pwd字段，则添加
                        if(!strpos($row['url'], '?pwd=') && preg_match('/["\'](pwd|code)["\']\s*:\s*["\']([^"\']+)["\']/', $stringValue, $pwdMatches)) {
                            $row['url'] .= '?pwd=' . $pwdMatches[2];
                        }
                    }
                    else {
                        $row['url'] = '';
                    }
                }
            }
            if(!empty($row['url'])){
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * TG频道类型处理
     */
    private function handleTg($line, $title)
    {
        $type = $line['pantype'];
        $maxCount = $line['count'];

        // 根据类型选择搜索参数
        $panType = [
            0 => 'quark',   // 夸克
            2 => 'baidu'    // 百度
        ];
        
        if (!isset($panType[$type]) || $maxCount <= 0) {
            return [];
        }

        $results = [];
        $url = 'https://t.me/s/'.$line['url'].'?q='.urlencode($title);
        $dom = getDom($url);
        $finder = new \DomXPath($dom);

        $nodes = $finder->query('//div[contains(@class, "tgme_widget_message_text")]');

        foreach ($nodes as $node) {
            // 获取 HTML 内容
            $htmlContent = $dom->saveHTML($node);
        
            // 提取标题（名称：xxx）
            if (preg_match('/名称：(.+?)<br/i', $htmlContent, $titleMatch)) {
                $parsedItem['title'] = trim(strip_tags($titleMatch[1]));
            } else {
                $parsedItem['title'] = $title;
            }
        
            // 提取夸克链接（可支持百度扩展）
            $parsedItem['url'] = '';
            if ($type === 0 && preg_match('/https:\/\/pan\.quark\.cn\/s\/[a-zA-Z0-9]+/', $htmlContent, $urlMatch)) {
                $parsedItem['url'] = trim($urlMatch[0]);
            } elseif ($type === 2 && preg_match('/https:\/\/pan\.baidu\.com\/s\/[a-zA-Z0-9_-]+(\?pwd=[a-zA-Z0-9]+)?/', $htmlContent, $urlMatch)) {
                $parsedItem['url'] = trim($urlMatch[0]);
            }
        
            // 过滤不合法或无效链接
            if ($parsedItem['title'] && $parsedItem['url']) {
                $results[] = $parsedItem;
            }
        
            if (count($results) >= $maxCount) {
                return $results;
            }
        }
        return $results;
    }


    /**
     * 网页类型处理
     */
    private function handleWeb($line, $title)
    {
        $results = [];

        // 替换搜索关键词并获取配置参数
        $url = str_replace('{keyword}', urlencode($title), $line['url']);

        $parts = explode('+', $line['html_item'], 2);
        $tag = $parts[0] ?? '';
        $classString = $parts[1] ?? '';

        $partsTitle = explode('+', $line['html_title'], 2);
        $tagTitle = $partsTitle[0] ?? '';
        $classStringTitle = $partsTitle[1] ?? '';

        $partsUrl = explode('+', $line['html_url2'], 2);
        $tagUrl = $partsUrl[0] ?? '';
        $classStringUrl = $partsUrl[1] ?? '';

        $maxCount = $line['count'] ?? 10;
        $type = $line['pantype'];

        // 定义网盘链接匹配规则
        $panPatterns = [
            0 => '/https:\/\/pan\.quark\.cn\/s\/[a-zA-Z0-9]+/', // 夸克
            2 => '/https:\/\/pan\.baidu\.com\/s\/[a-zA-Z0-9_-]+(\?pwd=[a-zA-Z0-9]+)?/' // 百度（包含提取码）
        ];

        // 获取DOM并设置XPath查询
        $dom = getDom($url);
        if (!$dom) {
            return $results;
        }
        
        $finder = new \DomXPath($dom);
        $xpath = $this->buildXPathQuery($tag, $classString);
        $nodes = $finder->query($xpath);
        
        foreach ($nodes as $node) {
            if (count($results) >= $maxCount) {
                break;
            }
            
            $html = $dom->saveHTML($node);
            $item = [
                'title' => '',
                'url'   => '',
            ];

            // 提取资源标题
            $item['title'] = $this->extractTitle($html, $tagTitle, $classStringTitle);
            
            // 尝试直接从当前HTML中提取网盘链接
            if (preg_match($panPatterns[$type], $html, $match)) {
                $item['url'] = trim($match[0]);
            } else {
                // 根据配置决定是否需要进入详情页
                if ($line['html_type'] == 1) {
                    $item['url'] = $this->extractUrlFromDetailPage($html, $line, $url, $tagUrl, $classStringUrl, $panPatterns[$type]);
                } else {
                    $item['url'] = $this->extractUrlFromListPage($html, $tagUrl, $classStringUrl, $panPatterns[$type]);
                }
            }

            // 只添加同时有标题和URL的结果
            if ($item['title'] && $item['url']) {
                $results[] = $item;
            }
        }

        return $results;
    }

    /**
     * 构建XPath查询语句
     * 
     * @param string $tag 标签名
     * @param string $classString 类名字符串
     * @return string XPath查询语句
     */
    private function buildXPathQuery($tag, $classString)
    {
        $classArray = explode(' ', trim($classString));
        $xpathConditions = [];
        foreach ($classArray as $cls) {
            if (!empty($cls)) {
                $xpathConditions[] = "contains(concat(' ', normalize-space(@class), ' '), ' {$cls} ')";
            }
        }
        
        return "//{$tag}" . (empty($xpathConditions) ? "" : "[" . implode(' and ', $xpathConditions) . "]");
    }

    /**
     * 从HTML中提取标题
     * 
     * @param string $html HTML内容
     * @param string $tagTitle 标题标签
     * @param string $classStringTitle 标题类名
     * @return string 提取的标题
     */
    private function extractTitle($html, $tagTitle, $classStringTitle)
    {
        // 尝试匹配"名称：xxx 描述："格式
        if (preg_match('/名称：(.*?)\n\n描述：/s', $html, $match)) {
            return trim(strip_tags($match[1]));
        }
        
        // 尝试根据标签和类名匹配
        $escapedClass = preg_quote($classStringTitle, '#');
        $escapedTag = preg_quote($tagTitle, '#');
        $pattern = '#<'.$escapedTag.'[^>]*class=["\'][^"\']*' . $escapedClass . '[^"\']*["\'][^>]*>(.*?)</'.$escapedTag.'>#s';
        
        if (preg_match($pattern, $html, $titleMatch)) {
            return trim(strip_tags($titleMatch[1]));
        }
        
        return '';
    }

    /**
     * 从详情页提取URL
     * 
     * @param string $html 列表页HTML
     * @param array $line 配置信息
     * @param string $baseUrl 基础URL
     * @param string $tagUrl URL标签
     * @param string $classStringUrl URL类名
     * @param string $panPattern 网盘链接匹配模式
     * @return string 提取的URL
     */
    private function extractUrlFromDetailPage($html, $line, $baseUrl, $tagUrl, $classStringUrl, $panPattern)
    {
        list($tagD, $classStringD) = explode('+', $line['html_url'], 2);
        
        // 构建匹配详情页链接的正则表达式
        $detailUrlPattern = $this->buildHrefPattern($tagD, $classStringD);
        
        if (!preg_match($detailUrlPattern, $html, $match)) {
            return '';
        }
        
        // 处理相对URL
        $detailUrl = trim($match[1]);
        $fullDetailUrl = $this->buildFullUrl($detailUrl, $baseUrl);
        
        // 获取详情页内容
        $dom2 = getDom($fullDetailUrl);
        if (!$dom2) {
            return '';
        }
        
        $finder2 = new \DomXPath($dom2);
        $xpath2 = $this->buildXPathQuery($tagUrl, $classStringUrl);
        $nodes2 = $finder2->query($xpath2);
        
        // 遍历详情页节点查找网盘链接
        foreach ($nodes2 as $node2) {
            $html2 = $dom2->saveHTML($node2);
            
            // 尝试从内容中提取
            $escapedClass = preg_quote($classStringUrl, '#');
            $escapedTag = preg_quote($tagUrl, '#');
            $contentPattern = '#<'.$escapedTag.'[^>]*class=["\'][^"\']*' . $escapedClass . '[^"\']*["\'][^>]*>(.*?)</'.$escapedTag.'>#s';
            
            if (preg_match($contentPattern, $html2, $titleMatch)) {
                $extractedUrl = trim(strip_tags($titleMatch[1]));
                if (preg_match($panPattern, $extractedUrl, $urlMatch)) {
                    return trim($urlMatch[0]);
                }
            }
            
            // 尝试从href属性中提取
            $hrefPattern = $this->buildHrefPattern($tagUrl, $classStringUrl);
            if (preg_match($hrefPattern, $html2, $match)) {
                $extractedUrl = trim($match[1]);
                if (preg_match($panPattern, $extractedUrl, $urlMatch)) {
                    return trim($urlMatch[0]);
                }
            }
        }
        
        return '';
    }

    /**
     * 从列表页直接提取URL
     * 
     * @param string $html HTML内容
     * @param string $tagUrl URL标签
     * @param string $classStringUrl URL类名
     * @param string $panPattern 网盘链接匹配模式
     * @return string 提取的URL
     */
    private function extractUrlFromListPage($html, $tagUrl, $classStringUrl, $panPattern)
    {
        // 尝试从内容中提取
        $escapedClass = preg_quote($classStringUrl, '#');
        $escapedTag = preg_quote($tagUrl, '#');
        $contentPattern = '#<'.$escapedTag.'[^>]*class=["\'][^"\']*' . $escapedClass . '[^"\']*["\'][^>]*>(.*?)</'.$escapedTag.'>#s';
        
        if (preg_match($contentPattern, $html, $titleMatch)) {
            $extractedUrl = trim(strip_tags($titleMatch[1]));
            if (preg_match($panPattern, $extractedUrl, $urlMatch)) {
                return trim($urlMatch[0]);
            }
        }
        
        // 尝试从href属性中提取
        $hrefPattern = $this->buildHrefPattern($tagUrl, $classStringUrl);
        if (preg_match($hrefPattern, $html, $match)) {
            $extractedUrl = trim($match[1]);
            if (preg_match($panPattern, $extractedUrl, $urlMatch)) {
                return trim($urlMatch[0]);
            }
        }
        
        return '';
    }

    /**
     * 构建 href 属性匹配模式，支持 class 和 href 顺序不固定
     *
     * @param string $tag 标签名
     * @param string $classString 类名（可为空）
     * @return string 正则表达式
     */
    private function buildHrefPattern($tag, $classString)
    {
        $escapedClass = preg_quote($classString, '#');
        $escapedTag = preg_quote($tag, '#');

        if (empty($escapedClass)) {
            // 没有类名要求，只匹配标签中包含 href 的内容
            return '#<' . $escapedTag . '\b[^>]*href=["\']([^"\']+)["\'][^>]*>#i';
        } else {
            // 匹配包含指定 class 的标签，不要求 href 和 class 的顺序
            return '#<' . $escapedTag . '\b(?=[^>]*class=["\'][^"\']*' . $escapedClass . '[^"\']*["\'])(?=[^>]*href=["\']([^"\']+)["\'])[^>]*>#i';
        }
    }


    /**
     * 构建完整URL
     * 
     * @param string $url 可能是相对URL
     * @param string $baseUrl 基础URL
     * @return string 完整URL
     */
    private function buildFullUrl($url, $baseUrl)
    {
        if (strpos($url, 'http') !== 0) {
            $parsed = parse_url($baseUrl);
            $base = $parsed['scheme'] . '://' . $parsed['host'];
            return $base . $url;
        }
        return $url;
    }

    /**
     * 自定义接口
     */
    private function handleKk($line, $title, $apiType = 0)
    {
        $type = $line['pantype'];
        $maxCount = $line['count'];

        $url2 = [];
        $urlDefault = "https://m.kkkba.com";

        // 网盘链接匹配正则
        $pattern = [
            0 => '/https:\/\/pan\.quark\.cn\/[^\s]+/',   // 夸克
            2 => '/https:\/\/pan\.baidu\.com\/[^\s]+/',  // 百度
        ];
        
        if (!isset($pattern[$type])) {
            return [];
        }

        try {
            $res = curlHelper($urlDefault."/v/api/getToken", "GET", null, [], "", "", 5)['body'] ?? null;
            if (!$res) return $url2;
        } catch (Exception $err) {
            return $url2;
        }

        $res = json_decode($res, true);
        $token = $res['token'] ?? '';
        if(empty($token)){
            return $url2;
        }

        // 所有接口列表
        $allApiList = [
            1 => "/v/api/getJuzi",
            2 => "/v/api/search",
            // 3 => "/v/api/getXiaoyu",
            // 4 => "/v/api/getDJ",
            // 5 => "/v/api/getKK"
        ];
        
        // 根据 apiType 确定要调用的接口列表
        if ($apiType == 0) {
            // 全部接口
            $apiList = array_values($allApiList);
        } elseif (isset($allApiList[$apiType])) {
            // 指定某个接口
            $apiList = [$allApiList[$apiType]];
        } else {
            // 错误类型，直接返回空
            return [];
        }

        // 请求头
        $urlData = array(
            'name' => $title, 
            'token' => $token
        );
        $headers = ['Content-Type: application/json'];

        foreach ($apiList as $apiUrl) {
            try {
                $response = curlHelper($urlDefault.$apiUrl, "POST", json_encode($urlData), $headers, "", "", 5);
                $res = isset($response['body']) ? json_decode($response['body'], true) : null;
            } catch (Exception $err) {
                continue;
            }

            if (empty($res['list']) || !is_array($res['list'])) {
                continue;
            }
            foreach ($res['list'] as $value) {
                if (preg_match($pattern[$type], $value['answer'], $matches)) {
                    $link = $matches[0];
                    if (preg_match('/提取码[:：]?\s*([a-zA-Z0-9]{4})/', $value['answer'], $codeMatch)) {
                        $link .= '?pwd=' . $codeMatch[1];
                    }
                    $titleText = preg_replace('/\s*[\(（]?(夸克|百度)?[\)）]?\s*/u', '', $value['answer']??'');
                    $url2[] = [
                        'title' => $titleText,
                        'url' => $link
                    ];
                    if (count($url2) >= $maxCount) {
                        return $url2;
                    }
                }
            }
        }

        return $url2;
    }

    /**
     * 验证夸克地址是否有效
     * @return array
     */
    private function verificationUrl($url) {
        $code = '';
        if (preg_match('/\?pwd=([^,\s&]+)/', $url, $pwdMatch)) {
            $code = trim($pwdMatch[1]);
        }
        $urlData = [
            'url' => $url,
            'code' => $code,
            'isType' => 1
        ];

        $transfer = new \netdisk\Transfer();
        $res = $transfer->transfer($urlData);

        if ($res['code'] !== 200) {
            return 0;
        }
        
        return $res['data'];
    }

    /**
     * 解密url并转存
     * @return void
     */
    public function save_url()
    {   
        $value = [
            'title'  => input('title', ''),
            'url'    => urldecode(input('url', '')),
            'stoken' => input('stoken', ''),
        ];
        $value['url'] = decryptObject($value['url']);
        
        if (empty($value['title']) || empty($value['url'])) {
            return jerr("参数不对");
        }

        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];
        $map[] = ['is_time', '=', 1];
        $map[] = ['content', '=', $value['url']];
            
        $url = $this->model->where($map)->field('source_id as id, title, url')->find();
        if (!empty($url)) {
            $this->model->where('source_id', $url['id'])->update(['update_time' => time()]);
            unset($url['id']);
            return jok('临时资源获取成功', $url);
        }

        //同一个搜索内容锁机
        $keys = $value['url'].'ACAA';
        if (Cache::has($keys)) {
            // 检查缓存中是否已有结果
            return jok('临时资源获取成功', Cache::get($keys));
        }

        // 检查是否有正在处理的请求
        if (Cache::has($keys . '_processing')) {
            // 如果当前正在处理相同关键词的请求，等待结果
            $startTime = time(); // 记录开始时间
            while (Cache::has($keys . '_processing')) {
                usleep(1000000); // 暂停1秒
        
                // 检查是否超过60秒
                if (time() - $startTime > 60) {
                    return jok('临时资源获取成功', []);
                }
            }
            return jok('临时资源获取成功', Cache::get($keys));
        }

        // 设置处理状态为正在处理
        Cache::set($keys . '_processing', true, 60); // 锁定60秒

        $datas = [];
        $num_total = 1;
        $num_success = 0;
        $res = $this->processUrl($value, $num_success, $datas, true);

        Cache::delete($keys . '_processing'); // 解锁

        if($res['code'] !== 200){
            return jerr($res['message']);
        }else{
            $result['title'] = $res['data']['title'];
            $result['url'] = $res['data']['url'];
            Cache::set($keys, $result, 60); // 缓存结果60秒
            return jok('临时资源获取成功', $result);
        }
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
    public function processUrl($value, &$num_success, &$datas, $type=false) 
    {
        $substring = strstr($value['url'], 's/');
        if ($substring === false) {
            if($type){
                return jerr2("资源地址格式有误");
            }else{
                return; // 模拟 continue 行为
            }
        }
        
        $code = '';
        if (preg_match('/\?pwd=([^,\s&]+)/',$value['url'], $pwdMatch)) {
            $code = trim($pwdMatch[1]);
        }
        
        $urlData = array(
            'url' => $value['url'],
            'code' => $code,
            'expired_type' => 2,
            'ad_fid' => '', //分享时带上这个文件
        );

        $transfer = new \netdisk\Transfer();
        $res = $transfer->transfer($urlData);

        if($res['code'] !== 200){
            if($type){
                return jerr2($res['message']);
            }else{
                return; // 模拟 continue 行为
            }
        }
    
        $patterns = '/^\d+\./';
        $title = preg_replace($patterns, '', $value['title']);
        // 添加资源到系统中
        $data["title"] =$title;
        $data["url"] =$res['data']['share_url'];
        $data["is_type"] = determineIsType($data["url"]);
        $data["content"] =$value['url'];
        $dataFid = $res['data']['fid']??'';
        $data["fid"] = is_array($dataFid) ? json_encode($dataFid) : $dataFid;
        $data["is_time"] = 1;
        $data["update_time"] = time();
        $data["create_time"] = time();
        $data["id"] = $this->model->insertGetId($data);
        $datas[] =$data;
        $num_success++;

        if($type){
            return jok2('转存成功',$data);
        }
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
