<?php
//检测是否已经安装
if(file_exists($config['installFile'])){
	exit(get_tip_html($config['alreadyInstallInfo']));
}

//写入文件
function filewrite($file){
	@touch($file);
}