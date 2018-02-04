<?php
if (!defined('puyuetian'))
	exit('403');

/*
 * 用户自定义函数
 */
function newBBcode($content, $type = 'read', $userqxs = NULL, $userdata = NULL, $return = '[Image]') {
	global $_G;
	if ($userqxs === NULL)
		$userqxs = $_G['USER']['QUANXIAN'];
	if ($userdata === NULL)
		$userdata = $_G['USER']['DATA'];
	$r = '';
	if ($type == 'read' || $type == 'reply') {
		if (InArray($userqxs, 'htmlcode')) {
			$r = $content;
		} elseif (InArray($userqxs, 'bbcode')) {
			$bbcode = JsonData($userdata, 'htmlcodemarks');
			$bbattr = JsonData($userdata, 'htmlcodeattrs');
			if (!$bbcode)
				$bbcode = FALSE;
			if (!$bbattr)
				$bbattr = FALSE;
			$r = BBcode($content, $bbcode, $bbattr);
		} else {
			$r = htmlspecialchars(strip_tags($content, ''));
		}
	} elseif ($type == 'sign') {
		if (InArray($userqxs, 'sign')) {
			$bbcode = JsonData($userdata, 'signcodemarks');
			$bbattr = JsonData($userdata, 'signcodeattrs');
			if (!$bbcode)
				$bbcode = FALSE;
			else
				$bbcode = $_G['SET']['SIGNCODEMARKS'];
			if (!$bbattr)
				$bbattr = FALSE;
			else
				$bbattr = $_G['SET']['SIGNCODEATTRS'];
			$r = BBcode($content, $bbcode, $bbattr);
		}
	}
	if (!$r)
		$r = $return;
	return $r;
}

function NewMessage($uid, $content, $fid = 0, $safetype = 0) {
	global $_G;
	$array['uid'] = Cnum($uid);
	$array['fid'] = Cnum($fid);
	if ($safetype == 0) {
		$array['content'] = htmlspecialchars(strip_tags($content), ENT_QUOTES);
	} elseif ($safetype == 1) {
		$array['content'] = BBcode($content);
	} else {
		$array['content'] = $content;
	}
	$array['addtime'] = time();
	$array['islook'] = 0;
	$_G['TABLE']['USER_MESSAGE'] -> newData($array);
}

function getZoneTimeline($uid) {
	global $_G;
	$__oldid = $_G['TABLE']['READ'] -> getOldId("where `uid`={$uid}");
	$__newid = $_G['TABLE']['READ'] -> getNewId("where `uid`={$uid}");
	if ($__oldid && $__newid) {
		$__newtime = $_G['TABLE']['READ'] -> getData($__newid);
		$__oldtime = $_G['TABLE']['READ'] -> getData($__oldid);
		$__newtime = date('Ym', $__newtime['posttime']);
		$__oldtime = date('Ym', $__oldtime['posttime']);
		for ($__i = 0; ($__oldtime < $__newtime) && $__i < ZONE_TIMELINENUM; $__i++) {
			$ZONE_TIMELINE .= "
				<a class='blog-timeline-a' href='" . ReWriteURL('zone', "uid={$uid}&timeline={$__newtime}&page=1") . "'>
					" . substr($__newtime, 0, 4) . '年' . substr($__newtime, strlen($__newtime) - 2) . "月
				</a>
				";
			if (substr($__newtime, strlen($__newtime) - 2) == '01') {
				//若当前为1月，上一月则为上一年12月
				$__newtime = intval((substr($__newtime, 0, 4) - 1) . '12');
			} else {

				$__newtime--;
			}
		}
	}
	return $ZONE_TIMELINE;
}

function UserDataChange($array, $uid = FALSE, $way = '+') {
	global $_G;
	if ($uid === FALSE) {
		$uid = $_G['USER']['ID'];
	}
	if (!$uid) {
		return FALSE;
	}
	if ($way) {
		$olddata = $_G['TABLE']['USER'] -> getData($uid);
		if ($olddata) {
			foreach ($array as $key => $value) {
				if ($way == '+') {
					$array[$key] = $olddata[$key] + $value;
				} elseif ($way == '-') {
					$array[$key] = $olddata[$key] - $value;
				}
			}
		} else {
			return FALSE;
		}
	}
	$array['id'] = $uid;
	return $_G['TABLE']['USER'] -> NewData($array);
}

