<?php
if (!defined('puyuetian'))
	exit('Not Found puyuetian!Please contact QQ632827168');

$a = Cstr(strtolower($_GET['a']), FALSE, $_G['STRING']['LOWERCASE'] . $_G['STRING']['NUMERICAL'] . '_:', 3, 255);
if ($a && strpos($a, ':') === FALSE) {
	$a .= ':index';
}
if ($a) {
	$a = explode(':', $a);
	if (count($a) == 2) {
		$_G['SYSTEM']['APPDIR'] = $a[0];
		$_G['SYSTEM']['APPFILE'] = $a[1];
		$_G['SYSTEM']['APPPATH'] = "{$_G['SYSTEM']['PATH']}/app/{$_G['SYSTEM']['APPDIR']}/{$_G['SYSTEM']['APPFILE']}.php";
		if (file_exists($_G['SYSTEM']['APPPATH']) && $_G['SET']['APP_' . strtoupper($_G['SYSTEM']['APPDIR']) . '_LOAD']) {
			require $_G['SYSTEM']['APPPATH'];
		} else {
			$_G['HTMLCODE']['TIP'] = "不存在的应用或未启用！";
			$_G['HTMLCODE']['OUTPUT'] .= template('tip', true);
		}
	} else {
		$_G['HTMLCODE']['TIP'] = "非法的插件请求！";
		$_G['HTMLCODE']['OUTPUT'] .= template('tip', true);
	}

} else {
	$_G['HTMLCODE']['TIP'] = "非法的请求参数！";
	$_G['HTMLCODE']['OUTPUT'] .= template('tip', true);
}
