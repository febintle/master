<?php
if (!defined('puyuetian'))
	exit('Not Found puyuetian!Please contact QQ632827168');

$_G['STRING']['UPPERCASE'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$_G['STRING']['LOWERCASE'] = 'abcdefghijklmnopqrstuvwxyz';
$_G['STRING']['NUMERICAL'] = '1234567890';
$_G['STRING']['SAFECHARS'] = $_G['STRING']['UPPERCASE'] . $_G['STRING']['LOWERCASE'] . $_G['STRING']['NUMERICAL'] . '_';
$_G['STRING']['BBCODEMARKS'] = '<b><i><u><strong><font><pre><code><p><span><table><tbody><tr><td><th><a><div><em><h1><h2><h3><h4><h5><h6><img><label><ul><ol><li><br>';
$_G['STRING']['BBCODEATTRS'] = 'class,style,href,target,src,width,height,title,alt,border,align';
$_G['DATETIME']['DATE'] = date('Y-m-d', time());
$_G['DATETIME']['TIME'] = date('H:i:s', time());
$_G['RND'] = rand(1000, 9999);
//网站域名
$_G['SYSTEM']['DOMAINS'] = explode(':', $_SERVER['HTTP_HOST']);
$_G['SYSTEM']['DOMAIN'] = strtolower($_G['SYSTEM']['DOMAINS'][0]);
$_G['SYSTEM']['PORT'] = $_G['SYSTEM']['DOMAINS'][1] ? $_G['SYSTEM']['DOMAINS'][1] : '';
$_G['SYSTEM']['CLIENTIP'] = $_SERVER['REMOTE_ADDR'];
$_G['SYSTEM']['SERVERIP'] = $_SERVER['SERVER_ADDR'];
$_G['SYSTEM']['LOCATION'] = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . "://{$_G['SYSTEM']['DOMAIN']}" . ($_G['SYSTEM']['PORT'] ? ':' . $_G['SYSTEM']['PORT'] : '') . "{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}";
$_G['SYSTEM']['REFERER'] = $_SERVER['HTTP_REFERER'];

//puyuetian框架默认错误模板
$_G['HTMLCODE']['ERROR'] = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error - Powered by puyuetian</title><style>*{font-family:"microsoft yahei"}a {color:black}</style></head><body><div style="padding:10px;margin:40px auto;text-align:center"><h1>Error</h1><h3>PuyuetianPHP operation error</h3><h5><a href="http://www.puyuetian.com">http://www.puyuetian.com</a></h5></div><div style="font-size:14px;margin:10px auto;width:100%;max-width:480px">{$err}</div></body></html>';
