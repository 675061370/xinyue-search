<?php
namespace util;

class Network
{
    protected $errorCodes = [
        -1 => '链接错误，链接失效或缺少提取码',
        -4 => '转存失败，无效登录。请退出账号在其他地方的登录',
        -6 => '转存失败，请用浏览器无痕模式获取 Cookie 后再试',
        -7 => '转存失败，转存文件夹名有非法字符，不能包含 < > | * ? \\ :，请改正目录名后重试',
        -8 => '转存失败，目录中已有同名文件或文件夹存在',
        -9 => '链接错误，提取码错误',
        -10 => '转存失败，容量不足',
        -12 => '链接错误，提取码错误',
        -62 => '转存失败，链接访问次数过多，请手动转存或稍后再试',
        0 => '转存成功',
        2 => '转存失败，目标目录不存在',
        4 => '转存失败，目录中存在同名文件',
        12 => '转存失败，转存文件数超过限制',
        20 => '转存失败，容量不足',
        105 => '链接错误，所访问的页面不存在'
    ];

    public function getErrorMessage($code)
    {
        return $this->errorCodes[$code] ?? "未知错误（错误码：{$code}）";
    }

    protected $session;
    protected $headers;
    protected $bdstoken;
    protected $baseUrl;
    protected $cookie;

    public function __construct($cookie = '')
    {
        $this->cookie = $cookie??'';
        $this->headers = [
            'Host: pan.baidu.com',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'Sec-Fetch-Dest: document',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Sec-Fetch-Site: same-site', 
            'Sec-Fetch-Mode: navigate',
            'Referer: https://pan.baidu.com',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-US;q=0.7,en-GB;q=0.6,ru;q=0.5',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
            'Cookie: ' . $cookie
        ];
        $this->bdstoken = '';
        $this->baseUrl = 'https://pan.baidu.com';
    }

    public function getBdstoken()
    {
        $url = $this->baseUrl . '/api/gettemplatevariable';
        $params = [
            'clienttype' => '0',
            'app_id' => '38824127',
            'web' => '1',
            'fields' => '["bdstoken","token","uk","isdocuser","servertime"]'
        ];

        $res = $this->request('GET', $url, $params);
        if ($res['errno'] != 0) {
            return $res['errno'];
        }

        return $res['result']['bdstoken'];
    }

    public function getDirList($folderName)
    {
        $url = $this->baseUrl . '/api/list';
        $params = [
            'order' => 'time',
            'desc' => '1',
            'showempty' => '0',
            'web' => '1',
            'page' => '1',
            'num' => '1000',
            'dir' => $folderName,
            'bdstoken' => $this->bdstoken
        ];

        $res = $this->request('GET', $url, $params);
        
        if ($res['errno'] != 0) {
            return $res['errno'];
        }

        return $res['list'];
    }

    public function createDir($folderName)
    {
        $url = $this->baseUrl . '/api/create';
        $params = [
            'a' => 'commit',
            'bdstoken' => $this->bdstoken
        ];
        $data = [
            'path' => $folderName,
            'isdir' => '1',
            'block_list' => '[]'
        ];

        $res = $this->request('POST', $url, $params, $data);
        return $res['errno'];
    }

    public function verifyPassCode($linkUrl, $passCode)
    {
        $url = $this->baseUrl . '/share/verify';
        $params = [
            'surl' => substr($linkUrl, 25, 23),
            'bdstoken' => $this->bdstoken,
            't' => round(microtime(true) * 1000),
            'channel' => 'chunlei',
            'web' => '1',
            'clienttype' => '0'
        ];
        $data = [
            'pwd' => $passCode,
            'vcode' => '',
            'vcode_str' => ''
        ];

        $res = $this->request('POST', $url, $params, $data);
        if ($res['errno'] != 0) {
            return $res['errno'];
        }

        return $res['randsk'];
    }

    public function updateBdclnd($randsk)
    {
        $this->cookie = $this->updateCookie($randsk, $this->cookie);
        
        // 更新 headers 中的 cookie
        foreach ($this->headers as &$header) {
            if (strpos($header, 'Cookie:') === 0) {
                $header = 'Cookie: ' . $this->cookie;
                break;
            }
        }
    }

    public function getTransferParams($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        // 允许跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \Exception(curl_error($ch));
        }

