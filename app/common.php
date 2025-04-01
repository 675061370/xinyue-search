<?php

/**
 * 输出正常JSON
 *
 * @param string 提示信息
 * @param array  输出数据
 * @return json
 */
function jok($message = 'success', $data = null)
{
    header("content-type:application/json;chartset=uft-8");
    if ($data) {
        echo json_encode(["code" => 200, "message" => $message, 'data' => $data]);
    } else {
        echo json_encode(["code" => 200, "message" => $message, 'data' => $data??'']);
    }
    die;
}
function jok2($message = 'success', $data = null)
{
    if ($data) {
        return ["code" => 200, "message" => $message, 'data' => $data];
    } else {
        return ["code" => 200, "message" => $message, 'data' => $data??''];
    }
}
/**
 * 输出错误JSON
 *
 * @param string 错误信息
 * @param int 错误代码
 * @return json
 */
function jerr($message = 'error', $code = 500)
{
    header("content-type:application/json;chartset=uft-8");
    echo json_encode(["code" => $code, "message" => $message]);
    die;
}
function jerr2($message = 'error', $code = 500)
{
    return ["code" => $code, "message" => $message];
}
/**
 * 密码+盐 加密
 *
 * @param string 明文密码
 * @param string 盐
 * @return string
 */
function encodePassword($password, $salt)
{
    return sha1($password . $salt . $password . $salt);
}
/**
 * 密码校验 6-16
 *
 * @param string 明文密码
 * @return boolean 是否校验通过
 */
function isValidPassword($password)
{
    return preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*\s).{6,}/', $password);
}
/**
 * 获取随机字符
 *
 * @param int $len
 * @return void
 */
function getRandString($len)
{
    $string = '';
    $randString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ($i = 0; $i < $len; $i++) {
        $string .= $randString[rand(0, strlen($randString) - 1)];
    }
    return $string;
}
/**
 * 生成唯一会议编码
 * @param string $prefix 头部
 * @return string
 */
function get_order_no($prefix = 'QF')
{
    $order_no = $prefix;
    $order_no .= mb_strtoupper(dechex(date('m')), 'utf-8');
    $order_no .= date('d') . mb_substr(time(), -5, null, 'utf-8');
    $order_no .= mb_substr(microtime(), 2, 5, 'utf-8');
    return $order_no;
}
/**
 * 获取随机字母
 *
 * @param int 长度
 * @return string
 */
function getRandChar($len)
{
    $string = '';
    $randString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for ($i = 0; $i < $len; $i++) {
        $string .= $randString[rand(0, strlen($randString) - 1)];
    }
    return $string;
}
/**
 * 驼峰转下划线
 * @param $camelCaps
 * @param string $separator
 * @return string
 */
function uncamelize($camelCaps, $separator = '_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}
/**
 * 遍历类的方法
 *
 * @param string 指定的类名称
 * @return array
 */
function getClassMethods($class)
{
    $array_result = [];
    $array_all = get_class_methods($class);
    if ($parent_class = get_parent_class($class)) {
        $array_parent = get_class_methods($parent_class);
        $array_result = array_diff($array_all, $array_parent);
    } else {
        $array_result = $array_all;
    }
    return $array_result;
}
/**
 * 获取包含协议和端口的域名
 *
 * @return string
 */
function getFullDomain()
{
    // return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME']) . "://" . $_SERVER['HTTP_HOST'];
    return "http://" . $_SERVER['HTTP_HOST'];
}
/**
 * 图片地址转绝对路径
 *
 * @return string
 */
function getimgurl($img)
{   
    $result = '';
    if($img){
        // $url = ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME']) . "://" . $_SERVER['HTTP_HOST'];
        $url = "http://" . $_SERVER['HTTP_HOST'];
        if(is_array($img)){
            $result = [];
            foreach ($img as $key => $value) {
                if(!preg_match("/^http(s)?:\\/\\/.+/", $value)){
                    $result[] = $url.$value;
                }else{
                    $result[] = $value;
                }
            }
        }else{
            if(!preg_match("/^http(s)?:\\/\\/.+/", $img)){
                $result = $url.$img;
            }else{
                $result = $img;
            }
        }
    }
    return $result;
}

/**
 * 替换编辑器内容中的文件地址
 * @param string  $content     编辑器内容
 * @return string
 */
function app_replace_content_file_url($content)
{
    \phpQuery::newDocumentHTML($content);
    $pq = pq(null);
    $domain        = request()->host();
    $images = $pq->find("img");
    if ($images->length) {
        foreach ($images as $img) {
            $img    = pq($img);
            $imgSrc = $img->attr("src");
            if(!preg_match("/^http(s)?:\\/\\/.+/", $imgSrc)){
                $img->attr("src", getimgurl($imgSrc));
            }
        }
    }
    $links = $pq->find("a");
    if ($links->length) {
        foreach ($links as $link) {
            $link = pq($link);
            $href = $link->attr("href");
            if(!preg_match("/^http(s)?:\\/\\/.+/", $href)){
                $img->attr("href", getimgurl($imgSrc));
            }
        }
    }
    $content = $pq->htmlOuter();
    \phpQuery::$documents = null;
    return $content;
}

/**
 * 获取客户端IP
 *
 * @return string
 */
function getClientIp()
{
    foreach (array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ) as $key) {
        if (array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if ((bool) filter_var(
                    $ip,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6
                    // FILTER_FLAG_NO_PRIV_RANGE |
                    // FILTER_FLAG_NO_RES_RANGE
                )) {
                    return $ip;
                }
            }
        }
    }
    return null;
}

