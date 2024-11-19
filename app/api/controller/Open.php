<?php

namespace app\api\controller;

use think\App;
use think\facade\Cache;
use app\api\QfShop;

class Open extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->url = "";
        $this->is_type = 0;
        $this->cookie = ""; //夸克登录凭证
        $this->Authorization = ""; //阿里登录凭证
        $this->expired_type = 1; //1分享永久 2临时
        $this->to_pdir_fid = ""; //存入目标文件
        $this->ad_fid = "";
        $this->code = ""; //提取码
        $this->isType = 0; //等于1时仅校验是否有效并提取资源信息
        $this->urlHeader = array(
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
        );
    }
    /**
     * 
     *
     * @return void
     */
    public function index()
    {
        return jok('Hello World');
    }

    /**
     * 获取夸克网盘文件夹
     *
     * @return void
     */
    public function getFiles()
    {
        $this->cookie = input('cookie')??'';
        $urlData = [];
        $queryParams = [
            'pr' => 'ucpro',
            'fr' => 'pc',
            'uc_param_str' => '',
            'pdir_fid' => 0,
            '_page' => 1,
            '_size' => 50,
            '_fetch_total' => 1,
            '_fetch_sub_dirs' => 0,
            '_sort' => 'file_type:asc,updated_at:desc',
        ];
        
        $this->urlHeader[] = 'cookie: ' . $this->cookie;
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/file/sort", "GET", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr($res['message']=='require login [guest]'?'夸克未登录，请检查cookie':$res['message']);
        }
        
        return jok('获取成功',$res['data']['list']);
    }
    
    /**
     * 一键转存并分享资源
     * 
     * type 0 夸克  1阿里
     *
     * @return void
     */
    public function transfer()
    {
        $url = input("url");
        $this->code = input('code')??'';
        $this->isType = input('isType')??0;
        
        if($this->isType != 1){ //直接入口就不用cookie 防止账号异常
            $this->cookie = input('cookie')??'';
            $this->Authorization = input('Authorization')??'';
        }
        
        $this->expired_type = input('expired_type')??1;
        $this->to_pdir_fid = input('to_pdir_fid')??"";
        $this->ad_fid = input('ad_fid')??"";
        
        if (strpos($url, '?entry=') !== false) {
            $entry = preg_match('/\?entry=([^&]+)/', $url, $matches) ? $matches[1] : '';
            $url = preg_match('/.*(?=\?entry=)/', $url, $matches) ? $matches[0] : '';
        }

        $substring = strstr($url, 's/');
        if ($substring !== false) {
            $pwd_id = substr($substring, 2); // 去除 's/' 部分
        } else {
            return jerr("资源地址格式有误");
        }

        $this->urlHeader[] = 'cookie: ' . $this->cookie;
        
        $patterns = [
            'pan.quark.cn' => 0,
            'www.alipan.com' => 1,
            'www.aliyundrive.com' => 1,
            // 'pan.baidu.com' => 2,
            'drive.uc.cn' => 3,
            // 'pan.xunlei.com' => 4,
        ];
        
        $url_type = -1;  // 默认值为 -1
        foreach ($patterns as $pattern => $type) {
            if (strpos($url, $pattern) !== false) {
                $url_type = $type;
                break;  // 一旦匹配成功，退出循环
            }
        }
        
        $this->url = $url;

        if ($url_type == 0) {
            //夸克
            if(empty($this->cookie) && $this->isType==0){
                jerr("参数有误");
            }
            $this->transferQuark(strtok($pwd_id, '#'));
        } else if($url_type == 1){
            //阿里
            $this->transferAlipan($pwd_id);
        } else if($url_type == 3){
            //UC
            $this->transferUc($pwd_id);
        } else {
            return jerr("资源地址格式有误");
        }
    }
    
    /**
     * 夸克 - 一键转存并分享资源
     *
     * @return void
     */
    public function transferQuark($pwd_id){
        //获取要转存夸克资源的stoken
        $infoData = $this->getStoken($pwd_id);
        
        if($this->isType == 1){
            $urls['title'] = $infoData['title'];
            $urls['share_url'] = $this->url;
            return jok('检验成功', $urls);
        }
        
        
        $stoken = $infoData['stoken'];
        
        
        //获取要转存夸克资源的详细内容
        $detail = $this->getShare($pwd_id,$stoken);
        
        $fid_list = [];
        $fid_token_list = [];
        $title = $detail['share']['title']; //资源名称
        foreach ($detail['list'] as $key => $value) {
            $fid_list[] =  $value['fid'];
            $fid_token_list[] =  $value['share_fid_token'];
        }

        //转存资源到指定文件夹
        $task_id = $this->getShareSave($pwd_id,$stoken,$fid_list,$fid_token_list);

        //转存后根据task_id获取转存到自己网盘后的信息
        $retry_index = 0;
        $myData = '';
        while ($myData=='' || $myData['status'] != 2) {
            $myData = $this->getShareTask($task_id, $retry_index);
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
                        return jerr("资源内容为空");
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
        $task_id = $this->getShareBtn($myData['save_as']['save_as_top_fids'],$title);

        //根据task_id拿到share_id
        $retry_index = 0;
        $myData = '';
        while ($myData=='' || $myData['status'] != 2) {
            $myData = $this->getShareTask($task_id, $retry_index);
            $retry_index++;
            // 可以添加一个最大重试次数的限制，防止无限循环
            if ($retry_index > 50) {
                break;
            }
        }


        //根据share_id  获取到分享链接
        $share = $this->getSharePassword($myData['share_id']);
        // $share['fid'] = $share['first_file']['fid'];
        $share['fid'] = (is_array($shareFid) && count($shareFid) > 1) ? $shareFid : $share['first_file']['fid'];

        return jok('转存成功', $share);
    }
    
    /**
     * 阿里 - 一键转存并分享资源
     *
     * @return void
     */
    public function transferAlipan($share_id)
    {
        $data = [];
        $infos = $this->getAlipan1($share_id);
        
        if($this->isType == 1){
            $urls['title'] = $infos['share_name'];
            $urls['share_url'] = $this->url;
            return jok('检验成功', $urls);
        }else{
            return jerr('阿里暂不支持转存');
        }
        
        
        //通过分享id获取file_id
        $file_infos = $infos['file_infos'];
        
        //通过分享id获取X-Share-Token
        $share_token = $this->getAlipan2($share_id);
        
        $data3['requests'] = [];
        $data3['resource'] = 'file';
        
        foreach ($file_infos as $key=>$value) {
            $data3['requests'][$key]['body']['auto_rename'] = true;
            $data3['requests'][$key]['body']['file_id'] = $value['file_id'];
            $data3['requests'][$key]['body']['share_id'] = $share_id;
            $data3['requests'][$key]['body']['to_drive_id'] = '2008425230';
            $data3['requests'][$key]['body']['to_parent_file_id'] = '66a20824c3846d890f6542c6aac2c79822d2a64f';
            $data3['requests'][$key]['headers']['Content-Type'] = 'application/json';
            $data3['requests'][$key]['id'] = $key.'';
            $data3['requests'][$key]['method'] = 'POST';
            $data3['requests'][$key]['url'] = '/file/copy';
        }
        
        //保存
        $responses = $this->getAlipan3($data3,$share_token);
        $data4['drive_id'] = '2008425230';
        $data4['expiration'] = '';
        $data4['share_pwd'] = '';
        $data4['file_id_list'] = [];
        
        foreach ($responses as $key=>$value){
            $data4['file_id_list'][] = $value['body']['file_id'];
        }
        
        //分享
        $share = $this->getAlipan4($data4);
        
        $data['share_url'] = $share['share_url'];
        $data['title'] = $share['share_title'];
        
        return jok('转存成功', $data);
    }
    
    /**
     * UC- 一键转存并分享资源
     *
     * @return void
     */
    public function transferUc($pwd_id){
        $infoData = $this->getStokenUc($pwd_id);
        
        if($this->isType == 1){
            $urls['title'] = $infoData['title'];
            $urls['share_url'] = $this->url;
            return jok('检验成功', $urls);
        }else{
            return jerr('UC暂不支持转存');
        }
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
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/share/sharepage/token?pr=ucpro&fr=pc&uc_param_str=", "POST",json_encode($urlData), $this->urlHeader)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr($res['message']);
        }
        $data = $res['data'];
        return $data;
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
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/share/sharepage/detail", "GET", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr($res['message']);
        }
        return $res['data'];
    }


    /**
     * 转存资源到指定文件夹
     *
     * @return void
     */
    public function getShareSave($pwd_id,$stoken,$fid_list,$fid_token_list)
    {
        $to_pdir_fid = $this->to_pdir_fid??"";
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
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/share/sharepage/save", "POST", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr($res['message']=='require login [guest]'?'夸克未登录，请检查cookie':$res['message']);
        }
        return $res['data']['task_id'];
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
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/share", "POST", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr($res['message']);
        }
        return $res['data']['task_id'];
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
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/task", "GET", json_encode($urlData), $this->urlHeader, $queryParams)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr($res['message']);
        }
        return $res['data'];
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
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/share/password", "POST", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr($res['message']);
        }
        return $res['data'];
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
        curlHelper("https://drive-pc.quark.cn/1/clouddrive/file/delete", "POST", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
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
        $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/file/sort", "GET", json_encode($urlData), $this->urlHeader,$queryParams)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return [];
        }
        return $res['data']['list'];
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
        $res = json_decode($res, true);
        if(!isset($res['file_infos'])){
            return jerr($res['message']??'转存失败1');
        }
        return $res;
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
        $urlHeader = array(
            'Accept: application/json, text/plain, */*',
            'Accept-Encoding: gzip, deflate, br, zstd',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
            'Authorization: '.$this->Authorization,
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
        );
        $res = curlHelper("https://api.aliyundrive.com/v2/share_link/get_share_token", "POST", json_encode($urlData), $urlHeader)['body'];
        $res = json_decode($res, true);
        if(!isset($res['share_token'])){
            return jerr($res['message']??'转存失败2');
        }
        return $res['share_token'];
    }
    
     /**
     * 阿里-1-保存
     *
     * @return void
     */
    public function getAlipan3($urlData,$share_token)
    {
        $urlHeader = array(
            'Accept: application/json, text/plain, */*',
            'Accept-Encoding: gzip, deflate, br, zstd',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
            'Authorization: '.$this->Authorization,
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
            'X-Canary: client=web,app=share,version=v2.3.1',
            'X-Share-Token: '.$share_token
        );
        $res = curlHelper("https://api.aliyundrive.com/adrive/v4/batch", "POST", json_encode($urlData), $urlHeader)['body'];
        $res = json_decode($res, true);
        if(!isset($res['responses'])){
            return jerr($res['message']??'转存失败3');
        }
        return $res['responses'];
    }
    
     /**
     * 阿里-2-分享
     *
     * @return void
     */
    public function getAlipan4($urlData)
    {
        $urlHeader = array(
            'Accept: application/json, text/plain, */*',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
            'Authorization: '.$this->Authorization,
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
        );
        $res = curlHelper("https://api.aliyundrive.com/adrive/v2/share_link/create", "POST", json_encode($urlData), $urlHeader)['body'];
        $res = json_decode($res, true);
        return jerr($res);
        if(!isset($res['share_url'])){
            return jerr($res['message']??'转存失败4');
        }
        return $res;
    }
    
    
    /**
     * UC 00000
     *
     * @return void
     */
    public function getStokenUc($pwd_id)
    {
        $urlData =  array(
            'passcode' => '',
            'pwd_id' => $pwd_id,
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
            'Referer: https://drive.uc.cn/',
            'Referrer-Policy: strict-origin-when-cross-origin',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        );
        
        $res = curlHelper("https://pc-api.uc.cn/1/clouddrive/share/sharepage/token?pr=UCBrowser&fr=pc", "POST",json_encode($urlData),$urlHeader)['body'];
        $res = json_decode($res, true);
        if($res['status'] !== 200){
            return jerr($res['message']);
        }
        $data = $res['data'];
        return $data;
    }
    
}
