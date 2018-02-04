<?php
if (!defined('puyuetian'))
	exit('403');

$id = Cnum($_G['GET']['ID'], 0, TRUE, 0);
$sortid = Cnum($_G['GET']['SORTID'], 0, TRUE, 0);
$type = $_G['GET']['TYPE'];

if ($type == 'read' || $type == 'reply') {
	if ($id) {
		//标题
		$_G['SET']['WEBTITLE'] = '编辑帖子';
		//检测版主
		if ($type == 'reply') {
			$_ls = $_G['TABLE']['REPLY'] -> getData($id);
			$_ls = $_ls['rid'];
			$sortid = $_G['TABLE']['READ'] -> getData($_ls);
			$sortid = $sortid['sortid'];
		} else {
			$sortid = $_G['TABLE']['READ'] -> getData($id);
			$sortid = $sortid['sortid'];
		}
		$bkdata = $_G['TABLE']['READSORT'] -> getData($sortid);
		(InArray($bkdata['adminuids'], $_G['USER']['ID']) && $_G['USER']['ID']) ? $bkadmin = TRUE : $bkadmin = FALSE;
		//=============================编辑帖子=============================
		if (InArray(getUserQX(), 'edit' . $type)) {
			$rrdata = $_G['TABLE'][strtoupper($type)] -> getData($id);
			if ($rrdata) {
				if (($rrdata['uid'] && $rrdata['uid'] == $_G['USER']['ID']) || InArray(getUserQX(), 'admin') || $bkadmin) {
					if ($type == 'read') {
						$rrdata['title'] = htmlspecialchars($rrdata['title'], ENT_QUOTES);
						$sortid = $rrdata['sortid'];
						$label = $rrdata['label'];
					} else {
						$readdata = $_G['TABLE']['READ'] -> getData($rrdata['rid']);
						$rrdata['title'] = htmlspecialchars('回复主题：' . $readdata['title'], ENT_QUOTES);
					}
					$rrdata['content'] = htmlspecialchars($rrdata['content'], ENT_QUOTES);
					$_G['HTMLCODE']['OUTPUT'] .= template('edit', TRUE);
				} else {
					//无权限
					$_G['HTMLCODE']['TIP'] = '您无权管理该帖！';
					$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
				}
			} else {
				//未找到
				$_G['HTMLCODE']['TIP'] = '未找到该帖！';
				$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
			}
		} else {
			//无权编辑
			$_G['HTMLCODE']['TIP'] = '您无权编辑！';
			$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
		}
	} else {
		//标题
		$_G['SET']['WEBTITLE'] = '发布帖子';
		//新发布前奏
		if (InArray(getUserQX(), 'post' . $type)) {
			$_G['HTMLCODE']['OUTPUT'] .= template('edit', TRUE);
		} else {
			//无权发布
			if ($_G['USER']['ID']) {
				$_G['HTMLCODE']['TIP'] = '您无权发帖！请联系管理员';
				$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
			} else {
				header('Location:index.php?c=login&referer=' . urlencode("index.php?c=edit&sortid={$sortid}&type={$type}&id={$id}"));
				exit();
			}
		}
	}
} else {
	//参数错误
	$_G['HTMLCODE']['TIP'] = '请求参数错误！';
	$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
}
