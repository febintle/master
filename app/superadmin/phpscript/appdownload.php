<?php
if (!defined('puyuetian'))
	exit('403');

if ($_G['GET']['ID'] && $_G['GET']['DO'] == 'download') {
	$ud = file_get_contents("http://www.hadsky.com/index.php?c=app&a=zhanzhang:index2&s=appdownload&id={$_G['GET']['ID']}&yuncheckcode={$YUNCHECKCODE}&domain={$_G['SYSTEM']['DOMAIN']}&rnd={$_G['RND']}");
	$ud = trim($ud, "\x00..\x1F");
	if ($ud == 404) {
		$r = '您的网站未绑定官方账号，无法下载应用';
	} else {
		$r = json_decode($ud, TRUE);
		$appname = CreateRandomString(32);
		if ($r['status'] == 'ok' && $r['content']) {
			$up = "{$_G['SYSTEM']['PATH']}/app/superadmin/appzip/{$appname}.zip";
			$r = file_put_contents($up, base64_decode($r['content']));
			if ($r) {
				$zip = new ZipArchive;
				$res = $zip -> open($up);
				if ($res === TRUE) {
					$zip -> extractTo($_G['SYSTEM']['PATH']);
					$zip -> close();
					$r = '应用下载成功，请进入<a target="_top" class="pk-text-primary pk-hover-underline" href="index.php?c=app&a=superadmin:index&PIndex=4&CIndex=0">模板中心</a>或<a target="_top" class="pk-text-primary pk-hover-underline" href="index.php?c=app&a=superadmin:index&PIndex=5&CIndex=0">应用中心</a>进行安装和设置';
				} else {
					$r = '解压失败：' . $res;
				}
			} else {
				$r = '写入失败';
			}
		} elseif ($r['status'] == 'no') {
			$r = '下载失败，请确保您的账号天豆充足';
		} else {
			$r = '校验提示：' . $ud;
		}
	}
	exit($r);
} else {
	$contenthtml = template('superadmin:home-appdownload', TRUE);
}