/**
 * 取文本中间
 *
 * @param string 原始字符串
 * @param string 左边字符串
 * @param string 右边字符串
 * @return string
 */
function getSubstr($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    $right = strpos($str, $rightStr, $left);
    if ($left < 0 or $right < $left) return '';
    return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}
/**
 * 获取操作系统
 *
 * @return string
 */
function  getOs()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return 'Other';
    }
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($agent, 'windows nt')) {
        $platform = 'Windows';
    } elseif (strpos($agent, 'macintosh')) {
        $platform = 'MacOS';
    } elseif (strpos($agent, 'ipod')) {
        $platform = 'iPod';
    } elseif (strpos($agent, 'ipad')) {
        $platform = 'iPad';
    } elseif (strpos($agent, 'iphone')) {
        $platform = 'iPhone';
    } elseif (strpos($agent, 'android')) {
        $platform = 'Android';
    } elseif (strpos($agent, 'unix')) {
        $platform = 'Unix';
    } elseif (strpos($agent, 'linux')) {
        $platform = 'Linux';
    } else {
        $platform = 'Other';
    }
    return $platform;
}
/**
 * 获取浏览器
 *
 * @return void
 */
function  getBrowser()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return 'Unknown';
    }
    $agent = $_SERVER["HTTP_USER_AGENT"];
    if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) //ie11判断
    {
        return "IE";
    } else if (strpos($agent, 'Firefox') !== false) {
        return "Firefox";
    } else if (strpos($agent, 'Chrome') !== false) {
        return "Chrome";
    } else if (strpos($agent, 'Opera') !== false) {
        return 'Opera';
    } else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false) {
        return 'Safari';
    } else {
        return 'Unknown';
    }
}
/**
 * 是否手机请求
 *
 * @return boolean
 */
function isMobileRequest()
{
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|uc|qq|wechat|micro|messenger|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
        $mobile_browser++;
    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser++;
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
    );
    if (in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser++;
    // Pre-final check to reset everything if the user is on Windows  
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        $mobile_browser = 0;
    // But WP7 is also Windows, with a slightly different characteristic  
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
        $mobile_browser++;
    if ($mobile_browser > 0)
        return true;
    else
        return false;
}
/**
 * 身份证号验证
 * @param $id
 * @return bool
 */
function isIDCard($id)
{
    $id = strtoupper($id);
    $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    $arr_split = array();
    if (!preg_match($regx, $id)) {
        return FALSE;
    }
    if (15 == strlen($id)) //检查15位
    {
        $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
        @preg_match($regx, $id, $arr_split);
        //检查生日日期是否正确
        $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) {
            return FALSE;
        } else {
            return TRUE;
        }
    } else { //检查18位
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) //检查生日日期是否正确
        {
            return FALSE;
        } else {
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int) $id[$i];
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($id, 17, 1)) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }
}
/**
 * 是否是整数
 *
 * @param string 输入内容
 * @return boolean
 */
function isInteger($input)
{
    return (ctype_digit(strval($input)));
}
/**
 * 获取一个key摘要
 *
 * @param string 原始key
 * @return string
 */
