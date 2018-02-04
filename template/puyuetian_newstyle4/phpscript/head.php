<?php
if (!defined('puyuetian'))
	exit('403');

if ($_G['SET']['TEMPLATE_PUYUETIAN_NEWSTYLE4_ACTIVESHOW']) {

} elseif ($_G['GET']['C'] == 'home' || (!$_G['GET']['C'] && $_G['SET']['DEFAULTPAGE'] == 'home')) {
	//站点信息
	$_G['SET']['SUMPOSTRR'] = $_G['TABLE']['READ'] -> getCount(array('del' => 0)) + $_G['TABLE']['REPLY'] -> getCount(array('del' => 0));
	$_G['SET']['YESTODAYPOSTRR'] = $_G['TABLE']['READ'] -> getCount('where `del`=0 and `posttime`>' . (strtotime(date('Y-m-d 00:00:00', time())) - 86400) . ' and `posttime`<' . (strtotime(date('Y-m-d 23:59:59', time())) - 86400)) + $_G['TABLE']['REPLY'] -> getCount('where `del`=0 and `posttime`>' . (strtotime(date('Y-m-d 00:00:00', time())) - 86400) . ' and `posttime`<' . (strtotime(date('Y-m-d 23:59:59', time())) - 86400));
	$_G['SET']['TODAYPOSTRR'] = $_G['TABLE']['READ'] -> getCount('where `del`=0 and `posttime`>' . strtotime(date('Y-m-d 00:00:00', time())) . ' and `posttime`<' . strtotime(date('Y-m-d 23:59:59', time()))) + $_G['TABLE']['REPLY'] -> getCount('where `del`=0 and `posttime`>' . strtotime(date('Y-m-d 00:00:00', time())) . ' and `posttime`<' . strtotime(date('Y-m-d 23:59:59', time())));
	$_G['SET']['MEMBERCOUNT'] = $_G['TABLE']['USER'] -> getCount();
}

$gpshtml = '';
switch ($_G['GET']['C']) {
	case 'list' :
		if ($_G['GET']['SORTID']) {
			$_sortid = $_G['GET']['SORTID'];
			for ($i = 0; $i < 99; $i++) {
				$sortdata = $_G['TABLE']['READSORT'] -> getData($_sortid);
				if ($sortdata) {
					$_sortid = $sortdata['pid'];
					$gpshtml = '&nbsp;&raquo;&nbsp;<a class="pk-hover-underline" href="' . ReWriteURL('list', "sortid={$sortdata['id']}&page=1") . '">' . $sortdata['title'] . '</a>' . $gpshtml;
				} else {
					break;
				}
			}
			$gpshtml = '&nbsp;&raquo;&nbsp;<a class="pk-hover-underline" href="' . ReWriteURL('forum', '') . '">版块列表</a>' . $gpshtml;
		}
		break;
	case 'forum' :
		if ($_G['GET']['ID']) {
			$_id = $_G['GET']['ID'];
			for ($i = 0; $i < 99; $i++) {
				$sortdata = $_G['TABLE']['READSORT'] -> getData($_id);
				if ($sortdata) {
					$_id = $sortdata['pid'];
					$gpshtml = '&nbsp;&raquo;&nbsp;<a class="pk-hover-underline" href="' . ReWriteURL('forum', "id={$sortdata['id']}&page=1") . '">' . $sortdata['title'] . '</a>' . $gpshtml;
				} else {
					break;
				}
			}
		} else {
			$gpshtml .= '&nbsp;&raquo;&nbsp;<a href="javascript:">版块列表</a>';
			break;
		}
		break;
	case 'read' :
		$readdata = $_G['TABLE']['READ'] -> getData(Cnum($_G['GET']['ID']));
		if ($readdata) {
			global $sortid;
			$_sortid = $sortid = $readdata['sortid'];
			for ($i = 0; $i < 99; $i++) {
				$sortdata = $_G['TABLE']['READSORT'] -> getData($_sortid);
				if ($sortdata) {
					$_sortid = $sortdata['pid'];
					$gpshtml = '&nbsp;&raquo;&nbsp;<a class="pk-hover-underline" href="' . ReWriteURL('list', "sortid={$sortdata['id']}&page=1") . '">' . $sortdata['title'] . '</a>' . $gpshtml;
				} else {
					break;
				}
			}
			$gpshtml .= '&nbsp;&raquo;&nbsp;<a href="javascript:">' . $readdata['title'] . '</a>';
		}
		break;
	case 'user' :
		$userdata = $_G['TABLE']['USER'] -> getData(Cnum($_G['GET']['ID']));
		if ($userdata) {
			$gpshtml .= '&nbsp;&raquo;&nbsp;<a class="pk-hover-underline" href="' . ReWriteURL('user', "id={$userdata['id']}&page=1") . '">' . $userdata['nickname'] . '的个人信息</a>';
		}
		break;
	case 'app' :
		$gpshtml .= '&nbsp;&raquo;&nbsp;<a href="javascript:">应用</a>';
		break;
	default :
		break;
}
$_G['TEMP']['GPSHTML'] = $gpshtml;
