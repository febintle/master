<?php
if (!defined('puyuetian'))
	exit('Not Found puyuetian!Please contact QQ632827168');

$id = Cnum($_G['GET']['ID'], 0, TRUE, 0);

if (chkReadSortQx($id, 'readlevel')) {
	$template = template('forum-2', TRUE, FALSE, FALSE);
	$forumdatas = $_G['TABLE']['READSORT'] -> getDatas(0, 0, "where `pid`={$id} and `show`=1 order by `rank`");
	if ($forumdatas) {
		foreach ($forumdatas as $forumdata) {
			$forumhtml .= template('forum-2', TRUE, $template);
		}
	}

	if ($id) {
		$pdata = $_G['TABLE']['READSORT'] -> getData($id);
		$_G['SET']['WEBTITLE'] = '版块列表 - ' . str_replace(array("\r", "\n", "\t", " "), '', (strip_tags($pdata['title'])));
		if ($pdata['content']) {
			$_G['SET']['WEBDESCRIPTION'] = str_replace(array("\r", "\n", "\t", " "), '', (strip_tags($pdata['content'])));
		}
	} else {
		$_G['SET']['WEBTITLE'] = '版块列表 - ' . $_G['SET']['WEBTITLE'];
	}

	$_G['HTMLCODE']['OUTPUT'] .= template('forum-1', TRUE) . $forumhtml . template('forum-3', TRUE);
} else {
	$_G['HTMLCODE']['TIP'] = '您的阅读权限太低或您的用户组不被允许';
	$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
}