function getTicket($key)
{
    return sha1($key . (env('SYSTEM_SALT') ?? 'qfshop') . $key);
}
/**
 * 通用CURL请求函数
 *
 * @param string $url URL地址
 * @param string $method 请求方法, 支持GET/POST/PUT/DELETE/PATCH/TRACE/OPTIONS/HEAD 默认POST
 * @param mixed $data 请求数据包体
 * @param array $header 请求头 数组
 * @param array $queryParams 查询参数 数组
 * @param string $cookies 请求COOKIES字符串
 * @param int $timeout 请求超时时间，默认30秒
 * @return array 响应数组，包括header, body, detail, error
 */
function curlHelper($url, $method = 'POST', $data = null, $header = [], $queryParams = "", $cookies = "", $timeout = 60)
{
    // 构建查询参数
    if (!empty($queryParams)) {
        $queryString = http_build_query($queryParams);
        $url .= '?' . $queryString;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // 设置超时时间
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // 设置连接超时时间

    // 根据请求方法设置选项
    switch (strtoupper($method)) {
        case "POST":
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case "PUT":
        case "DELETE":
        case "PATCH":
        case "TRACE":
        case "OPTIONS":
        case "HEAD":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case "GET":
        default:
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
    }

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $output = [
        'header' => substr($response, 0, $headerSize),
        'body' => substr($response, $headerSize),
        'detail' => curl_getinfo($ch)
    ];

    curl_close($ch);
    return $output;
}

/**
 * 模拟表单上传文件请求
 * @param $$url 提交地址
 * @param $data 提交数据
 * @param $cookies 如设置了Content-Type将被自动覆写为formdata
 * ex.
 * $data = ['file'=>new \CURLFile(realpath($file_dir)),appid"=>"1234"];
 * $result = curl_form($url,$data);
 * @return mixed
 */
function curlForm($url, $data = null, $header = [], $cookies = "")
{
    $header[] = 'Content-Type: multipart/form-data';
    return curlHelper($url, "POST", $data, $header, $cookies);
}
/**
 * 多维数组合并（支持多数组）
 * @param arraylist arrayMergeMulti(['1'=>'1','2'=>'2','3'=>'3'],['4'=>'4','5'=>'5','6'=>'6'])
 * @return array
 */
function arrayMergeMulti()
{
    //获取当前方法捕获到的所有参数数组
    $args = func_get_args();
    $array = [];
    foreach ($args as $arg) {
        if (is_array($arg)) {
            foreach ($arg as $k => $v) {
                if (is_array($v)) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : [];
                    $array[$k] = arrayMergeMulti($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }

    return $array;
}
/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list   查询结果
 * @param string $field 排序的字段名
 * @param array $sortBy 排序类型
 *                      asc正向排序 desc逆向排序 nat自然排序
 * @return array|bool
 */
function listSortBy($list, $field, $sortBy = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = [];
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }
        switch ($sortBy) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }
        return $resultSet;
    }

    return false;
}

/**
 * 格式化字节大小
 * @param  number   $size       字节数
 * @param  int      $float      小数保留位数
 * @param  string   $delimiter  数字和单位分隔符
 * @return string   格式化后的带单位的大小
 */
function formatBytes($size, $float = 2, $delimiter = '')
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;

    return round($size, $float) . $delimiter . $units[$i];
}
/**
 * 生成标准UUID
 *
 * @return string
 */
function getUuid()
{
    mt_srand((float) microtime() * 10000);
    $uuid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    return $uuid;
}


/**
 * 判断是否为空参数
 * @param mixed $parm
 * @return bool
 */
function is_empty_parm(&$parm)
{
    return !(isset($parm) && '' !== $parm);
}

/**
 * 返回当前账号openid
 * $openid wxapp_openid wechat_openid
 * @return string
 */
function get_client_openid($user_id, $openid)
{
    return \think\facade\Db::name('user')->where('user_id', $user_id)->value($openid);
}

/**
 * 产生数字与字母混合随机字符串
 * @param int $len 数值长度,默认6位
 * @return string
 */
function get_randstr($len = 6)
{
    $chars = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
        'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
        'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
        'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2',
        '3', '4', '5', '6', '7', '8', '9',
    ];

    $charsLen = count($chars) - 1;
    shuffle($chars);

    $output = '';
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }

    return $output;
}

/**
 * 产生随机数值
 * @param int $len 数值长度,默认8位
 * @return string
 */
function rand_number($len = 8)
{
    $chars = str_repeat('123456789', 3);
    if ($len > 10) {
        $chars = str_repeat($chars, $len);
    }

    $chars = str_shuffle($chars);
    return mb_substr($chars, 0, $len, 'utf-8');
}

/**
 * 智能字符串模糊化
 * @param string $str 被模糊的字符串
 * @param int    $len 模糊的长度
 * @return string
 */
