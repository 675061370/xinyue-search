<?php
namespace netdisk\pan;

class QuarkPan extends BasePan
{
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->urlHeader = [
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
            'cookie: ' . Config('qfshop.quark_cookie')
        ];
    }

    public function getFiles($pdir_fid=0)
    {
        // 原 getFiles 方法内容
        $urlData = [];
        $queryParams = [
            'pr' => 'ucpro',
            'fr' => 'pc',
            'uc_param_str' => '',
            'pdir_fid' => $pdir_fid,
            '_page' => 1,
            '_size' => 50,
            '_fetch_total' => 1,
            '_fetch_sub_dirs' => 0,
            '_sort' => 'file_type:asc,updated_at:desc',
        ];
        
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/file/sort", "GET", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr2($res['message']=='require login [guest]'?'夸克未登录，请检查cookie':$res['message']);
        }
        
        return jok2('获取成功',$res['data']['list']);
    }

    public function transfer($pwd_id)
    {
        if(empty($this->stoken)){
            //获取要转存夸克资源的stoken
            $res = $this->getStoken($pwd_id);
            if($res['status'] !== 200) return jerr2($res['message']);
            $infoData = $res['data'];
            
            if($this->isType == 1){
                $urls['title'] = $infoData['title'];
                $urls['share_url'] = $this->url;
                $urls['stoken'] = $infoData['stoken'];
                return jok2('检验成功', $urls);
            }
            $stoken = $infoData['stoken'];
            $stoken = str_replace(' ', '+', $stoken);
        }else{
            $stoken = str_replace(' ', '+', $this->stoken);
        }
    
        //获取要转存夸克资源的详细内容
        $res = $this->getShare($pwd_id,$stoken);
        if($res['status']!== 200) return jerr2($res['message']);
        $detail = $res['data'];
  
        $fid_list = [];
        $fid_token_list = [];
        $title = $detail['share']['title']; //资源名称
        foreach ($detail['list'] as $key => $value) {
            $fid_list[] =  $value['fid'];
            $fid_token_list[] =  $value['share_fid_token'];
        }
        
        //转存资源到指定文件夹
        $res = $this->getShareSave($pwd_id,$stoken,$fid_list,$fid_token_list);
        if($res['status']!== 200) return jerr2($res['message']);
        $task_id = $res['data']['task_id'];

        //转存后根据task_id获取转存到自己网盘后的信息
        $retry_index = 0;
        $myData = '';
        while ($myData=='' || $myData['status'] != 2) {
            $res = $this->getShareTask($task_id, $retry_index);
            if($res['message']== 'capacity limit[{0}]'){
                return jerr2('容量不足');
            }
            if($res['status']!== 200) {
                return jerr2($res['message']);
            }
            $myData = $res['data'];
            $retry_index++;
            // 可以添加一个最大重试次数的限制，防止无限循环
            if ($retry_index > 50) {
                break;
            }
        }

        try {
            //删除转存后可能有的广告
            $banned = Config('qfshop.quark_banned')??''; //如果出现这些字样就删除
            if(!empty($banned)){
                $bannedList = explode(',', $banned);
                $pdir_fid = $myData['save_as']['save_as_top_fids'][0];
                $dellist = [];
                $plist = $this->getPdirFid($pdir_fid);
                if(!empty($plist)){
                    foreach ($plist as $key => $value) {
                         // 检查$value['file_name']是否包含$bannedList中的任何一项
                        $contains = false;
                        foreach ($bannedList as $item) {
                            if (strpos($value['file_name'], $item) !== false) {
                                $contains = true;
                                break;
                            }
                        }
                        if ($contains) {
                            $dellist[] = $value['fid'];
                        }
                    }
                    if(count($plist) === count($dellist)){
                        //要删除的资源数如果和原数据资源数一样 就全部删除并终止下面的分享
                        $this->deletepdirFid([$pdir_fid]);
                        return jerr2("资源内容为空");
                    }else{
                        if (!empty($dellist)) {
                            $this->deletepdirFid($dellist);
                        } 
                    }
                    
                }
            }
        } catch (Exception $e) {
        }
        
        $shareFid = $myData['save_as']['save_as_top_fids'];
        //分享资源并拿到更新后的task_id
        $res = $this->getShareBtn($myData['save_as']['save_as_top_fids'],$title);
        if($res['status']!== 200) return jerr2($res['message']);
        $task_id = $res['data']['task_id'];

        //根据task_id拿到share_id
        $retry_index = 0;
        $myData = '';
        while ($myData=='' || $myData['status'] != 2) {
            $res = $this->getShareTask($task_id, $retry_index);
            if($res['status']!== 200) continue;
            $myData = $res['data'];
            $retry_index++;
            // 可以添加一个最大重试次数的限制，防止无限循环
            if ($retry_index > 50) {
                break;
            }
        }


        //根据share_id  获取到分享链接
        $res = $this->getSharePassword($myData['share_id']);
        if($res['status']!== 200) return jerr2($res['message']);
        $share = $res['data'];
        // $share['fid'] = $share['first_file']['fid'];
        $share['fid'] = (is_array($shareFid) && count($shareFid) > 1) ? $shareFid : $share['first_file']['fid'];

        return jok2('转存成功', $share);
    }

    /**
     * 获取要转存资源的stoken
     *
     * @return void
     */
    public function getStoken($pwd_id)
    {
        $urlData =  array(
            'passcode' => '',
            'pwd_id' => $pwd_id,
        );
        $queryParams = [
            'pr' => 'ucpro',
            'fr' => 'pc',
            'uc_param_str' => '',
        ];
        return $this->executeApiRequest(
            "https://drive-pc.quark.cn/1/clouddrive/share/sharepage/token", 
            "POST", 
            $urlData, 
            $queryParams
        );
    }


    /**
     * 获取要转存资源的详细内容
     *
     * @return void
     */
    public function getShare($pwd_id,$stoken)
    {
        $urlData = array();
        $queryParams = [
            "pr" => "ucpro",
            "fr" => "pc",
            "uc_param_str" => "",
            "pwd_id" => $pwd_id,
            "stoken" => $stoken,
            "pdir_fid" => "0",
            "force" => "0",
            "_page" => "1",
            "_size" => "100",
            "_fetch_banner" => "1",
            "_fetch_share" => "1",
            "_fetch_total" => "1",
            "_sort" => "file_type:asc,updated_at:desc"
        ];
        return $this->executeApiRequest(
            "https://drive-pc.quark.cn/1/clouddrive/share/sharepage/detail", 
            "GET", 
            $urlData, 
            $queryParams
        );
    }


    /**
     * 转存资源到指定文件夹
     *
     * @return void
     */
    public function getShareSave($pwd_id,$stoken,$fid_list,$fid_token_list)
    {
        if(!empty($this->to_pdir_fid)){
            $to_pdir_fid = $this->to_pdir_fid;
        }else{
            $to_pdir_fid = Config('qfshop.quark_file'); //默认存储路径
            if($this->expired_type == 2){
                $to_pdir_fid = Config('qfshop.quark_file_time'); //临时资源路径
            }
        }
        
        $urlData =  array(
            'fid_list' => $fid_list, 
            'fid_token_list' => $fid_token_list, 
            'to_pdir_fid' => $to_pdir_fid, 
            'pwd_id' => $pwd_id, 
            'stoken' => $stoken, 
            'pdir_fid' => "0", 
            'scene' => "link", 
        );
        $queryParams = [
            "entry" => "update_share",
            "pr" => "ucpro",
            "fr" => "pc",
            "uc_param_str" => ""
        ];
        
        return $this->executeApiRequest(
            "https://drive-pc.quark.cn/1/clouddrive/share/sharepage/save", 
            "POST", 
            $urlData, 
            $queryParams
        );
    }

    /**
     * 分享资源拿到task_id
     *
     * @return void
     */
    public function getShareBtn($fid_list,$title)
    {
        if(!empty($this->ad_fid)){
            $fid_list[] = $this->ad_fid;
        }
        $urlData =  array(
            'fid_list' => $fid_list, 
            'expired_type' => $this->expired_type, 
            'title' => $title, 
            'url_type' => 1, 
        );
        $queryParams = [
            "pr" => "ucpro",
            "fr" => "pc",
            "uc_param_str" => ""
        ];
        
        return $this->executeApiRequest(
            "https://drive-pc.quark.cn/1/clouddrive/share", 
            "POST", 
            $urlData, 
            $queryParams
        );
    }


    /**
     * 根据task_id拿到自己的资源信息
     *
     * @return void
     */
    public function getShareTask($task_id,$retry_index)
    {
        $urlData = array();
        $queryParams = [
            "pr" => "ucpro",
            "fr" => "pc",
            "uc_param_str" => "",
            "task_id" => $task_id,
            "retry_index" => $retry_index
        ];
        return $this->executeApiRequest(
            "https://drive-pc.quark.cn/1/clouddrive/task", 
            "GET", 
            $urlData, 
            $queryParams
        );
    }

    /**
     * 根据share_id  获取到分享链接
     *
     * @return void
     */
    public function getSharePassword($share_id)
    {
        $urlData =  array(
            'share_id' => $share_id,
        );
        $queryParams = [
            "pr" => "ucpro",
            "fr" => "pc",
            "uc_param_str" => ""
        ];
        return $this->executeApiRequest(
            "https://drive-pc.quark.cn/1/clouddrive/share/password", 
            "POST", 
            $urlData, 
            $queryParams
        );
    }
    
    
    /**
     * 删除指定资源
     * 
     * @return void
     */
    public function deletepdirFid($filelist)
    {
        $urlData =  array(
            'action_type' => 2,
            'exclude_fids' => [],
            'filelist' => $filelist,
        );
        $queryParams = [
            "pr" => "ucpro",
            "fr" => "pc",
            "uc_param_str" => ""
        ];
        return $this->executeApiRequest(
            "https://drive-pc.quark.cn/1/clouddrive/file/delete", 
            "POST", 
            $urlData, 
            $queryParams
        );
    }
    
    /**
     * 获取夸克网盘指定文件夹内容
     *
     * @return void
     */
    public function getPdirFid($pdir_fid)
    {
        $urlData = [];
        $queryParams = [
            'pr' => 'ucpro',
            'fr' => 'pc',
            'uc_param_str' => '',
            'pdir_fid' => $pdir_fid,
            '_page' => 1,
            '_size' => 200,
            '_fetch_total' => 1,
            '_fetch_sub_dirs' => 0,
            '_sort' => 'file_type:asc,updated_at:desc',
        ];
        try {
            $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/file/sort", "GET", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
            $res = json_decode($res, true);
            if($res['status'] !== 200){
                return [];
            }
            return $res['data']['list'];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * 执行API请求并处理重试逻辑
     * 
     * @param string $url 请求URL
     * @param string $method 请求方法(GET/POST)
     * @param array $data 请求数据
     * @param array $queryParams 查询参数
     * @param int $maxRetries 最大重试次数
     * @param int $retryDelay 重试延迟(秒)
     * @return array 响应结果
     */
    protected function executeApiRequest($url, $method, $data = [], $queryParams = [], $maxRetries = 3, $retryDelay = 2)
    {
        $attempt = 0;
        while ($attempt < $maxRetries) {
            $attempt++;
            try {
                $res = curlHelper($url, $method, json_encode($data), $this->urlHeader, $queryParams)['body'];
                return json_decode($res, true);
            } catch (\Throwable $e) {
                $this->logApiError($url, $attempt, $e->getMessage());
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                }
            }
        }
        
        return ['status' => 500, 'message' => '接口请求异常'];
    }
    /**
     * 记录API错误日志
     * 
     * @param string $prefix 日志前缀
     * @param int $attempt 尝试次数
     * @param mixed $error 错误信息
     */
    protected function logApiError($prefix, $attempt, $error)
    {
        $errorMsg = is_scalar($error) ? $error : json_encode($error);
        $logMessage = date('Y-m-d H:i:s') . ' ' . $prefix . '请求失败（尝试次数: ' . $attempt . '） 错误: ' . $errorMsg . "\n";
        file_put_contents('error.log', $logMessage, FILE_APPEND);
    }
}