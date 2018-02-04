<?php
if (!defined('puyuetian'))
	exit('403');

if (!$_G['GET']['T']) {
	$_G['GET']['T'] = 'search';
}
if ($_G['GET']['T'] == 'search') {
	$type = Cstr($_POST['type'], FALSE, TRUE, 1, 255);
	$value = $_POST['value'];
	if ($type && $value)
		$sql = array($type => $value);
	else
		$sql = 'order by `id` desc';
	$page = Cnum($_G['GET']['PAGE'], 1, TRUE, 1);
	$spos = ($page - 1) * 100;
	$userdatas = $_G['TABLE']['USER'] -> getDatas($spos, 100, $sql);
	if ($userdatas) {
		foreach ($userdatas as $userdata) {
			$usergroupdata = $_G['TABLE']['USERGROUP'] -> getData($userdata['groupid']);
			if (!$usergroupdata)
				$usergroupdata['usergroupname'] = '无';
			$userlist .= "
<tr>
	<td>{$userdata['id']}</td>
	<td>{$usergroupdata['usergroupname']}</td>
	<td>{$userdata['username']}</td>
	<td class='pk-hide-sm'>{$userdata['nickname']}</td>
	<td class='pk-hide-sm'>{$userdata['jifen']}</td>
	<td class='pk-hide-sm'>{$userdata['tiandou']}</td>
	<td>
		<a class='pk-hover-underline pk-text-primary' target='_blank' href='index.php?c=user&id={$userdata['id']}'>编辑</a>
		<a class='pk-hover-underline pk-text-primary' href='index.php?c=app&a=superadmin:index&s=user&t=seniormanagement&id={$userdata['id']}'>高级</a>
		<a class='pk-hover-underline pk-text-danger' href='javascript:' onclick='if(confirm(\"确认删除该用户（{$userdata['username']}）？\")){location.href=\"index.php?c=app&a=superadmin:index&s=delete&os=user&ot=search&table=user&id={$userdata['id']}&chkcsrfval={$_G['CHKCSRFVAL']}\"}'>删除</a>
		<a target='_blank' class='pk-hover-underline pk-text-primary' href='index.php?c=app&a=mysqlmanager:index&id={$userdata['id']}&table=USER&type=edit' title='需安装MySQL管理插件才能使用'>MySQL管理</a>
		<a target='_blank' class='pk-hover-underline pk-text-primary' href='index.php?c=app&a=filesmanager:index&path=" . urlencode(str_replace('\\', '/', $_G['SYSTEM']['PATH']) . "/uploadfiles/files/{$userdata['id']}") . "' title='需安装文件管理插件才能使用'>文件管理</a>
	</td>
</tr>";
		}
	}
} elseif ($_G['GET']['T'] == 'seniormanagement') {
	$userdata = $_G['TABLE']['USER'] -> getData($_G['GET']['ID']);
	if ($userdata) {
		$userdata['data'] = JsonData($userdata['data']);
	} else {
		$userdata['groupid'] = $_G['SET']['REGUSERGROUPID'];
		$userdata['quanxian'] = $_G['SET']['REGUSERQUANXIAN'];
	}
} elseif ($_G['GET']['T'] == 'group') {
	$usergroupdata = $_G['TABLE']['USERGROUP'] -> getData($_G['GET']['ID']);
	if ($usergroupdata) {
		$usergroupdata['data'] = JsonData($usergroupdata['data']);
	}
}
$_G['TEMP']['UGS'] = '';
$ugds = $_G['TABLE']['USERGROUP'] -> getDatas(0, 0);
foreach ($ugds as $value88) {
	$_G['TEMP']['UGS'] .= '<option value="' . $value88['id'] . '">' . htmlspecialchars($value88['usergroupname']) . '（ID:' . $value88['id'] . '）</option>';
}
$contenthtml = template('superadmin:user-' . $_G['GET']['T'], TRUE);
