<?php
if (!defined('puyuetian'))
	exit('403');

$mysql_location = $_POST['mysql_location'];
$mysql_username = $_POST['mysql_username'];
$mysql_password = $_POST['mysql_password'];
$mysql_database = $_POST['mysql_database'];
$mysql_prefix = $_POST['mysql_prefix'];
$mysql_charset = $_POST['mysql_charset'];
$adminusername = Cstr($_POST['adminusername']);
$adminpassword = Cstr($_POST['adminpassword'], FALSE, FALSE, 5, 16);
$adminemail = filter_var($_POST['adminemail'], FILTER_VALIDATE_EMAIL);

if (!$mysql_location || !$mysql_username || !$mysql_database || !$mysql_prefix || !$mysql_charset) {
	ExitJson("请填写完整MySQL数据库信息！");
}
if (!$adminusername || !$adminpassword || !$adminemail) {
	ExitJson("请填写正确的创世人信息！");
}

//mysql/config.php配置文件写入
$mysql_config = "<?php
	\$_G['MYSQL']['LOCATION'] = '{$mysql_location}';
	\$_G['MYSQL']['USERNAME'] = '{$mysql_username}';
	\$_G['MYSQL']['PASSWORD'] = '{$mysql_password}';
	\$_G['MYSQL']['DATABASE'] = '{$mysql_database}';
	\$_G['MYSQL']['CHARSET'] = '{$mysql_charset}';
	\$_G['MYSQL']['PREFIX'] = '{$mysql_prefix}';
	";
file_put_contents($mp . 'puyuetian/mysql/config.php', $mysql_config);
//连接mysql数据库
$MYSQL_CONNECT = mysql_connect($mysql_location, $mysql_username, $mysql_password);
if (!$MYSQL_CONNECT) {
	//连接失败
	ExitJson("MySQL数据库连接失败！请返回检查");
}
//选择数据库
$MYSQL_SELECT_DB_R = mysql_select_db($mysql_database, $MYSQL_CONNECT);
if (!$MYSQL_SELECT_DB_R) {
	ExitJson("不存在的数据库！请创建后再安装");
}
//数据库编码设置
mysql_query($mysql_charset);
//导入MySQL数据
$string = file_get_contents($mp . 'install/mysqldata/hadsky.sql');
//去除bom
if (ord(substr($string, 0, 1)) == 239 && ord(substr($string, 1, 1)) == 187 && ord(substr($string, 2, 1)) == 191) {
	$string = substr($string, 3);
}
//数据表前缀替换
//require "{$mp}install/mysqldata/data.php";
$string = str_replace('`pk_', "`{$mysql_prefix}", $string);
$querys = explode(";\r\n", $string);
if (count($querys) < 5) {
	$querys = explode(";\n", $string);
}
$err = 1;
$rs = '';
foreach ($querys as $query) {
	$err++;
	if (trim($query, "\x00..\x1F")) {
		$r = mysql_query($query);
		if (!$r) {
			ExitJson("出错行{$err}：" . mysql_error() . "，出错语句：" . htmlspecialchars($query));
		}
	}
}
//创始人信息更新
$s_t = CreateRandomString(7);
$key = CreateRandomString(32);
mysql_query("update `{$mysql_prefix}user` set `username`='{$adminusername}',`password`='" . md5($adminpassword) . "',`email`='{$adminemail}',`session_token`='{$s_t}' where `id`=1");
mysql_query("update `{$mysql_prefix}set` set `setvalue`='{$key}' where `setname`='key'");
//账号登录
//setcookie('UIA', key_endecode('1|' . md5(md5($adminpassword) . $s_t) . md5($s_t), 'EN', md5($key)), time() + 86400, '/', NULL, FALSE, TRUE);
//云服务域名处理
if ($_POST['hs_username']) {
	$YCCP = '../yuncheckcode.htm';
	$YUNCHECKCODE = CreateRandomString(16);
	file_put_contents($YCCP, json_encode(array('yuncheckcode' => $YUNCHECKCODE, 'deadline' => (time() + 300))));
	$r = GetPostData("http://www.hadsky.com/index.php?c=app&a=zhanzhang:index2&s=installbinding&yuncheckcode={$YUNCHECKCODE}&domain={$_G['SYSTEM']['DOMAIN']}&rnd={$_G['RND']}", "username={$_POST['hs_username']}&password=" . md5($_POST['hs_password']), 5);
	$r = json_decode($r, TRUE);
	if ($r['state'] == 'ok') {
		mysql_query("update `{$mysql_prefix}set` set `setvalue`='{$r['datas']['msg']}' where `setname`='app_hadskycloudserver_sitekey'");
		ExitJson('安装和云服务绑定成功完成', TRUE);
	} else {
		ExitJson('pkalert|安装已完成，但云服务绑定失败：' . ($r['datas']['msg'] ? $r['datas']['msg'] : '通讯失败'), TRUE);
	}
}

ExitJson('安装成功完成', TRUE);