function auto_hid_substr(string $str, $len = 3)
{
    if (empty($str)) {
        return null;
    }

    $sub_str = mb_substr($str, 0, 1, 'utf-8');
    for ($i = 0; $i < $len; $i++) {
        $sub_str .= '*';
    }

    if (mb_strlen($str, 'utf-8') <= 2) {
        $str = $sub_str;
    }

    $sub_str .= mb_substr($str, -1, 1, 'utf-8');
    return $sub_str;
}

/**
 * 多维数组，根据某个特定字段过滤重复值
 * @return array
 */
function assoc_unique($arr, $key) {
    $tmp_arr = array();
    foreach ($arr as $k => $v) {
        if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            unset($arr[$k]);
        } else {
            $tmp_arr[] = $v[$key];
        }
    }
    sort($arr); //sort函数对数组进行排序
    return $arr;
}




function getDom($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $html = curl_exec($ch);
    curl_close($ch);
    
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    
    return $dom;
}


/**
 * 过滤标点符号并提取词语
 *
 * @param array $result 分词结果
 * @return array 过滤后的词汇
 */
function filterAndExtractWords(array $result)
{
    // 定义要过滤掉的符号，包括标点符号和其他符号
    $pattern = '/[\p{P}\p{S}]/u';

    // 提取词语、过滤掉符号并删除空的词语
    $filteredWords = array();
    foreach ($result as $item) {
        $word = preg_replace($pattern, '', $item[0]);
        // 只保留非空的词汇
        if (!empty($word)) {
            $filteredWords[] = $word;
        }
    }

    return $filteredWords;
}


/**
 * 高亮显示关键词
 * @param string $title 原始标题
 * @param string $searchTitle 搜索标题
 * @return string 带有高亮的标题
 */
function highlightKeywords($title, $searchTitle)
{
    $originalTitle = $title; // 保存原始标题

    // 使用正则表达式来高亮所有关键词
    foreach ($searchTitle as $keyword) {
        $title = preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<span>$1</span>', $title);
    }

    // 如果有关键词被高亮，添加<p>标签
    if ($title !== $originalTitle) {
        $title = '<p>' . $title . '</p>';
    }

    return $title;
}

/**
 * 判断是哪个网盘
 * @return int 网盘类型
 */
function determineIsType($url) {
    $domains = [
        'alipan.com' => 1,
        'aliyundrive.com' => 1,
        'baidu.com' => 2,
        'uc.cn' => 3,
        'xunlei.com' => 4
    ];

    foreach ($domains as $domain => $type) {
        if (strpos($url, $domain) !== false) {
            return $type;
        }
    }

    // 默认值是夸克网盘，返回 0
    return 0;
}

/**
 * 解析网盘分享链接
 * 
 * @param string $input 输入的链接文本，可以是多行
 * @return array 解析后的链接数组，每个元素包含url、title和code
 */
function parsePanLinks($input)
{
    // 分割多行输入
    $links = explode("\n", $input);
    
    // 去掉数组元素中的空白字符
    $links = array_map('trim', $links);
    
    // 过滤掉空值的数组元素
    $links = array_filter($links);
    
    $parsedLinks = array_values(array_filter(array_map(function ($item) {
        // 统一处理百度网盘分享格式，同时匹配两种格式
        if (preg_match('/链接:?\s*(https?:\/\/[^\s]+)\s*提取码:?\s*([a-zA-Z0-9]{4})/i', $item, $matches)) {
            $url = trim($matches[1]);
            $code = trim($matches[2]);
            // 确保URL中包含提取码
            if (!empty($code) && strpos($url, '?pwd=') === false) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . 'pwd=' . $code;
            }
            return [
                'url' => $url,
                'title' => '',
                'code' => $code
            ];
        }
        
        // 提取 URL
        if (!preg_match('/https?:\/\/[^\s]+/', $item, $matches)) {
            return null; // 没有匹配到 URL，直接丢弃
        }
    
        $url = trim($matches[0]);
        $code = '';
    
        // 提取提取码（?pwd= 或 , 分割）
        if (preg_match('/\?pwd=([^,\s&]+)/', $item, $pwdMatch)) {
            $code = trim($pwdMatch[1]);
        } elseif (preg_match('/,\s*([a-zA-Z0-9]{4})\s*$/', $item, $commaMatch)) {
            $code = trim($commaMatch[1]);
            // 添加提取码到URL
            if (!empty($code)) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . 'pwd=' . $code;
            }
        } elseif (preg_match('/提取码:?\s*([a-zA-Z0-9]{4})/i', $item, $codeMatch)) {
            $code = trim($codeMatch[1]);
            // 添加提取码到URL
            if (!empty($code)) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . 'pwd=' . $code;
            }
        }
    
        // 返回结果时，确保 title 保持为空字符串
        return [
            'url' => $url,
            'title' => '',
            'code' => $code
        ];
    }, $links)));
    
    // 去重，使用 'url' 字段来去重
    $uniqueUrls = [];
    $result = array_filter($parsedLinks, function($item) use (&$uniqueUrls) {
        if (!in_array($item['url'], $uniqueUrls)) {
            $uniqueUrls[] = $item['url'];  // 添加到已处理的 URL 列表
            return true;  // 保留此项目
        }
        return false;  // 去掉重复的项目
    });
    
    return array_values($result);
}