//版块遍历函数，调用前需定义childs=-1;
function foundchildforum($pid) {
	global $_G, $postnewreadforumlist, $childs;
	//echo $childs;
	$data = $_G['TABLE']['READSORT'] -> getDatas(0, 0, "where `pid`={$pid} order by `rank`");
	if ($data) {
		$childs++;
		foreach ($data as $array) {
			$fgf = $sqx = $disabled = '';
			for ($i = 0; $i < $childs; $i++) {
				$fgf .= '--';
			}
			if ($_G['USER']['READLEVEL'] < $array['postlevel']) {
				$sqx = " 需阅读权限达到：{$array['postlevel']}";
				$disabled = " disabled";
			}
			$postnewreadforumlist .= "<option value='{$array['id']}'{$disabled}>{$fgf}{$array['title']}{$sqx}</option>";
			foundchildforum($array['id']);
			if ($pid == 0) {
				$childs = 0;
			}
		}
	}
}

//插件前台加载函数
function LoadHadSkyPlugHOOKHTML($html) {
	global $_G;
	//读取插件数据
	if ($_G['PLUG']['DATA'][$_G['SYSTEM']['CLOADPLUGNAME']]) {
		$__temp = $_G['PLUG']['DATA'][$_G['SYSTEM']['CLOADPLUGNAME']]['P'];
		if ($__temp) {
			$__temp = explode(',', $__temp);
			foreach ($__temp as $__value) {
				$__show = explode(':', $__value);
				if (count($__show) == 2) {
					$_G['HOOK'][strtoupper($__show[0])][strtoupper($__show[1])] .= $html;
				}
			}
		}
	}
}

