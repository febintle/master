<?php
if (!defined('puyuetian'))
	exit('403');

$_G['TEMPLATE']['BODY'] = 'tip';
$_G['HTMLCODE']['TIPJS'] = str_replace('\"', '"', $_G['HTMLCODE']['TIPJS']);
$_G['HTMLCODE']['TIPJS'] = str_replace('"', '\"', $_G['HTMLCODE']['TIPJS']);
