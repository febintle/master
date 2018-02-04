<?php
if (!defined('puyuetian'))
	exit('403');

$_G['TEMP']['CHKENVIRONMENT'] = '';
//PHP版本检测
if (version_compare(PHP_VERSION, '5.4.0') > -1) {
	//true
	$_G['TEMP']['CHKENVIRONMENT'] .= 'PHP版本<span class="pk-text-success fa fa-fw fa-check-circle-o"></span>';
} else {
	//false
	$_G['TEMP']['CHKENVIRONMENT'] .= 'PHP版本<span class="pk-text-warning fa fa-fw fa-exclamation-circle pk-cursor-pointer" title="' . PHP_VERSION . '版本php可能不支持部分功能及应用，建议5.4及以上php版本，推荐5.5或5.6，hs支持7.0"></span>';
}

$_G['TEMP']['CHKENVIRONMENT'] .= '&nbsp;&nbsp;';
//读取文件检测
if (file_get_contents($_G['SYSTEM']['PATH'] . 'index.php') !== FALSE) {
	//true
	$_G['TEMP']['CHKENVIRONMENT'] .= '读取<span class="pk-text-success fa fa-fw fa-check-circle-o"></span>';
} else {
	//false
	$_G['TEMP']['CHKENVIRONMENT'] .= '读取<span class="pk-text-danger fa fa-fw fa-close pk-cursor-pointer" title="必备权限"></span>';
}

$_G['TEMP']['CHKENVIRONMENT'] .= '&nbsp;&nbsp;';
//写入文件检测
if (file_put_contents($_G['SYSTEM']['PATH'] . 'test.html', '写入权限检测') !== FALSE) {
	//true
	$_G['TEMP']['CHKENVIRONMENT'] .= '写入<span class="pk-text-success fa fa-fw fa-check-circle-o"></span>';
} else {
	//false
	$_G['TEMP']['CHKENVIRONMENT'] .= '写入<span class="pk-text-danger fa fa-fw fa-close pk-cursor-pointer" title="建议权限"></span>';
}

$_G['TEMP']['CHKENVIRONMENT'] .= '&nbsp;&nbsp;';
//删除文件检测
if (unlink($_G['SYSTEM']['PATH'] . 'test.html') !== FALSE) {
	//true
	$_G['TEMP']['CHKENVIRONMENT'] .= '删除<span class="pk-text-success fa fa-fw fa-check-circle-o"></span>';
} else {
	//false
	$_G['TEMP']['CHKENVIRONMENT'] .= '删除<span class="pk-text-danger fa fa-fw fa-close pk-cursor-pointer" title="建议权限"></span>';
}

$_G['TEMP']['CHKENVIRONMENT'] .= '&nbsp;&nbsp;';
//magic_quotes_gpc()函数关闭
if (get_magic_quotes_gpc()) {
	//true
	$_G['TEMP']['CHKENVIRONMENT'] .= 'magic_quotes_gpc()函数关闭<span class="pk-text-danger fa fa-fw fa-close pk-cursor-pointer" title="HS建议关闭该函数，否则部分功能或插件将无法正常运行"></span><a target="_blank" class="pk-hover-underline pk-text-primary" href="http://www.hadsky.com/read-1320-1.html">magic_quotes_gpc()函数关闭方法</a>';
} else {
	//false
	$_G['TEMP']['CHKENVIRONMENT'] .= 'magic_quotes_gpc()函数关闭<span class="pk-text-success fa fa-fw fa-check-circle-o"></span>';
}