function Post(&$postarray, $type = 'read', $mc = FALSE) {
	global $_G;
	if (!$postarray['id']) {
		$postarray['id'] = Cnum($_G['GET']['ID'], 0, TRUE, 0);
	}
	InArray(getUserQX(), 'admin') ? $admin = TRUE : $admin = FALSE;
	InArray(getUserQX(), 'superman') ? $superman = TRUE : $superman = FALSE;
	//编辑时检测版主
	if ($postarray['id']) {
		if ($type == 'reply') {
			$_ls = $_G['TABLE']['REPLY'] -> getData($postarray['id']);
			$sortid = $_G['TABLE']['READ'] -> getData($_ls['rid']);
			$sortid = $sortid['sortid'];
		} else {
			$sortid = $_G['TABLE']['READ'] -> getData($postarray['id']);
			$sortid = $sortid['sortid'];
		}
		$bkdata = $_G['TABLE']['READSORT'] -> getData($sortid);
		(InArray($bkdata['adminuids'], $_G['USER']['ID']) && $_G['USER']['ID']) ? $bkadmin = TRUE : $bkadmin = FALSE;
	}

	if ($type == 'read') {
		//========================文章发布及编辑=====================================
		if (!$postarray['title']) {
			$postarray['title'] = htmlspecialchars(strip_tags(trim(Cstr($_POST['title'], FALSE, FALSE, Cnum($_G['SET']['READTITLEMIN'], 3), Cnum($_G['SET']['READTITLEMAX'], 255))), ''), ENT_QUOTES);
		}
		if (!$postarray['content']) {
			$postarray['content'] = newBBcode(trim(Cstr($_POST['content'], FALSE, FALSE, Cnum($_G['SET']['READCONTENTMIN'], 10), Cnum($_G['SET']['READCONTENTMAX'], 25000))), $type, NULL, NULL, FALSE);
		}
		if (!$postarray['sortid']) {
			$postarray['sortid'] = Cnum($_POST['sortid'], 0, TRUE, 0);
		}
		if (!$postarray['label']) {
			$postarray['label'] = preg_replace('/[\'\"]+/', '', strip_tags($_POST['label']));
		}
		if (!$postarray['readlevel']) {
			$postarray['readlevel'] = Cnum($_POST['readlevel'], 0, TRUE, 0, Cnum(getUserQX($_G['USER']['ID'], 'readlevel')));
		}
		if (InArray(getUserQX(), 'admin')) {
			if (!$postarray['top']) {
				$postarray['top'] = Cnum($_POST['top'], 0, TRUE, 0, 1);
			}
			if (!$postarray['high']) {
				$postarray['high'] = Cnum($_POST['high'], 0, TRUE, 0, 1);
			}
		}
		if (!$postarray['replyafterlook']) {
			$postarray['replyafterlook'] = Cnum($_POST['replyafterlook'], 0, TRUE, 0, 1);
		}

		//非超级管理员管理员无法设置精华和置顶
		if (!$superman) {
			unset($array['high'], $array['top']);
		}

		if (!($postarray['content'] && $postarray['title'])) {
			return '发布的内容为空或内容过长';
		}

		if ($postarray['id']) {
			//============================编辑文章===========================
			if (!InArray(getUserQX(), 'editread')) {
				return '您无权编辑！';
			}

			$readdata = $_G['TABLE']['READ'] -> getData($postarray['id']);
			if (!$readdata) {
				return '未找到该帖！';
			}

			if (!(($readdata['uid'] && $readdata['uid'] == $_G['USER']['ID']) || $admin || $bkadmin)) {
				return '您无权管理该文章！';
			}

			/*
			 //检查字段
			 $cols = $_G['TABLE']['READ'] -> getColumns();
			 foreach ($postarray as $key => $value) {
			 if (!$cols[$key]) {
			 unset($postarray[$key]);
			 }
			 }
			 */

			if (!($_G['TABLE']['READ'] -> newData($postarray))) {
				return mysql_error();
			}
			//if ($mc) {
			return $postarray['id'];
			//} else {
			//	return TRUE;
			//	header("Location:index.php?c=read&id={$postarray['id']}&page=1");
			//}
		} else {
			//===============================发布文章==============================
			if (!InArray(getUserQX(), 'postread')) {
				return '您无权发帖！<br>可能原因：未登录';
			}

			$readsortdata = $_G['TABLE']['READSORT'] -> getData(Cnum($postarray['sortid']));

			if ($readsortdata['banpostread']) {
				return '该版块禁止发布文章';
			}

			if (!chkReadSortQx($postarray['sortid'])) {
				return '您的权限不足以在该版块发帖';
			}

			if (!$postarray['uid']) {
				$postarray['uid'] = $_G['USER']['ID'];
			}
			if (!$postarray['postip']) {
				$postarray['postip'] = getClientInfos('ip');
			}
			if (!$postarray['posttime']) {
				$postarray['posttime'] = time();
			}
			if (!$postarray['activetime']) {
				$postarray['activetime'] = time();
			}

			if (!($_G['TABLE']['READ'] -> newData($postarray))) {
				return mysql_error();
			}

			if (!$postarray['del'] && $_G['USER']['ID']) {
				UserDataChange(array("jifen" => Cnum($_G['SET']['POSTREADJIFEN']), "tiandou" => Cnum($_G['SET']['POSTREADTIANDOU'])));
			}

			$newid = $_G['TABLE']['READ'] -> getId();
			//if ($mc) {
			return $newid;
			//} else {
			//	return TRUE;
			//	header("Location:index.php?c=read&id={$newid}&page=1");
			//}
		}
	} elseif ($type == 'reply') {
		//===========================回复发布及编辑=================================
		if (!$postarray['content']) {
			$postarray['content'] = newBBcode(trim(Cstr($_POST['content'], FALSE, FALSE, Cnum($_G['SET']['REPLYCONTENTMIN'], 1), Cnum($_G['SET']['REPLYCONTENTMAX'], 765))), $type, NULL, NULL, FALSE);
		}

		if (!$postarray['content']) {
			return '回复的内容为空或数据不合法！';
		}

		if ($postarray['id']) {
			//==========================编辑回复====================================
			if (!InArray(getUserQX(), 'editreply')) {
				return '您无权编辑！';
			}

			$replydata = $_G['TABLE']['REPLY'] -> getData($postarray['id']);
			if (!$replydata) {
				return '未找到该帖！';
			}

			if (!(($replydata['uid'] && $replydata['uid'] == $_G['USER']['ID']) || $admin || $bkadmin)) {
				return '您无权管理该回复！';
			}

			if (!($_G['TABLE']['REPLY'] -> newData($postarray))) {
				return mysql_error();
			}

			//if ($mc) {
			return $replydata['rid'];
			//} else {
			//	return TRUE;
			//	header("Location:index.php?c=read&id={$replydata['rid']}&page=1");
			//}
		} else {
			//==========================发布回复====================================
			if (!$postarray['rid']) {
				$postarray['rid'] = Cnum($_POST['rid'], FALSE, TRUE, 1);
			}

			if (!InArray(getUserQX(), 'postreply')) {
				return '您无权回复！<br>可能原因：未登录';
			}

			$readdata = $_G['TABLE']['READ'] -> getData($postarray['rid']);
			if (!$readdata) {
				return '回复目标错误';
			}

			if (!chkReadSortQx($readdata['sortid'], 'replylevel')) {
				return '您的权限不足以在该版块回复';
			}

			if (!$postarray['fnum']) {
				$postarray['fnum'] = $readdata['fs'] + 1;
			}
			if (!$postarray['uid']) {
				$postarray['uid'] = $_G['USER']['ID'];
			}
			if (!$postarray['postip']) {
				$postarray['postip'] = getClientInfos('ip');
			}
			if (!$postarray['posttime']) {
				$postarray['posttime'] = time();
			}

			if (!($_G['TABLE']['REPLY'] -> newData($postarray))) {
				return mysql_error();
			}

			$replyid = $_G['TABLE']['REPLY'] -> getId();

			$array['id'] = $readdata['id'];
			$array['fs'] = $readdata['fs'] + 1;
			$array['activetime'] = time();
			$array['replyid'] = $replyid;
			$_G['TABLE']['READ'] -> newData($array);

			//通知被回复的作者和人员
			$tzbl = FALSE;
			if (substr($postarray['content'], 0, 1) == '@') {
				$epos = strpos($postarray['content'], ':');
				if ($epos) {
					$telluid = substr($postarray['content'], 1, $epos - 1);
					if (Cnum($telluid)) {
						$telluserdata = $_G['TABLE']['USER'] -> getData($telluid);
						if ($telluserdata) {
							$tzbl = TRUE;
							NewMessage($telluid, "<a href='index.php?c=user&id={$_G['USER']['ID']}' target='_blank'>{$_G['USER']['NICKNAME']}</a> 在 <a href='index.php?c=read&id={$readdata['id']}' target='_blank'>{$readdata['title']}</a> 中回复您的回复，快去看看吧~", 0, 2);
							if (function_exists('sendmail') && $_G['SET']['APP_SYSTEMEMAIL_REPLYSENDEMAIL'] && filter_var($telluserdata['email'], FILTER_VALIDATE_EMAIL)) {
								sendmail($telluserdata['email'], $_G['SET']['LOGOTEXT'] . ' - 您的回复收到了新回复', "<a target='_blank' href='http://{$_G['SYSTEM']['DOMAIN']}/index.php?c=user&id={$_G['USER']['ID']}'>{$_G['USER']['NICKNAME']}</a> 在 <a href='http://{$_G['SYSTEM']['DOMAIN']}/index.php?c=read&id={$readdata['id']}' target='_blank'>{$readdata['title']}</a> 中回复您的回复，快去看看吧~");
							}
						}
					}
				}
			}

			if (!$tzbl && $_G['USER']['ID'] != $readdata['uid']) {
				NewMessage($readdata['uid'], "<a href='index.php?c=user&id={$_G['USER']['ID']}' target='_blank'>{$_G['USER']['NICKNAME']}</a> 回复了你的文章 <a href='index.php?c=read&id={$readdata['id']}' target='_blank'>{$readdata['title']}</a> ，快去看看吧~", 0, 2);
				$readuserdata = $_G['TABLE']['USER'] -> getData($readdata['uid']);
				if (function_exists('sendmail') && $_G['SET']['APP_SYSTEMEMAIL_REPLYSENDEMAIL'] && filter_var($readuserdata['email'], FILTER_VALIDATE_EMAIL)) {
					sendmail($readuserdata['email'], $_G['SET']['LOGOTEXT'] . ' - 您的文章收到了新回复', "<a target='_blank' href='http://{$_G['SYSTEM']['DOMAIN']}/index.php?c=user&id={$_G['USER']['ID']}'>{$_G['USER']['NICKNAME']}</a> 回复了你的文章 <a href='http://{$_G['SYSTEM']['DOMAIN']}/index.php?c=read&id={$readdata['id']}' target='_blank'>{$readdata['title']}</a> ，快去看看吧~");
				}
			}

			if (!$postarray['del'] && $_G['USER']['ID']) {
				UserDataChange(array("jifen" => Cnum($_G['SET']['POSTREPLYJIFEN']), "tiandou" => Cnum($_G['SET']['POSTREPLYTIANDOU'])));
			}

			//if ($mc) {
			return $postarray['rid'];
			//} else {
			//	return TRUE;
			//	header("Location:index.php?c=read&id={$postarray['rid']}&page=1");
			//}
		}
	}
}

