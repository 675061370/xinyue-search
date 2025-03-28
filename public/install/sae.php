<?php
//设置storage的domain
if($_GET['step'] == 0){
	if(empty($_POST['storagedomain'])){
		$step_html = '<li class="current"><em>0</em>Storage设置</li>';
		include './templates/0.php';
		exit;
	} else {
		$_SESSION['STORAGEDOMAIN'] = $_POST['storagedomain'];
		if(!empty($_SESSION['STORAGEDOMAIN'])){
			header('location:./index.php?step=1');
			exit();	
		}
	}
}
if(!isset($_SESSION['STORAGEDOMAIN']) || empty($_SESSION['STORAGEDOMAIN'])){
	header('location:./index.php?step=0');
	exit;
}
$config['uploaddir'] = $_SESSION['STORAGEDOMAIN'];
//测试该该storage是否有效！
if(!filewrite($config['uploaddir'].'/install_file.lock')){
	exit(get_tip_html('该storage无效！'));
} else{
	file_delete($config['uploaddir'].'/install_file.lock');
}

define('SAESTOR_INSTALL_NAME', $config['uploaddir'].'/saestor_'. $_SERVER['HTTP_APPVERSION'] . '_install.lock');
$config['alreadySaeInstallInfo'] = "版本" . $_SERVER['HTTP_APPVERSION'] . "已完成安装!请删除网站根目录下的install目录！<br />如果需要重新安装，请先删除storage内的 saestor_" . $_SERVER['HTTP_APPVERSION'] . "_install.lock 文件";
if(fileExists(SAESTOR_INSTALL_NAME)){
	exit(get_tip_html($config['alreadySaeInstallInfo']));
}
if(!is_storage){
	exit(get_tip_html('请开启storage服务！'));
}
if(!is_mc){
	exit(get_tip_html('请开启memcahce服务！'));
}
if(!is_mysql){
	exit(get_tip_html('请开启mysql服务！'));
}
if(!is_kv){
	exit(get_tip_html('请开启KV数据库服务！'));
}

//SaeStorage
function SaeStorage(){
	static $SaeStorage = array();
	if(!isset($SaeStorage['SaeStorage'])){
		$SaeStorage['SaeStorage'] = new SaeStorage();
	}
	return $SaeStorage['SaeStorage'];
}
//domain 路径
function file_getdomainfilepath($filename){
	$arr=explode('/',ltrim($filename,'./'));
	if($arr[count($arr)-1] == ''){
		unset($arr[count($arr)-1]);
	}
	$domain=array_shift($arr);
	$filePath=implode('/',$arr);
	return array('domain'=>$domain,'filepath'=>$filePath);
}
//检查文件是否存在
function fileExists($filename){
	$arr=file_getdomainfilepath($filename);
	return SaeStorage()->fileExists($arr['domain'], $arr['filepath']);
}
//写入文件
function filewrite($file = ''){
	$file = empty($file) ? SAESTOR_INSTALL_NAME:$file;
	$arr=file_getdomainfilepath($file);
	SaeStorage()->write($arr['domain'], $arr['filepath'],'1');
}
//删除文件
function file_delete($filename){
	$arr=file_getdomainfilepath($filename);
	return SaeStorage()->delete($arr['domain'], $arr['filepath']);
}

//判断是否开启storage
function is_storage() {
	$s = new SaeStorage();
	if (!$s->write(SAESTOR_NAME, 'is_storage', '1')) {
		return FALSE;
	} else {
		return TRUE;
	}
}
//判断是否开启memcahce
function is_mc() {
	$mmc = @memcache_init();
	if ($mmc) {
		return TRUE;
	} else {
		return FALSE;
	}
}
//判断是否开启mysql
function is_mysql() {
	$mysql = @new SaeMysql();
	$sql = "select database()";
	$data = @$mysql->getData($sql);
	if ($data) {
		return TRUE;
	} else {
		return FALSE;
	}
}
//判断是否开启KV数据库
function is_kv(){
	$kv=new SaeKV();
	if($kv->init()){
		return TRUE;
	} else {
		return FALSE;
	}
}