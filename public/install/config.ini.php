<?php
if(!defined('HAIZAN_VERSION')) die('what do you want?');
return array(
	//数据库类型
	'DB_TYPE' => 'mysql',
	//服务器
	'DB_HOST'=>'#DB_HOST#',
	//数据库名
	'DB_NAME'=>'#DB_NAME#',
	//数据库用户名
	'DB_USER'=>'#DB_USER#',
	//数据库用户密码
	'DB_PWD'=> '#DB_PWD#',
	//数据库库表前缀
	'DB_PREFIX'=>'#DB_PREFIX#',
	// 端口
	'DB_PORT'=> '#DB_PORT#',

	//Cookie前缀 避免冲突
	'COOKIE_PREFIX' => '#COOKIE_PREFIX#',
	// 缓存前缀
	'DATA_CACHE_PREFIX' => '#DATA_CACHE_PREFIX#',
	// session 前缀
	'SESSION_PREFIX' => '#SESSION_PREFIX#',
	//authcode加密函数密钥
	"AUTHCODE" => '#AUTHCODE#',
);