<?php
if (!defined('puyuetian'))
	exit('Not Found puyuetian!Please contact QQ632827168');
if ($_G['MYSQL']['LOCATION']) {
	if (version_compare(PHP_VERSION, '7.0.0') > -1) {
		//php7兼容处理
		require $_G['SYSTEM']['PATH'] . '/puyuetian/mysql/withphp7.php';
	}
	//连接mysql数据库
	$MYSQL_CONNECT = mysql_connect($_G['MYSQL']['LOCATION'], $_G['MYSQL']['USERNAME'], $_G['MYSQL']['PASSWORD']);
	//为防止因数据库服务器问题而导致的连接失败，此处再尝试连接3次
	if (!$MYSQL_CONNECT) {
		for ($MYSQL_CONNECT_I = 0; $MYSQL_CONNECT_I < 3; $MYSQL_CONNECT_I++) {
			$MYSQL_CONNECT_R = false;
			$MYSQL_CONNECT = mysql_connect($_G['MYSQL']['LOCATION'], $_G['MYSQL']['USERNAME'], $_G['MYSQL']['PASSWORD']);
			if ($MYSQL_CONNECT) {
				$MYSQL_CONNECT_R = true;
				break;
			}
		}
		if (!$MYSQL_CONNECT_R) {
			if (DEBUG) {
				exit('MySQL数据库连接出错:' . mysql_error());
			} else {
				exit('MySQL数据库连接出错，如果你是网站管理员，可以开启调试模式来显示错误详情');
			}
		}
	}
	//选择数据库
	$MYSQL_SELECT_DB_R = mysql_select_db($_G['MYSQL']['DATABASE'], $MYSQL_CONNECT);
	if (!$MYSQL_SELECT_DB_R) {
		exit('不存在的数据库！请创建');
	}
	//数据库编码设置
	mysql_query($_G['MYSQL']['CHARSET']);
	//防止mysql宽注入
	mysql_query("set character_set_client=binary");
	mysql_set_charset('utf8');

	//系统设置的读取,所有设置统一存储为$_G['SET']数组内
	$SET_ARRAY = array();
	$MYSQL_SET = mysql_query("select * from `{$_G['MYSQL']['PREFIX']}set`");
	if ($MYSQL_SET) {
		while ($MYSQL_SETS = mysql_fetch_assoc($MYSQL_SET)) {
			if (!$MYSQL_SETS['noload']) {
				$_G['SET'][strtoupper($MYSQL_SETS['setname'])] = $MYSQL_SETS['setvalue'];
			}
		}
	}

	//各个数据表对象的实例化，统一放置于$_G['TABLE']内，大写
	$prefixlen = strlen($_G['MYSQL']['PREFIX']);
	$MYSQL_TABLES = mysql_query("show tables from `{$_G['MYSQL']['DATABASE']}`");
	while ($tables = mysql_fetch_row($MYSQL_TABLES)) {
		if (substr($tables[0], 0, $prefixlen) == $_G['MYSQL']['PREFIX']) {
			$tablename = substr($tables[0], $prefixlen);
			$_G['TABLES'][] = $tablename;
			$_G['TABLE'][strtoupper($tablename)] = new Data($tables[0], false);
		}
	}
	//使用过后的无关变量清理
	unset($prefixlen, $tables, $tablename, $newtablename);
}
?>