function chkReadSortQx($sortid, $qx = 'postlevel') {
	$sortid = Cnum($sortid, FALSE, TRUE, 1);
	if (!$sortid) {
		return TRUE;
	}
	global $_G;
	$sortdata = $_G['TABLE']['READSORT'] -> getData($sortid);
	if ($sortdata) {
		//检测是否是允许的用户组
		if ($sortdata['allowgroupids'] && !InArray($sortdata['allowgroupids'], $_G['USERGROUP']['ID'])) {
			return FALSE;
		}
		if ((!$_G['USERGROUP']['ID'] && (Cnum($_G['USER']['READLEVEL']) < $sortdata[$qx])) || ($_G['USERGROUP']['ID'] && (Cnum($_G['USERGROUP']['READLEVEL']) < $sortdata[$qx]))) {
			return FALSE;
		}
	}
	return TRUE;
}

//获取版块动态、发帖、回复数
function getReadCount($sortid, $type = 'count', $count = 0) {
	global $_G;
	if ($type == 'count') {
		$sql = array('sortid' => $sortid, 'del' => FALSE);
	} elseif ($type == 'today') {
		$sql = 'where `del`=false and `sortid`=' . $sortid . ' and  `posttime`>' . strtotime(date('Y-m-d 00:00:00', time())) . ' and `posttime`<' . strtotime(date('Y-m-d 23:59:59', time()));
	} else {
		return 0;
	}
	$count = $_G['TABLE']['READ'] -> getCount($sql) + $count;
	$zbk = $_G['TABLE']['READSORT'] -> getDatas(0, 0, "where `pid`={$sortid}");
	if ($zbk) {
		foreach ($zbk as $value) {
			$count = getReadCount($value['id'], $type, $count);
		}
	}
	return $count;
}

//检测用户是否具有指定权限
function getUserQX($uid = FALSE, $zd = 'quanxian') {
	global $_G;
	if ($uid === FALSE || $uid == $_G['USER']['ID']) {
		$uid = $_G['USER']['ID'];
		$userdata = $_G['USER'];
		$usergroupdata = $_G['USERGROUP'];
		$zd = strtoupper($zd);
	} else {
		$userdata = $_G['TABLE']['USER'] -> getData(Cnum($uid));
		$usergroupdata = $userdata['groupid'] ? $_G['TABLE']['USERGROUP'] -> getData($userdata['groupid']) : FALSE;
	}
	if ($usergroupdata) {
		//用户组为主
		return $usergroupdata[$zd];
	} else {
		//用户个人权限次之
		return $userdata[$zd];
	}
}
