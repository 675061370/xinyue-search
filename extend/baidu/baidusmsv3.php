<?php
include_once __DIR__ . '/baidusign.php';

class Baidusmsv3
{

    //终端，默认为smsv3.bj.baidubce.com
    protected $endPoint;
    //AK
    protected $accessKey;
    //SK
    protected $secretAccessKey;

    /**
     * $config = array(
     *    'endPoint' => 'smsv3.bj.baidubce.com',
     *    'accessKey' => '618888888888888888888888',
     *    'secretAccessKey' => 'a6888888888888888888888888',
     *  );
     */
    function __construct(array $config)
    {
        $this->endPoint = isset($config['endPoint']) ? $config['endPoint'] : 'sms.bj.baidubce.com';
        $this->accessKey = isset($config['accessKey']) ? $config['accessKey'] : '';
        $this->secretAccessKey = isset($config['secretAccessKey']) ? $config['secretAccessKey'] : '';
    }

    public function sendMessage($message_array)
    {

        //生成json格式
        $json_data = json_encode($message_array);

        //生成签名
        $signer = new SampleSigner();
        $credentials = array("ak" => $this->accessKey, "sk" => $this->secretAccessKey);
        $httpMethod = "POST";
        $path = "/api/v3/sendSms";
        $params = array("clientToken" => $this->create_uuid());
        $timestamp = new \DateTime();
        $timestamp->setTimezone(new \DateTimeZone("GMT"));
        $datetime = $timestamp->format("Y-m-d\TH:i:s\Z");
        //$datetime_gmt = $timestamp->format("D, d M Y H:i:s T");

        $headers = array("Host" => $this->endPoint);
        //$str_sha256 = hash('sha256', $json_data);
        //$headers['x-bce-content-sha256'] = $str_sha256;
        $headers['Content-Length'] = strlen($json_data);
        $headers['Content-Type'] = "application/json";
        $headers['x-bce-date'] = $datetime;
        $options = array(SignOption::TIMESTAMP => $timestamp, SignOption::HEADERS_TO_SIGN => array('host', 'x-bce-date',),);
        $ret = $signer->sign($credentials, $httpMethod, $path, $headers, $params, $options);
        $headers_curl = array(
            'Content-Type:application/json',
            'Host:' . $this->endPoint,
            'x-bce-date:' . $datetime,
            'Content-Length:' . strlen($json_data),
            //'x-bce-content-sha256:' . $str_sha256,
            'Authorization:' . $ret,
            //"Accept-Encoding: gzip,deflate",
            //'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
            //'Date:' . $datetime_gmt,
        );
        //$url = 'http://sms.bj.baidubce.com/bce/v2/message';
        $url = 'http://' . $this->endPoint . $path . "?" . http_build_query($params);
        //$url = '/bce/v2/message';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        //curl_setopt($curl,CURLOPT_PROXY,'127.0.0.1:8888');//设置代理服务器
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $errorno = curl_errno($curl);
        curl_close($curl);
        //print var_export($result, true);
        return json_decode($result, true);
    }

    private function create_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * {}
     */
    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;
        return $this;
    }

    /**
     * {}
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * {}
     */
    public function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
        return $this;
    }

    /**
     * {}
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * {}
     */
    public function setSecretAccessKey($secretAccessKey)
    {
        $this->secretAccessKey = $secretAccessKey;
        return $this;
    }

    /**
     * {}
     */
    public function getSecretAccessKey()
    {
        return $this->secretAccessKey;
    }

}