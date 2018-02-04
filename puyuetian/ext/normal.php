<?php
if (!defined('puyuetian'))
	exit('403');

//站点状态
if ($_G['SET']['SITESTATUS'] && $_G['GET']['C'] != 'login' && $_G['GET']['C'] != 'chklogin' && $_G['USER']['ID'] != 1 && $_G['GET']['C'] != 'app') {
	$_G['HTMLCODE']['MAIN'] .= template('null', TRUE);
	$_G['HTMLCODE']['MAIN'] .= template('siteclosed', TRUE);
	$_G['HTMLCODE']['MAIN'] .= template('null', TRUE);
	template($_G['TEMPLATE']['MAIN']);
	exit();
}

$postarray = array();