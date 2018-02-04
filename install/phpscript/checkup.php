<?php
if (!defined('puyuetian'))
	exit('403');

if ($_GET['submit'] == 'yes') {
	$array = array();
	//php版本
	$array['PHP版本'] = PHP_VERSION;
	if (version_compare($array['PHP版本'], '5.3') == -1 || version_compare($array['PHP版本'], '7.0') > -1) {
		$array['PHP版本'] .= '（兼容当前版本，但5.5/5.6为最佳版本）';
	}
	if (version_compare($array['PHP版本'], '5.2') == -1) {
		$array['PHP版本'] = FALSE;
	}

	//读取文件检测
	$array['文件可读'] = is_readable($mp . 'install/mysqldata/hadsky.sql') ? TRUE : FALSE;

	//写入文件检测
	$array['文件可写'] = is_writeable($mp . 'puyuetian/mysql/config.php') ? TRUE : FALSE;

	//通讯检测
	$array['与官方通讯'] = GetPostData('http://www.hadsky.com', '', 5) ? TRUE : FALSE;

	//gd
	$array['GD图片处理库'] = function_exists('gd_info') ? TRUE : FALSE;

	//mysql
	$array['MySQL扩展'] = function_exists('mysql_connect') || function_exists('mysqli_connect') ? TRUE : FALSE;

	//xml
	$array['xml文件处理'] = function_exists('simplexml_load_string') ? TRUE : FALSE;

	//json
	$array['json数据处理'] = function_exists('json_encode') ? TRUE : FALSE;

	//检测curl
	$array['启用curl功能'] = function_exists('curl_init') ? TRUE : FALSE;

	//检测scandir
	$array['启用scandir函数'] = function_exists('scandir') ? TRUE : FALSE;

	//检测fsockopen
	$array['启用fsockopen函数'] = function_exists('fsockopen') ? TRUE : FALSE;

	//检测get_magic_quotes_gpc()
	$array['禁用magic_quotes_gpc函数'] = get_magic_quotes_gpc() ? '建议关闭该函数' : TRUE;

	ExitJson($array, TRUE);
}

$HTMLCODE .= template("{$tpath}checkup.hst", TRUE);