/**
 * 网络资源搜索源一
 * @return array
 */   
    
 function source1($title)
 {
     $d = [];
     
     return $d;
 }
 
 /**
  * 网络资源搜索源二(5条线路)
  * 每个线路只取第一个
  * @return array
  */
 function source2($title)
 {
     $urlDefault = "http://s.kkkob.com"; //http://s.kkkob.com
     $url2 = [];
     $res = curlHelper($urlDefault."/v/api/getToken", "GET")['body'];
     $res = json_decode($res, true);
     $token = $res['token'] ?? '';
     if(empty($token)){
         return $url2;
     }
     
     $urlData = array(
         'name' => $title, 
         'token' => $token
     );
     $urlHeader = array('Content-Type: application/json');
     // 定义正则表达式模式
     $pattern = '/https:\/\/pan\.quark\.cn\/[^\s]*/';
     
     //线路2
     $res = curlHelper($urlDefault."/v/api/getJuzi", "POST", json_encode($urlData), $urlHeader)['body'];
     $res = json_decode($res, true);
     if (!empty($res['list'] ?? [])) {
         foreach ($res['list'] as $key => $value) {
             if(preg_match($pattern, $value['answer'], $matches)){
                 // 匹配成功，$matches[0] 包含了匹配到的链接
                 $link = $matches[0];
                 $url2[] = [
                     'title' => preg_replace('/\s*[\(（]?(夸克)?[\)）]?\s*/u', '', $value['question']),
                     'url' => $link
                 ];
                 break;
             }
         }
     }
     
     if(!empty($url2)){
         return $url2;
     }
     
     //线路4
     $res = curlHelper($urlDefault."/v/api/getXiaoyu", "POST", json_encode($urlData), $urlHeader)['body'];
     $res = json_decode($res, true);
     if (!empty($res['list'] ?? [])) {
         foreach ($res['list'] as $key => $value) {
             if(preg_match($pattern, $value['answer'], $matches)){
                 // 匹配成功，$matches[0] 包含了匹配到的链接
                 $link = $matches[0];
                 $url2[] = [
                     'title' => preg_replace('/\s*[\(（]?(夸克)?[\)）]?\s*/u', '', $value['question']),
                     'url' => $link
                 ];
                 break;
             }
         }
     }
     
     
     // //线路1
     // $res = curlHelper($urlDefault."/v/api/search", "POST", json_encode($urlData), $urlHeader)['body'];
     // $res = json_decode($res, true);
     // if (!empty($res['list'] ?? [])) {
     //     $item = $res['list'][0];
     //     // 使用正则表达式进行匹配
     //     if (preg_match($pattern, $item['answer'], $matches)) {
     //         // 匹配成功，$matches[0] 包含了匹配到的链接
     //         $link = $matches[0];
     //         $url2[] = [
     //             'title' => '①'.preg_replace('/\s*[\(（]?(夸克)?[\)）]?\s*/u', '', $item['question']),
     //             'url' => $link
     //         ];
     //     }
     // }
     
     return $url2;
 }
 
 
 /**
  * 网络资源搜索源三
  * @return array
  */
 function source3($title)
 {
     $url3 = [];
     $url = 'https://www.qileso.com/tag/quark?s='.$title;
     $dom = getDom($url);
     $finder = new DomXPath($dom);
     // 查询class值为list-group post-list mt-3的元素
     $nodes =$finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' list-group post-list mt-3 ')]//a");
     
     if ($nodes->length > 0) {
         $firstNode =$nodes->item(0);
         $href = $firstNode->getAttribute('href');
         
         $dom = getDom($href);
         $finder = new DomXPath($dom);
         
         // 查询包含特定前缀的href属性的所有<a>标签
         $nodes =$finder->query("//@*[starts-with(., 'https://pan.quark.cn/s/')]");
         if ($nodes->length > 0) {
             $firstNode =$nodes->item(0);
             $value = $firstNode->value;
             
             // 查询<title>元素
             $nodes =$finder->query("/html/head/title");
             
             if ($nodes->length > 0) {
                 $titleNode =$nodes->item(0);
                 $titleText =$titleNode->textContent;
                 // 去掉 " - 奇乐搜" 部分
                 $title = preg_replace('/ - 奇乐搜|网盘|夸克/', '', $titleText);
             }
             
             $url3[] = [
                 'title' => '②'.$title,
                 'url' => $value
             ];
         }
     }
     
     return $url3;
 }
 
 
 /**
  * 网络资源搜索源四
  * @return array
  */
 function source4($title)
 {
     $url = 'https://www.pansearch.me/search?keyword='.urlencode($title).'&pan=quark';
     $dom = getDom($url);
     $finder = new DomXPath($dom);
     
     // 使用 XPath 查询选择具有特定类名的元素
     $nodes =$finder->query('//div[contains(concat(" ", normalize-space(@class), " "), " whitespace-pre-wrap ") and contains(concat(" ", normalize-space(@class), " "), " break-all ")]');
 
     $results = [];
     foreach ($nodes as $node) {
         // 获取元素的内容，包括其子元素
         $content = $node->textContent;
         
         // Initialize an associative array to store parsed data
         $parsedItem = [
             'title' => '',
             'url' => ''
         ];
 
         // Use regular expressions to extract the title and url
         if (preg_match('/名称：(.*?)\n\n描述：/s', $content, $titleMatch)) {
             $parsedItem['title'] = '「推荐」'.trim($titleMatch[1]);
         }
 
         if (preg_match('/链接：(https:\/\/pan\.quark\.cn\/s\/[a-zA-Z0-9]+)/', $content, $urlMatch)) {
             $parsedItem['url'] = trim($urlMatch[1]);
         }
 
         if ($parsedItem['title'] && $parsedItem['url']) {
            //  $results[] = $parsedItem;
             if (strpos($parsedItem['title'], $title) !== false || strpos($title, $parsedItem['title']) !== false) {
                $results[] = $parsedItem;
            }
         }
         
         if (count($results) >= 3) {
             break;
         }
     }
     
     return $results;
 }


 /**
 * 网络资源搜索源五----音乐资源
 * @return array
 */
