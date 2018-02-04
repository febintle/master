<?php
if (!defined('puyuetian'))
	exit('403');

if ($_G['GET']['TYPE'] == 'out') {
	UserLogout();
	setcookie('USERNAME', '', time() - 3600);
	setcookie('PASSWORD', '', time() - 3600);
	setcookie('SESSION_TOKEN', '', time() - 3600);
	header('Location:index.php?from=loginout');
}

if ($_G['USER']['ID']) {
	$_G['HTMLCODE']['TIP'] = '您已登录';
	$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php?c=user"';
	$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
} else {
	$_G['HTMLCODE']['OUTPUT'] .= template('login', TRUE);
}
