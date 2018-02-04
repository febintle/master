<?php
if (!defined('puyuetian'))
	exit('403');

/*
 * 用户登录检测，若登录用户信息保存在$_G['USER']数组内
 * $_G['USER']['ID']为$LoginUserId的简写
 * $_G['USER']为$LoginUserArray的简写
 */

//user auto login
//用户身份验证
$_G['USER'] = UIA();

//读取用户组数据
if ($_G['USER']['ID'] && $_G['USER']['GROUPID']) {
	$_G['USERGROUP'] = $_G['TABLE']['USERGROUP'] -> getData($_G['USER']['GROUPID']);
	standardArray($_G['USERGROUP']);
	$_G['USER']['READLEVEL'] = $_G['USERGROUP']['READLEVEL'];
} else {
	$_G['USERGROUP'] = FALSE;
}

//当前用户未读消息数
if ($_G['USER']['ID']) {
	$_G['USERMESSAGE']['UNREADCOUNT'] = $_G['TABLE']['USER_MESSAGE'] -> getCount(array('islook' => 0, 'uid' => $_G['USER']['ID']));
} else {
	$_G['USER']['ID'] = 0;
	$_G['USERMESSAGE']['UNREADCOUNT'] = 0;
}

//防止csrf攻击
$_G['CHKCSRFVAL'] = md5(key_endecode(md5($_G['USER']['SESSION_TOKEN'])));

//用户数据前端化
$showstrs = explode(',', 'id,groupid,username,sex,nickname,tiandou,jifen,readlevel,birthday,email,qq,phone,sign');
$_G['SET']['EMBED_HEADMARKS'] .= '<script>var $_USER=[];';
foreach ($showstrs as $value) {
	$_G['SET']['EMBED_HEADMARKS'] .= '$_USER["' . strtoupper($value) . '"]="' . str_replace('"', '\"', str_replace("\r", '', str_replace("\n", '', $_G['USER'][strtoupper($value)]))) . '";';
}
$_G['SET']['EMBED_HEADMARKS'] .= '$_USER["QUANXIAN"]="' . getUserQX() . '";';
$_G['SET']['EMBED_HEADMARKS'] .= '$_USER["CHKCSRFVAL"]="' . $_G['CHKCSRFVAL'] . '";$_USER["C"]="' . Cstr($_G['GET']['C'], $_G['SET']['DEFAULTPAGE'], TRUE, 1, 255) . '";</script>';
unset($showstrs, $value);
