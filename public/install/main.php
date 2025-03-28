<?php
$username = trim($_POST['manager']);
$password = trim($_POST['manager_pwd']);
//网站名称
$site_name = addslashes(trim($_POST['sitename']));

//更新配置信息
$mysqli->query("UPDATE `{$dbPrefix}conf` SET  `conf_value` = '$site_name' WHERE conf_key='app_name'");

if(INSTALLTYPE == 'HOST'){
	        $db_str=<<<php
APP_DEBUG = true
SYSTEM_SALT= {$site_name}

[APP]
DEFAULT_TIMEZONE = Asia/Chongqing

[DATABASE]
TYPE = mysql
HOSTNAME = {$dbHost}
DATABASE = {$dbName}
USERNAME = {$dbUser}
PASSWORD = {$dbPwd}
HOSTPORT = {$dbPort}
CHARSET = utf8mb4
DEBUG = false
PREFIX = {$dbPrefix}

[LANG]
default_lang = zh-cn
php;
        // 创建数据库链接配置文件
        file_put_contents('../../.env', $db_str);
}

//插入管理员
//生成随机认证码
$salt = genRandomString(4);
$time = time();
$ip = get_client_ip();
$password = sha1($password . $salt . $password . $salt);
$url = "insert into `{$dbPrefix}admin` VALUES (1,'{$username}', '{$password}', '{$salt}', '超级管理员','','超级管理员','',0.00,1,'127.0.0.1',0,'{$time}','{$time}')";
$mysqli->query($url);

$mysqli->close();
return array('status'=>2,'info'=>'成功添加管理员<br />成功写入配置文件<br>安装完成...');
