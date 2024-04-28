<?php

namespace app\api\controller;

use think\App;
use app\api\QfShop;
use app\model\Source as SourceModel;

class Other extends QfShop
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //第三方转存接口地址
        $this->url = "https://pan.xinyuedh.com";
        $this->model = new SourceModel();
    }
    
    /**
     * 全网搜索 该接口仅用于微信自动回复
     * 
     * @return void
     */
    public function search()
    {
        
        $param = input('');
        if (empty($param['title'])) {
            return jerr("请输入要看的内容");
        }

        $searchData = array(
            'cookie' => Config('qfshop.quark_cookie'),
            'title' => $param['title'],
        );
        $res = curlHelper($this->url."/api/open/network_search", "POST", $searchData)['body'];
        $res = json_decode($res, true);

        if($res['code'] !== 200){
            return jerr($res['message']);
        }

        $urls = $res['data'];
        
        $datas = [];
        foreach ($urls as $url) {
            $substring = strstr($url, 's/');
            if ($substring !== false) {
                $pwd_id = substr($substring, 2); // 去除 's/' 部分
            } else {
               continue;
            }
            
            $urlData = array(
                'cookie' => Config('qfshop.quark_cookie'),
                'url' => $url,
            );
            $res = curlHelper($this->url."/api/open/transfer", "POST", $urlData)['body'];
            $res = json_decode($res, true);
    
            if($res['code'] !== 200){
                continue;
            }
            $patterns = '/^\d+\./';
            $title = preg_replace($patterns, '', $res['data']['title']);
            //添加资源到系统中
            $data["title"] = $title.'('.$param['title'].')';
            $data["url"] = $res['data']['share_url'];
            $data["fid"] = $res['data']['first_file']['fid'];
            $data["is_time"] = 1;
            $data["update_time"] = time();
            $data["create_time"] = time();
            $this->model->insertGetId($data);
            $datas[] = $data;
        }
        
        return jok('临时资源获取成功',$datas);
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
        $map[] = ['create_time', '<=', time() - (10 * 60)];
        
        
        $this->model->where($map)->chunk(100, function ($order) {
            foreach ($order as $value) {
                $deles = $value->toArray();
                $filelist = [];
                $filelist[] = $deles['fid'];
                
                
                $urlData = '{ "action_type": 2, "exclude_fids": [], "filelist": '.json_encode($filelist).' }';
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
                $res = curlHelper("https://drive-pc.quark.cn/1/clouddrive/file/delete?pr=ucpro&fr=pc&uc_param_str=", "POST", $urlData, $urlHeader)['body'];
                $res = json_decode($res, true);
                if($res['status'] == 200){
                    $this->model->where('fid', $deles['fid'])->delete();
                }
            }
        });
        
        return jok('临时资源删除成功');
    }
    
}
