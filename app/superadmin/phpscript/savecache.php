<?php
if (!defined('puyuetian'))
	exit('403');

if ($_G['GET']['DO'] == 'refresh') {
	$path = scandir($_G['SYSTEM']['PATH'] . '/cache/');
	foreach ($path as $value) {
		if (end(explode('.', $value)) == 'html') {
			unlink($_G['SYSTEM']['PATH'] . '/cache/' . $value);
		}
	}
	$r = TRUE;
} else {
	$value = "<?php
define('HADSKY_CACHELIFE', " . Cnum($_POST['cachelife']) . ");
define('HADSKY_CACHEPAGES', '" . Cstr(strtolower($_POST['cachepages']), 'home,forum,list,read', $_G['STRING']['LOWERCASE'] . ',', 1, 255) . "');
define('HADSKY_CACHEREFRESH', " . Cnum($_POST['cacherefresh']) . ");
";
	$r = file_put_contents($_G['SYSTEM']['PATH'] . '/puyuetian/cache/config.php', $value);
}
if ($_G['GET']['JSON']) {
	$r = $r ? '操作成功' : '操作失败';
	ExitJson($r, TRUE);
} else {
	header("Location:index.php?c=app&a=superadmin:index&s=home&t=cache&pkalert=show&alert=" . urlencode('操作成功！') . "&rnd={$_G['RND']}");
}
exit();
