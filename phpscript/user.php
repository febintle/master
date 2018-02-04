<?php
if (!defined('puyuetian'))
	exit('403');

$id = Cnum($_G['GET']['ID'], $_G['USER']['ID'], TRUE, 0);
$page = Cnum($_G['GET']['PAGE'], 1, TRUE, 1);
$chkr = false;
for ($rf = 0; $rf < 1; $rf++) {
	if (!$id) {
		if (!$_G['USER']['ID']) {
			header('Location:index.php?c=login&referer=' . urlencode($_G['SYSTEM']['LOCATION']));
			exit();
		}
		$chkr = '非法的ID参数！';
		break;
	}
	if ((!InArray($_G['USERGROUP']['QUANXIAN'], 'lookuser') && $_G['USERGROUP']['ID']) || (!InArray($_G['USER']['QUANXIAN'], 'lookuser') && !$_G['USERGROUP']['ID'])) {
		$chkr = '您无权查看用户资料！';
		break;
	}

	$userdata = $_G['TABLE']['USER'] -> getData($id);

	if (!$userdata) {
		$chkr = '未找到的用户！';
		break;
	}

	$_G['SET']['WEBTITLE'] = "{$userdata['nickname']} 的个人信息";
	//用户组
	$usergroupdata = $_G['TABLE']['USERGROUP'] -> getData($userdata['groupid']);
	if (!$usergroupdata) {
		$usergroupdata['usergroupname'] = '逍遥法外（暂无用户组）';
	} else {
		$userdata['readlevel'] = $usergroupdata['readlevel'];
	}
	//隐私设置
	if (JsonData($userdata['data'], 'privacysettings') && $_G['USER']['ID'] != $userdata['id'] && $_G['USER']['ID'] != 1 && ((!InArray($_G['USERGROUP']['QUANXIAN'], 'admin') && $_G['USERGROUP']['ID']) || (!InArray($_G['USER']['QUANXIAN'], 'admin') && !$_G['USERGROUP']['ID'])) && ((!InArray($_G['USERGROUP']['QUANXIAN'], 'superman') && $_G['USERGROUP']['ID']) || (!InArray($_G['USER']['QUANXIAN'], 'superman') && !$_G['USERGROUP']['ID']))) {
		$userdata['birthday'] = $userdata['phone'] = $userdata['qq'] = $userdata['email'] = $userdata['tiandou'] = '保密';
	}
}
if ($chkr) {
	$_G['HTMLCODE']['TIP'] = $chkr;
	$_G['HTMLCODE']['TIPJS'] = 'location.href="index.php"';
	$_G['HTMLCODE']['OUTPUT'] .= template('tip', TRUE);
} else {
	$_G['HTMLCODE']['OUTPUT'] .= template('user', TRUE);
}
