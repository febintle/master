<?php
if (!defined('puyuetian'))
	exit('403');

if ($_G['USER']['ID']) {
	$_G['HTMLCODE']['TIP'] = '您已登录';
	$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php?c=user"';
	$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
} else {
	if ($_G['SET']['OPENREG']) {
		$_G['HTMLCODE']['OUTPUT'] .= template('reg', TRUE);
	} else {
		$_G['HTMLCODE']['TIP'] = $_G['SET']['CLOSEREGTIP'];
		$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php"';
		$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
	}
}
