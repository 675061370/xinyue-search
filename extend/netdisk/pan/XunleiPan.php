<?php

namespace netdisk\pan;

use think\facade\Db;

class XunleiPan extends BasePan
{
    private $clientId = 'Xqp0kJBXWhwaTpB6';
    private $deviceId = '925b7631473a13716b791d7f28289cad';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->urlHeader = [
            'Accept: */*',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: zh-CN,zh;q=0.9',
            'Cache-Control: no-cache',
            'Content-Type: application/json',
            'Origin: https://pan.xunlei.com',
            'Pragma: no-cache',
            'Priority: u=1,i',
            'Referer: https://pan.xunlei.com/',
            'sec-ch-ua: "Not;A=Brand";v="99", "Google Chrome";v="139", "Chromium";v="139"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-site',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',
            'Authorization: ',
            'x-captcha-token: ',
            'x-client-id: ' . $this->clientId,
            'x-device-id: ' . $this->deviceId,
        ];
    }

    /**
     * ✅ 核心方法：获取 Access Token（内部包含缓存判断、刷新、保存）
     */
    private function getAccessToken()
    {
        $tokenFile = __DIR__ . '/xunlei_token.json';

        // 1️⃣ 先读取缓存
        if (file_exists($tokenFile)) {
            $data = json_decode(file_get_contents($tokenFile), true);
            if (isset($data['access_token'], $data['expires_at']) && time() < $data['expires_at']) {
                return $data['access_token']; // 缓存有效
            }
        }

        // 2️⃣ 构造请求体
        $body = [
            'client_id' => $this->clientId,
            'grant_type' => 'refresh_token',
            'refresh_token' => Config('qfshop.xunlei_cookie')
        ];

        // 3️⃣ 构造请求头（直接传入，不用处理 Authorization/x-captcha-token）
        $headers = array_filter($this->urlHeader, function ($h) {
            return strpos($h, 'Authorization') === false && strpos($h, 'x-captcha-token') === false;
        });

        // 4️⃣ 调用封装请求方法
        $res = $this->requestXunleiApi(
            'https://xluser-ssl.xunlei.com/v1/auth/token',
            'POST',
            $body,
            [],      // GET 参数为空
            $headers // headers 直接传入
        );

        // 5️⃣ 判断返回
        if ($res['code'] !== 0 || !isset($res['data']['access_token'])) {
            return ''; // 获取失败
        }

        $resData = $res['data'];

        // 6️⃣ 计算过期时间（当前时间 + expires_in - 60 秒缓冲）
        $expiresAt = time() + intval($resData['expires_in']) - 60;

        // 7️⃣ 缓存到文件
        file_put_contents($tokenFile, json_encode([
            'access_token'  => $resData['access_token'],
            'refresh_token' => $resData['refresh_token'],
            'expires_at'    => $expiresAt
        ]));

        // 8️⃣ 同步刷新 refresh_token 到数据库
        Db::name('conf')->where('conf_key', 'xunlei_cookie')->update([
            'conf_value' => $resData['refresh_token']
        ]);

        // 9️⃣ 返回 token
        return $resData['access_token'];
    }


    /**
     * ✅ 获取 captcha_token
     */
    private function getCaptchaToken()
    {
        $tokenFile = __DIR__ . '/xunlei_captcha.json';

        // 1️⃣ 先读取缓存
        if (file_exists($tokenFile)) {
            $data = json_decode(file_get_contents($tokenFile), true);
            if (isset($data['captcha_token']) && isset($data['expires_at'])) {
                if (time() < $data['expires_at']) {
                    return $data['captcha_token']; // 缓存有效
                }
            }
        }

        // 2️⃣ 构造请求体
        $body = [
            'client_id' => $this->clientId,
            'action' => "get:/drive/v1/share",
            'device_id' => $this->deviceId,
            'meta' => [
                'username' => '',
                'phone_number' => '',
                'email' => '',
                'package_name' => 'pan.xunlei.com',
                'client_version' => '1.45.0',
                'captcha_sign' => '1.fe2108ad808a74c9ac0243309242726c',
                'timestamp' => '1645241033384',
                'user_id' => '0'
            ]
        ];

        // 3️⃣ 构造请求头
        $headers = [
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ];

        // 4️⃣ 调用封装请求方法
        $res = $this->requestXunleiApi(
            "https://xluser-ssl.xunlei.com/v1/shield/captcha/init",
            'POST',
            $body,
            [],      // GET 参数为空
            $headers // headers 传入即用
        );

        if ($res['code'] !== 0 || !isset($res['data']['captcha_token'])) {
            return ''; // 获取失败
        }

        $data = $res['data'];

        // 5️⃣ 计算过期时间（当前时间 + expires_in - 10 秒缓冲）
        $expiresAt = time() + intval($data['expires_in']) - 10;

        // 6️⃣ 缓存到文件
        file_put_contents($tokenFile, json_encode([
            'captcha_token' => $data['captcha_token'],
            'expires_at' => $expiresAt
        ]));

        return $data['captcha_token'];
    }


    public function getFiles($pdir_fid = '')
    {
        // 1️⃣ 获取 AccessToken
        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            return jerr2('登录状态异常，获取accessToken失败');
        }

        // 2️⃣ 获取 CaptchaToken
        $captchaToken = $this->getCaptchaToken();
        if (empty($captchaToken)) {
            return jerr2('获取 captchaToken 失败');
        }

        // 3️⃣ 构造 headers
        $headers = array_map(function ($h) use ($accessToken, $captchaToken) {
            if (str_starts_with($h, 'Authorization: ')) {
                return 'Authorization: Bearer ' . $accessToken;
            }
            if (str_starts_with($h, 'x-captcha-token: ')) {
                return 'x-captcha-token: ' . $captchaToken;
            }
            return $h;
        }, $this->urlHeader);

        // 4️⃣ 构造请求体和 GET 参数
        $filters = [
            "phase" => ["eq" => "PHASE_TYPE_COMPLETE"],
            "trashed" => ["eq" => false],
        ];

        $filtersStr = urlencode(json_encode($filters));
        $urlData = [];
        $queryParams = [
            'parent_id'      => $pdir_fid ?: '',
            'filters'       => '{"phase":{"eq":"PHASE_TYPE_COMPLETE"},"trashed":{"eq":false}}',
            'with_audit'     => true,
            'thumbnail_size' => 'SIZE_SMALL',
            'limit'          => 50,
        ];

        // 5️⃣ 调用封装方法请求
        $res = $this->requestXunleiApi(
            "https://api-pan.xunlei.com/drive/v1/files",
            'GET',
            $urlData,
            $queryParams,
            $headers
        );

        // 6️⃣ 检查结果
        if ($res['code'] !== 0 || !isset($res['data']['files'])) {
            return jerr2($res['msg'] ?? '获取文件列表失败');
        }
        return jok2('获取成功', $res['data']['files']);
    }


    public function transfer($pwd_id)
    {
        // 1️⃣ 获取 AccessToken
        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            return jerr2('登录状态异常');
        }

        // 2️⃣ 获取 CaptchaToken
        $captchaToken = $this->getCaptchaToken();
        if (empty($captchaToken)) {
            return jerr2('登录异常');
        }

        // 3️⃣ 构造 headers
        $this->urlHeader = array_map(function ($h) use ($accessToken, $captchaToken) {
            if (str_starts_with($h, 'Authorization: ')) {
                return 'Authorization: Bearer ' . $accessToken;
            }
            if (str_starts_with($h, 'x-captcha-token: ')) {
                return 'x-captcha-token: ' . $captchaToken;
            }
            return $h;
        }, $this->urlHeader);

        $pwd_id = strtok($pwd_id, '?');
        $this->code = str_replace('#', '', $this->code);

        $res = $this->getShare($pwd_id, $this->code);
        if ($res['code'] !== 200) return jerr2($res['message']);
        $infoData = $res['data'];

        if ($this->isType == 1) {
            $urls['title'] = $infoData['title'];
            $urls['share_url'] = $this->url;
            $urls['stoken'] = '';
            return jok2('检验成功', $urls);
        }

        //转存到网盘
        $res = $this->getRestore($pwd_id, $infoData);
        if ($res['code'] !== 200) return jerr2($res['message']);


        //获取转存后的文件信息
        $tasData = $res['data'];
        $retry_index = 0;
        $myData = '';
        while ($myData == '' || $myData['progress'] != 100) {
            $res = $this->getTasks($tasData);
            if ($res['code'] !== 200) return jerr2($res['message']);
            $myData = $res['data'];
            $retry_index++;
            // 可以添加一个最大重试次数的限制，防止无限循环
            if ($retry_index > 20) {
                break;
            }
        }

        if ($myData['progress'] != 100) {
            return jerr2($myData['message'] ?? '转存失败');
        }

        $result = [];
        if (isset($myData['params']['trace_file_ids']) && !empty($myData['params']['trace_file_ids'])) {
            $traceData = json_decode($myData['params']['trace_file_ids'], true);
            if (is_array($traceData)) {
                $result = array_values($traceData);
            }
        }

        try {
            //删除转存后可能有的广告
            $banned = Config('qfshop.quark_banned') ?? ''; //如果出现这些字样就删除
            if (!empty($banned)) {
                $bannedList = explode(',', $banned);
                $pdir_fid = $result[0];
                $dellist = [];
                $plists = $this->getFiles($pdir_fid);
                $plist = $plists['data'];
                if (!empty($plist)) {
                    foreach ($plist as $key => $value) {
                        // 检查$value['name']是否包含$bannedList中的任何一项
                        $contains = false;
                        foreach ($bannedList as $item) {
                            if (strpos($value['name'], $item) !== false) {
                                $contains = true;
                                break;
                            }
                        }
                        if ($contains) {
                            $dellist[] = $value['id'];
                        }
                    }
                    if (count($plist) === count($dellist)) {
                        //要删除的资源数如果和原数据资源数一样 就全部删除并终止下面的分享
                        $this->deletepdirFid([$pdir_fid]);
                        return jerr2("资源内容为空");
                    } else {
                        if (!empty($dellist)) {
                            $this->deletepdirFid($dellist);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }

        //根据share_id  获取到分享链接
        $res = $this->getSharePassword($result);
        if ($res['code'] !== 200) return jerr2($res['message']);


        $title = $infoData['files'][0]['name'] ?? '';
        $share = [
            'title' => $title,
            'share_url' => $res['data']['share_url'] . '?pwd=' . $res['data']['pass_code'],
            'code' => $res['data']['pass_code'],
            'fid' => $result,
        ];

        return jok2('转存成功', $share);
    }

    /**
     * 资源分享信息
     *
     * @return void
     */
    public function getShare($pwd_id, $pass_code)
    {
        $urlData =  [];
        $queryParams = [
            'share_id' => $pwd_id,
            'pass_code' => $pass_code,
            'limit' => 100,
            'pass_code_token' => '',
            'page_token' => '',
            'thumbnail_size' => 'SIZE_SMALL',
        ];
        $res = $this->requestXunleiApi(
            "https://api-pan.xunlei.com/drive/v1/share",
            'GET',
            $urlData,
            $queryParams,
            $this->urlHeader
        );
        if (!empty($res['data']['error_code'])) {
            return jerr2($res['data']['error_description'] ?? 'getShare失败');
        }
        if (isset($res['data']['share_status']) && $res['data']['share_status'] !== 'OK') {
            if (!empty($res['data']['share_status_text'])) {
                return jerr2($res['data']['share_status_text']);
            }

            if ($res['data']['share_status'] === 'SENSITIVE_RESOURCE') {
                return jerr2('该分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！');
            }

            return jerr2('资源已失效');
        }

        return jok2('ok', $res['data']);
    }


    /**
     * 转存到网盘
     *
     * @return void
     */
    public function getRestore($pwd_id, $infoData)
    {
        $parent_id = Config('qfshop.xunlei_file'); //默认存储路径
        if ($this->expired_type == 2) {
            $parent_id = Config('qfshop.xunlei_file_time'); //临时资源路径
        }

        $ids = [];
        if (isset($infoData['files']) && is_array($infoData['files']) && !empty($infoData['files'])) {
            $ids = array_column($infoData['files'], 'id');
        }

        $urlData =  [
            'parent_id' => $parent_id,
            'share_id' => $pwd_id,
            "pass_code_token" => $infoData['pass_code_token'],
            'ancestor_ids' => [],
            'specify_parent_id' => true,
            'file_ids' => $ids,
        ];
        $queryParams = [];
        $res = $this->requestXunleiApi(
            "https://api-pan.xunlei.com/drive/v1/share/restore",
            'POST',
            $urlData,
            $queryParams,
            $this->urlHeader
        );
        if (!empty($res['data']['error_code'])) {
            return jerr2($res['data']['error_description'] ?? 'getRestore失败');
        }
        return jok2('ok', $res['data']);
    }

    /**
     * 获取转存后的文件信息
     *
     * @return void
     */
    public function getTasks($infoData)
    {
        $urlData =  [];
        $queryParams = [];
        $res = $this->requestXunleiApi(
            "https://api-pan.xunlei.com/drive/v1/tasks/" . $infoData['restore_task_id'],
            'GET',
            $urlData,
            $queryParams,
            $this->urlHeader
        );
        if (!empty($res['data']['error_code'])) {
            return jerr2($res['data']['error_description'] ?? 'getTasks失败');
        }
        return jok2('ok', $res['data']);
    }


    /**
     * 获取分享链接
     *
     * @return void
     */
    public function getSharePassword($result)
    {
        // $result[] = '';
        $expiration_days = '-1';
        if ($this->expired_type == 2) {
            $expiration_days = '2';
        }
        $urlData = [
            'file_ids' => $result,
            'share_to' => 'copy',
            'params' => [
                'subscribe_push' => 'false',
                'WithPassCodeInLink' => 'true'
            ],
            'title' => '云盘资源分享',
            'restore_limit' => '-1',
            'expiration_days' => $expiration_days
        ];

        $queryParams = [];
        $res = $this->requestXunleiApi(
            "https://api-pan.xunlei.com/drive/v1/share",
            'POST',
            $urlData,
            $queryParams,
            $this->urlHeader
        );
        if (!empty($res['data']['error_code'])) {
            return jerr2($res['data']['error_description'] ?? 'getSharePassword失败');
        }
        return jok2('ok', $res['data']);
    }


    /**
     * 删除指定资源
     * 
     * @return void
     */
    public function deletepdirFid($filelist)
    {
        // 1️⃣ 获取 AccessToken
        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            return jerr2('登录状态异常，获取accessToken失败');
        }

        // 2️⃣ 获取 CaptchaToken
        $captchaToken = $this->getCaptchaToken();
        if (empty($captchaToken)) {
            return jerr2('获取 captchaToken 失败');
        }

        // 3️⃣ 构造 headers
        $this->urlHeader = array_map(function ($h) use ($accessToken, $captchaToken) {
            if (str_starts_with($h, 'Authorization: ')) {
                return 'Authorization: Bearer ' . $accessToken;
            }
            if (str_starts_with($h, 'x-captcha-token: ')) {
                return 'x-captcha-token: ' . $captchaToken;
            }
            return $h;
        }, $this->urlHeader);

        $urlData = [
            'ids' => $filelist,
            'space' => ''
        ];

        $queryParams = [];
        $res = $this->requestXunleiApi(
            "https://api-pan.xunlei.com/drive/v1/files:batchDelete",
            'POST',
            $urlData,
            $queryParams,
            $this->urlHeader
        );

        return ['status' => 200];
    }

    /**
     * Xunlei API 通用请求方法
     *
     * @param string $url 接口地址
     * @param string $method GET 或 POST
     * @param array $data POST 数据
     * @param array $query GET 查询参数
     * @param array $headers 请求头，传啥用啥
     * @return array 返回解析后的 JSON 或错误信息
     */
    private function requestXunleiApi(
        string $url,
        string $method = 'GET',
        array $data = [],
        array $query = [],
        array $headers = []
    ): array {
        // 拼接 GET 参数
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate"); // 明确只使用gzip和deflate编码
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不验证域名

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif (strtoupper($method) === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }


        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) return ['code' => 1, 'msg' => "请求失败: $error"];

        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['code' => 1, 'msg' => '返回 JSON 解析失败', 'raw' => $body];
        }

        return ['code' => 0, 'data' => $json];
    }
}
