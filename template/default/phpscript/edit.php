<?php
if (!defined('puyuetian'))
	exit('403');

global $postnewreadforumlist, $childs;
$_G['TEMPLATE']['BODY'] = 'edit';
$_G['TEMP']['FORUMSHTML'] = '';
if (InArray($_G['USER']['QUANXIAN'], 'admin')) {
	$showbk = '';
} else {
	$showbk = '`show`=true and `banpostread`=0 and ';
}
$forums = $_G['TABLE']['READSORT'] -> getDatas(0, 0, 'where ' . $showbk . '`postlevel`<=' . Cnum(getUserQX(FALSE, 'readlevel')) . ' order by `rank`');
if ($forums) {
	foreach ($forums as $forum) {
		$_G['TEMP']['FORUMSHTML'] .= "<span id='forum-{$forum['id']}' data-id='{$forum['id']}' data-pid='{$forum['pid']}' data-title=\"" . htmlspecialchars($forum['title'], ENT_QUOTES) . "\" data-label=\"" . htmlspecialchars($forum['label'], ENT_QUOTES) . "\" data-banpostread=\"" . htmlspecialchars($forum['banpostread'], ENT_QUOTES) . "\">" . htmlspecialchars($forum['content'], ENT_QUOTES) . "</span>";
	}
}
