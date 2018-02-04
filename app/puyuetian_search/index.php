<?php
if (!defined('puyuetian'))
	exit('403');

function searchws($str) {
	$ws = explode(' ', $_GET['w']);
	foreach ($ws as $w) {
		$str = str_replace($w, '<span class="pk-text-danger">' . $w . '</span>', $str);
	}
	return $str;
}

if (InArray($_G['USER']['QUANXIAN'], 'search')) {
	$w = trim($_GET['w']);
	if ($w) {
		$ws = explode(' ', $w);
		$mysqlws = '(`title` like ' . mysqlstr($w, TRUE, '%', TRUE) . ' or `content` like ' . mysqlstr($w, TRUE, '%', TRUE) . ' or `title` like ' . mysqlstr(str_replace(' ', '', $w), TRUE, '%', TRUE) . ' or `content` like ' . mysqlstr(str_replace(' ', '', $w), TRUE, '%', TRUE) . ' or ';
		$i = 0;
		foreach ($ws as $key => $value) {
			if ($value) {
				$mysqlws .= '`title` like ' . mysqlstr($value, TRUE, '%', TRUE) . ' or `content` like ' . mysqlstr($value, TRUE, '%', TRUE) . ' or ';
			}
			$i++;
			if ($i > 4) {
				break;
			}
		}
		$mysqlws = substr($mysqlws, 0, strlen($mysqlws) - 4) . ')';
		//exit($mysqlws);
		$template = template('puyuetian_search:list-2', TRUE, FALSE, FALSE);
		$readdatas = $_G['TABLE']['READ'] -> getDatas(0, Cnum($_G['SET']['APP_PUYUETIAN_SEARCH_SHOWCOUNT'], 100, TRUE, 1), 'where `del`=0 and ' . $mysqlws . ' order by `id` desc');
		$i = 0;
		if ($readdatas) {
			foreach ($readdatas as $readdata) {
				$i++;
				//检测是否为回复查看帖
				if ($readdata['replyafterlook']) {
					if ($_G['USER']['ID']) {
						if (!$_G['TABLE']['REPLY'] -> getId(array('rid' => $readdata['id'], 'uid' => $_G['USER']['ID'], 'del' => 0)))
							$readdata['content'] = '该文章设置了回复查看，请回复后查看内容';
					} else {
						$readdata['content'] = '您需要登录并回复后才可以查看该文章内容';
					}
				}
				//检测阅读权限是否合法
				if ((Cnum($readdata['readlevel']) > Cnum($_G['USER']['READLEVEL'])) || !chkReadSortQx($sortid, 'readlevel')) {
					$readdata['content'] = '您的阅读权限太低，无法查看该文章';
				}
				$readuserdata = $_G['TABLE']['USER'] -> getData($readdata['uid']);
				$replydata = $_G['TABLE']['REPLY'] -> getData($readdata['replyid']);
				if ($replydata['uid']) {
					$replyuserdata = $_G['TABLE']['USER'] -> getData($replydata['uid']);
				} else {
					$replyuserdata = JsonData($_G['SET']['GUESTDATA']);
				}
				$readhtml .= template(FALSE, TRUE, $template);
			}
		}
		$_G['HTMLCODE']['OUTPUT'] .= template('puyuetian_search:list-1', TRUE);
		$_G['HTMLCODE']['OUTPUT'] .= $readhtml;
		$_G['HTMLCODE']['OUTPUT'] .= template('puyuetian_search:list-3', TRUE);
	} else {
		$_G['HTMLCODE']['TIP'] = '搜索词非法或无效';
		$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
	}
} else {
	if ($_G['USER']['ID']) {
		$_G['HTMLCODE']['TIP'] = '您无权使用搜索功能';
		$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php"';
	} else {
		$_G['HTMLCODE']['TIP'] = '游客，请先登录';
		$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php?c=login"';
	}
	$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
}
