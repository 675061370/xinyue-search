<?php
return array(
	/* ------系统------ */
	//系统名称
	'name' => '搜索管理系统',
	//系统版本
	'version' => '1.0',
	//系统powered
	'powered' => '后台管理系统',
	//系统脚部信息
	'footerInfo' => '',


	/* ------配置------ */
	'php' => '7.2.0', //最低要求
	'php_t' => '7.2.5', //推荐配置

	/* ------站点------ */
	//数据库文件
	'sqlFileName' => 'data.sql',
	//初始化商品文件
	'sqlFileGoods' => 'goods.sql',
	//数据库名
	'dbName' => '',
	//数据库表前缀
	'dbPrefix' => 'qf_',
	//站点名称
	'siteName' => '',
	//需要读写权限的目录
	'dirAccess' => array(
		'/',
		'../uploads',
	),
	/* ------写入数据库完成后处理的文件------ */
	'handleFile' => 'main.php',
	/* ------安装验证/生成文件;非云平台安装有效------ */
	'installFile' => './install.lock',
	'alreadyInstallInfo' => '你已经安装过该系统，如果想重新安装，请先删除站点install目录下的 install.lock 文件，然后再尝试安装！',
);
