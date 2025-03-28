<?php
namespace netdisk\pan;

class AlipanPan extends BasePan
{
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->urlHeader = [
            'Accept: application/json, text/plain, */*',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
            'Authorization: ',
            'Content-Type: application/json',
            'Origin: https://www.alipan.com',
            'Priority: u=1, i',
            'Referer: https://www.alipan.com/',
            'Sec-Ch-Ua: "Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-site',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 Edg/126.0.0.0',
            'X-Canary: client=web,app=share,version=v2.3.1'
        ];
    }

    public function getFiles($pdir_fid=0)
    {
        $tokenFile = __DIR__ . '/access_token.json';
        $access_token = $this->manageAccessToken($tokenFile);
        if(empty($access_token)){
            return jerr2('登录状态异常，获取access_token失败');
        }
        foreach ($this->urlHeader as &$header) {
            if (str_starts_with($header, 'Authorization: ')) {
                $header = 'Authorization: Bearer ' . $access_token;
                break;
            }
        }

        if($pdir_fid === 0){
            $pdir_fid = 'root';
        }

        $urlData =  array(
            'all' => false,
            'drive_id' => '2008425230',
            'fields' => "*",
            'limit' => 100,
            'order_by' => "updated_at",
            'order_direction' => "DESC",
            'parent_file_id' => $pdir_fid,
            'url_expire_sec' => 14400,
        );

        $res = curlHelper("https://api.aliyundrive.com/adrive/v3/file/list", "POST", json_encode($urlData),$this->urlHeader)['body'];
        $res = json_decode($res, true);
        if(!empty($res['message'])){
            return jerr2($res['message']);
        }
        return jok2('获取成功',$res['items']);
    }

    public function transfer($share_id)
    {
        $tokenFile = __DIR__ . '/access_token.json';
        $access_token = $this->manageAccessToken($tokenFile);
        if(empty($access_token)){
            return jerr2('登录状态异常，获取access_token失败');
        }
        foreach ($this->urlHeader as &$header) {
            if (str_starts_with($header, 'Authorization: ')) {
                $header = 'Authorization: Bearer ' . $access_token;
                break;
            }
        }

        $data = [];
        $res = $this->getAlipan1($share_id);
        if(!isset($res['file_infos'])){
            return jerr2($res['message']);
        }
        $infos = $res;
        
        if($this->isType == 1){
            $urls['title'] = $infos['share_name'];
            $urls['share_url'] = $this->url;
            return jok2('检验成功', $urls);
        }
        
        //通过分享id获取file_id
        $file_infos = $infos['file_infos'];
        //通过分享id获取X-Share-Token
        $res = $this->getAlipan2($share_id);
        if(!isset($res['share_token'])){
            return jerr2($res['message']);
        }
        $share_token = $res['share_token'];

        $to_pdir_fid = Config('qfshop.ali_file'); //默认存储路径
        if($this->expired_type == 2){
            $to_pdir_fid = Config('qfshop.ali_file_time'); //临时资源路径
        }
        
        $data3['requests'] = [];
        $data3['resource'] = 'file';
        
        foreach ($file_infos as $key=>$value) {
            $data3['requests'][$key]['body']['auto_rename'] = true;
            $data3['requests'][$key]['body']['file_id'] = $value['file_id'];
            $data3['requests'][$key]['body']['share_id'] = $share_id;
            $data3['requests'][$key]['body']['to_drive_id'] = '2008425230';
            $data3['requests'][$key]['body']['to_parent_file_id'] = $to_pdir_fid;
            $data3['requests'][$key]['headers']['Content-Type'] = 'application/json';
            $data3['requests'][$key]['id'] = $key.'';
            $data3['requests'][$key]['method'] = 'POST';
            $data3['requests'][$key]['url'] = '/file/copy';
        }
        
        //保存
        $res = $this->getAlipan3($data3,$share_token);
        if (!isset($res['responses'])) {
            return jerr2($res['message'] ?? '请求失败，无响应数据');
        }

        $response = $res['responses'][0];
        $body = $response['body'];
        if (isset($body['code'])) {
            return jerr2($body['message'] ?? '请求失败');
        }

        $responses = $res['responses'];

        $data4['drive_id'] = '2008425230';
        $data4['expiration'] = '';
        $data4['share_pwd'] = '';
        $data4['file_id_list'] = [];
        
        foreach ($responses as $key=>$value){
            $data4['file_id_list'][] = $value['body']['file_id'];
        }
        
        //分享
        $res = $this->getAlipan4($data4);
        if(!isset($res['share_url'])){
            return jerr2($res['message']??'转存失败4');
        }
        $share = $res;
        
        $data['share_url'] = $share['share_url'];
        $data['title'] = $share['share_title'];
        $data['fid'] = $share['file_id_list'];
        
        return jok2('转存成功', $data);
    }
  
    /**
     * 阿里-0-通过分享id获取file_id
     *
     * @return void
     */
    public function getAlipan1($share_id)
    {
        $urlData =  [
            'share_id' => $share_id,
        ];
        $urlHeader = array(
            'Content-Type: application/json',
        );
        $res = curlHelper("https://api.aliyundrive.com/adrive/v3/share_link/get_share_by_anonymous", "POST", json_encode($urlData),$urlHeader)['body'];
        return json_decode($res, true);
    }
    
     /**
     * 阿里-0-通过分享id获取X-Share-Token
     *
     * @return void
     */
    public function getAlipan2($share_id)
    {
        $urlData =  array(
            'share_id' => $share_id,
        );
        $res = curlHelper("https://api.aliyundrive.com/v2/share_link/get_share_token", "POST", json_encode($urlData), $this->urlHeader)['body'];
        return json_decode($res, true);
    }
    
     /**
     * 阿里-1-保存
     *
     * @return void
     */
    public function getAlipan3($urlData,$share_token)
    {
        $urlHeader= $this->urlHeader;
        $urlHeader[] = 'X-Share-Token: '.$share_token;
        $res = curlHelper("https://api.aliyundrive.com/adrive/v4/batch", "POST", json_encode($urlData), $urlHeader)['body'];
        return json_decode($res, true);
    }
    
     /**
     * 阿里-2-分享
     *
     * @return void
     */
    public function getAlipan4($urlData)
    {
        $res = curlHelper("https://api.aliyundrive.com/adrive/v2/share_link/create", "POST", json_encode($urlData), $this->urlHeader)['body'];
        return json_decode($res, true);
    }

    

    /**
     * 管理 access_token
     * @param string $tokenFile token文件路径
     * @return string access_token
     */
    private function manageAccessToken($tokenFile)
    {
        $tokenData = [];
        $currentRefreshToken = Config('qfshop.Authorization');
        
        // 检查文件是否存在
        if (file_exists($tokenFile)) {
            $tokenData = json_decode(file_get_contents($tokenFile), true);
            
            // 检查token是否存在且未过期且不为空，且refresh_token未改变
            if (isset($tokenData['access_token']) 
                && isset($tokenData['expires_at']) 
                && isset($tokenData['refresh_token'])
                && !empty($tokenData['access_token'])
                && time() < $tokenData['expires_at']
                && $tokenData['refresh_token'] === $currentRefreshToken
            ) {
                return $tokenData['access_token'];
            }
        }
        
        // 获取新的token
        $newToken = $this->getNewAccessToken();
        
        // 保存新token
        $tokenData = [
            'access_token' => $newToken,
            'refresh_token' => $currentRefreshToken,
            'expires_at' => time() + 3600
        ];
        
        file_put_contents($tokenFile, json_encode($tokenData));
        
        return $newToken;
    }

    /**
     * 获取新的access_token
     * @return string
     */
    private function getNewAccessToken()
    {
        $urlData =  [
            'refresh_token' => Config('qfshop.Authorization'),
        ];
        $urlHeader = array(
            'Content-Type: application/json',
        );
        $res = curlHelper("https://api.aliyundrive.com/token/refresh", "POST", json_encode($urlData),$urlHeader)['body'];
        $res = json_decode($res, true);
        return $res['access_token']??'';
    }


    /**
     * 删除指定资源
     * 
     * @return void
     */
    public function deletepdirFid($filelist)
    {
        $urlData['requests'] = [];
        $urlData['resource'] = 'file';
        
        foreach ($filelist as $key=>$value) {
            $urlData['requests'][$key]['body']['file_id'] = $value??'';
            $urlData['requests'][$key]['body']['drive_id'] = '2008425230';
            $urlData['requests'][$key]['headers']['Content-Type'] = 'application/json';
            $urlData['requests'][$key]['id'] = $value??'';
            $urlData['requests'][$key]['method'] = 'POST';
            $urlData['requests'][$key]['url'] = '/recyclebin/trash';
        }

        $tokenFile = __DIR__ . '/access_token.json';
        $access_token = $this->manageAccessToken($tokenFile);
        if(empty($access_token)){
            return jerr2('登录状态异常，获取access_token失败');
        }
        foreach ($this->urlHeader as &$header) {
            if (str_starts_with($header, 'Authorization: ')) {
                $header = 'Authorization: Bearer ' . $access_token;
                break;
            }
        }
        $res = curlHelper("https://api.aliyundrive.com/adrive/v4/batch", "POST", json_encode($urlData), $this->urlHeader)['body'];
        return json_decode($res, true);
    }

}