        // 直接返回原始响应内容，不做 json 解析
        return $this->parseResponse($response);
    }

    public function transferFile($paramsList, $folderName)
    {
        $url = $this->baseUrl . '/share/transfer';
        $params = [
            'shareid' => $paramsList[0],
            'from' => $paramsList[1],
            'bdstoken' => $this->bdstoken,
            'channel' => 'chunlei',
            'web' => '1',
            'clienttype' => '0'
        ];
        $data = [
            'fsidlist' => '[' . implode(',', $paramsList[2]) . ']',
            'path' => '/' . $folderName
        ];

        $res = $this->request('POST', $url, $params, $data);
        return $res['errno'];
    }

    public function createShare($fsId, $expiry, $password)
    {
        $url = $this->baseUrl . '/share/set';
        $params = [
            'channel' => 'chunlei',
            'bdstoken' => $this->bdstoken,
            'clienttype' => '0',
            'app_id' => '250528',
            'web' => '1'
        ];
        $data = [
            'period' => $expiry,
            'pwd' => $password,
            'eflag_disable' => 'true',
            'channel_list' => '[]',
            'schannel' => '4',
            'fid_list' => '[' . $fsId . ']'
        ];

        $res = $this->request('POST', $url, $params, $data);
        if ($res['errno'] != 0) {
            return $res['errno'];
        }

        return $res['link'];
    }

    public function setBdstoken($token)
    {
        $this->bdstoken = $token;
    }

    protected function request($method, $url, $params = [], $data = null, $retry = 3)
    {
        while ($retry > 0) {
            try {
                $ch = curl_init();
                if ($method === 'GET' && !empty($params)) {
                    $url .= '?' . http_build_query($params);
                }
                
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');

                if ($method === 'POST') {
                    curl_setopt($ch, CURLOPT_POST, true);
                    if (!empty($params)) {
                        $url .= '?' . http_build_query($params);
                        curl_setopt($ch, CURLOPT_URL, $url);
                    }
                    if (!empty($data)) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    }
                }

                $response = curl_exec($ch);
                curl_close($ch);

                if ($response === false) {
                    throw new \Exception(curl_error($ch));
                }

                return json_decode($response, true);
            } catch (\Exception $e) {
                $retry--;
                if ($retry <= 0) {
                    throw $e;
                }
                usleep(rand(1000000, 2000000)); // 1-2秒随机延迟
            }
        }
    }

    protected function updateCookie($bdclnd, $cookie)
    {
        // 拆分 cookie 字符串到数组
        $cookiePairs = array_filter(explode(';', $cookie));
        $cookiesDict = [];
        
        // 将 cookie 键值对转换为关联数组
        foreach ($cookiePairs as $pair) {
            $parts = explode('=', trim($pair), 2);
            if (count($parts) == 2) {
                $cookiesDict[$parts[0]] = $parts[1];
            }
        }
        
        // 更新或添加 BDCLND 值
        $cookiesDict['BDCLND'] = $bdclnd;
        
        // 重新构建 cookie 字符串
        $cookieParts = [];
        foreach ($cookiesDict as $key => $value) {
            $cookieParts[] = $key . '=' . $value;
        }
        
        return implode('; ', $cookieParts);
    }

    protected function parseResponse($response)
    {
        // 预定义正则表达式
        $patterns = [
            'shareid' => '/"shareid":(\d+?),"/',
            'user_id' => '/"share_uk":"(\d+?)","/',
            'fs_id' => '/"fs_id":(\d+?),"/',
            'server_filename' => '/"server_filename":"(.+?)","/',
            'isdir' => '/"isdir":(\d+?),"/'
        ];

        // 提取所有需要的参数
        $results = [];
        foreach ($patterns as $key => $pattern) {
            preg_match_all($pattern, $response, $matches);
            $results[$key] = $matches[1] ?? [];
        }

        // 验证是否获取到所有必要参数
        if (empty($results['shareid']) || empty($results['user_id']) || 
            empty($results['fs_id']) || empty($results['server_filename']) || 
            empty($results['isdir'])) {
            return -1;
        }

        // 返回格式化的结果
        return [
            $results['shareid'][0],           // shareid
            $results['user_id'][0],           // user_id
            $results['fs_id'],                // fs_id 列表
            array_unique($results['server_filename']), // 文件名列表（去重）
            $results['isdir']                 // 是否为目录
        ];
    }

    public function deleteFile($filePath)
    {
        $url = $this->baseUrl . '/api/filemanager';
        $params = [
            'async' => '2',
            'onnest' => 'fail',
            'opera' => 'delete',
            'bdstoken' => $this->bdstoken,
            'newVerify' => '1',
            'clienttype' => '0',
            'app_id' => '250528',
            'web' => '1'
        ];

        // 支持单个路径字符串或路径数组
        if (!is_array($filePath)) {
            $filePath = [$filePath];
        }

        $data = [
            'filelist' => json_encode($filePath)
        ];

        $res = $this->request('POST', $url, $params, $data);
        return $res['errno'];
    }

    /**
         * 批量删除文件
         * @param array|string $filePaths 文件路径数组或单个文件路径
         * @return array 删除结果
         */
        public function batchDeleteFiles($filePaths)
        {
            // 如果是字符串，转换为数组处理
            if (!is_array($filePaths)) {
                $filePaths = [$filePaths];
            }
            
            // 处理每个路径
            $processedPaths = [];
            foreach ($filePaths as $path) {
                if (empty($path)) {
                    continue;
                }
                
                // 防止删除根目录
                if ($path === '/' || $path === '') {
                    continue;
                }
                
                // 确保路径以斜杠开头
                if (substr($path, 0, 1) !== '/') {
                    $path = '/' . $path;
                }
                
                $processedPaths[] = $path;
            }
            
            if (empty($processedPaths)) {
                return [
                    'errno' => -100,
                    'message' => '没有有效的文件路径可删除'
                ];
            }
            
            // 调用删除方法
            $result = $this->deleteFile($processedPaths);
            
            return [
                'errno' => $result,
                'message' => $this->getErrorMessage($result),
                'deletedCount' => count($processedPaths),
                'paths' => $processedPaths
            ];
        }
}