<?php
if (!defined('puyuetian'))
	exit('403');

if ($_G['GET']['DO'] == 'update') {
	$ud = file_get_contents("http://www.hadsky.com/index.php?c=app&a=zhanzhang:index2&s=update&nowversion={$nowversion}&yuncheckcode={$YUNCHECKCODE}&domain={$_G['SYSTEM']['DOMAIN']}&rnd={$_G['RND']}");
	$ud = trim($ud, "\x00..\x1F");
	if ($ud == 404) {
		$r = '您的网站未绑定官方账号，无法自动升级';
	} else {
		$r = json_decode($ud, TRUE);
		if ($r['status'] == 'ok' && $r['content'] && $r['newversion']) {
			$up = "{$_G['SYSTEM']['PATH']}/app/superadmin/updatezip/{$r['newversion']}.zip";
			$r = file_put_contents($up, base64_decode($r['content']));
			if ($r) {
				$zip = new ZipArchive;
				$res = $zip -> open($up);
				if ($res === TRUE) {
					$zip -> extractTo($_G['SYSTEM']['PATH']);
					$zip -> close();
					$r = '升级成功&nbsp;<a target="_top" class="pk-text-primary pk-hover-underline" href="index.php?c=app&a=superadmin:index">确认</a>';
				} else {
					$r = '解压失败：' . $res;
				}
			} else {
				$r = '写入失败';
			}
		} elseif ($r['status'] == 'no') {
			$r = '升级失败';
		} else {
			$r = '校验提示：' . $ud;
		}
	}
}
exit($r);
