<?php
if (!defined('puyuetian'))
	exit('403');

/*
 * php7兼容php5 mysql操作语法
 * by puyuetian
 * http://www.hadsky.com
 */

function mysql_connect($localhost, $username, $password) {
	return mysqli_connect($localhost, $username, $password);
}

function mysql_error($link = false) {
	if (!$link) {
		global $MYSQL_CONNECT;
		$link = $MYSQL_CONNECT;
	}
	return mysqli_error($link);
}

function mysql_select_db($database, $link) {
	return mysqli_select_db($link, $database);
}

function mysql_query($query, $link = false) {
	if (!$link) {
		global $MYSQL_CONNECT;
		$link = $MYSQL_CONNECT;
	}
	return mysqli_query($link, $query);
}

function mysql_set_charset($charset, $link = false) {
	if (!$link) {
		global $MYSQL_CONNECT;
		$link = $MYSQL_CONNECT;
	}
	return mysqli_set_charset($link, $charset);
}

function mysql_fetch_assoc($result) {
	return mysqli_fetch_assoc($result);
}

function mysql_fetch_row($result) {
	return mysqli_fetch_row($result);
}

function mysql_num_rows($result) {
	return mysqli_num_rows($result);
}

function mysql_real_escape_string($string_to_escape, $link = false) {
	if (!$link) {
		global $MYSQL_CONNECT;
		$link = $MYSQL_CONNECT;
	}
	return mysqli_real_escape_string($link, $string_to_escape);
}

function mysql_get_server_info($link = false) {
	if (!$link) {
		global $MYSQL_CONNECT;
		$link = $MYSQL_CONNECT;
	}
	return mysqli_get_server_info($link);
}
