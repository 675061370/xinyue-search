<?php

namespace app\model;

class Sms
{
    /**
     * 发送通知短信
     *
     * @param string 手机号码
     * @param string 内容
     * @param string 签到二维码链接
     * @return void
     */
    public function sendSmsNotice($smsData)
    {
        //短连接生成
        $baidu_token = config('qfshop.baidu_token');
        if (!$baidu_token) {
            jerr('请先在后台系统设置短链接相关参数！');
        }
        $urlData = [
            [
                'LongUrl' => $smsData['qrcodeUrl'],
                'TermOfValidity' => "1-year",
            ]
        ];
        $urlHeader = array(
            'Content-Type: application/json; charset=UTF-8',
            'Dwz-Token: '.$baidu_token,
        );
        $res = curlHelper("https://dwz.cn/api/v3/short-urls", "POST", json_encode($urlData,256), $urlHeader)['body'];
        $res = json_decode($res, true);
        if($res['Code'] != 0){
            return false;
        }

        $smsData['qrcodeurl'] = $res['ShortUrls'][0]['ShortUrl'];

        $result = $this->sendSms($smsData);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 发送短信（百度）
     *
     * @param string 手机号码
     * @param string 内容
     * @return void
     */
    private function sendSms($smsData)
    {
        //初始化短信相关配置
        $sms_sign = config('qfshop.sms_sign');
        $sms_tmpl = config('qfshop.sms_tmpl_1');
        $error = null;
        if (!($sms_sign && $sms_tmpl)) {
            $error =  jerr('请先在后台系统设置短信相关参数！');
        }

        require_once __DIR__ . "/../../extend/baidu/baidusmsv3.php";
        $config = array(
            'endPoint' => 'smsv3.bj.baidubce.com',
            'accessKey' => 'b71eab07ebcc4f09b3f770e43a9d126d',
            'secretAccessKey' => '3c6c8b3d85274baab4c38481996919ec',
        );
        $smsClient = new \Baidusmsv3($config);
        $message = array(
            'template'  => $sms_tmpl, //短信模板ID
            'signatureId' => $sms_sign, //短信签名ID
            'mobile'   => $smsData['mobile'],
            "contentVar" => array(
                'param1' => '“'.$smsData['title'].'“',
                'param2' => $smsData['time'],
                'param3' => $smsData['address'],
                'param4' => '青峰网络',
                'param5' => $smsData['qrcodeurl'],
            ),
        );
        $result = $smsClient->sendMessage($message);
        if($result['code'] == 1000){
            return true;
        }else{
            return false;
        }
      
    }


    /**
     * 发送短信验证码
     *
     * @param string 手机号码
     * @return void
     */
    public function sendSmsCode($mobile,$code)
    {
        //初始化短信相关配置
        $sms_sign = config('qfshop.sms_sign');
        $sms_tmpl = config('qfshop.sms_tmpl_2');
        $error = null;
        if (!($sms_sign && $sms_tmpl)) {
            $error =  jerr('请先在后台系统设置短信相关参数！');
        }

        require_once __DIR__ . "/../../extend/baidu/baidusmsv3.php";
        $config = array(
            'endPoint' => 'smsv3.bj.baidubce.com',
            'accessKey' => 'b71eab07ebcc4f09b3f770e43a9d126d',
            'secretAccessKey' => '3c6c8b3d85274baab4c38481996919ec',
        );
        $smsClient = new \Baidusmsv3($config);
        $message = array(
            'template'  => $sms_tmpl, //短信模板ID
            'signatureId' => $sms_sign, //短信签名ID
            'mobile'   => $mobile,
            "contentVar" => array(
                'code' => ''.$code,
                'time' => '5',
            ),
        );
        $result = $smsClient->sendMessage($message);
        if($result['code'] == 1000){
            return true;
        }else{
            return false;
        }
    }
    
}