function source5($title)
{
    $results = [];
    $title = str_replace("音乐", "", $title);
    $url = 'https://www.yym4.com/search/'.urlencode($title);
    $dom = getDom($url);
    $finder = new DomXPath($dom);
    
    // 使用 XPath 查询选择具有特定类名的元素
    $nodes =$finder->query('//span[@class="search_result_item_dl_btn"]/a[@class="a-no-white"]');
    // 检查是否有匹配的节点
    if ($nodes->length > 0) {
        // 获取第一个匹配的链接的 href 属性
        $firstLink = $nodes->item(0)->getAttribute('href');
        // echo "第一个链接: " . $firstLink;
        $detailUrl = 'https://www.yym4.com'.$firstLink;
        $dom = getDom($detailUrl);
        $finder = new DomXPath($dom);
        // 使用 XPath 获取网盘链接（详情页中的链接）
        $panLinkNodes = $finder->query('//div[@class="detail_dl_btn"]/a[starts-with(@href, "https://pan.quark.cn")]');
        // 获取名称
        $nameNode = $finder->query('//div[@class="detail_info_detail_name"]');
        
        $parsedItem = [
            'title' => '',
            'url' => ''
        ];
        
        // 检查是否找到网盘链接
        if ($panLinkNodes->length > 0) {
            // 获取第一个网盘链接的 href 属性
            $panLink = $panLinkNodes->item(0)->getAttribute('href');
            $parsedItem['url'] = $panLink;
        }
        
        
        if ($nameNode->length > 0) {
            // 获取名称
            $name = $nameNode->item(0)->nodeValue;
            $parsedItem['title'] = '「音乐」'.$name;
        }
        
        if ($parsedItem['title'] && $parsedItem['url']) {
            if (strpos($parsedItem['title'], $title) !== false || strpos($title, $parsedItem['title']) !== false) {
                $results[] = $parsedItem;
            }
        }
        
    }
    
    return $results;
}
 