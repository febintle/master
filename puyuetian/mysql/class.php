<?php
if (!defined('puyuetian'))
	exit('Not Found puyuetian!Please contact QQ632827168');
/*
 * puyuetianPHP轻框架 核心函数
 * 作者：蒲乐天（puyuetian）
 * QQ：632827168
 * 官网：http://www.puyuetian.com
 *
 * 作者允许您转载和使用，但必须注明来自puyuetianPHP轻框架。
 */
/********************************************
 数据库操作类 - 获取、修改、添加、删除表信息，必须为id为唯一索引
 *******************************************/
class Data {
	public $id, $array, $query, $mysql_prefix, $table, $index;
	private $array_keys, $array_values;
	public function __construct($table, $prefix = true) {
		if ($prefix) {
			global $_G;
			//获取数据库表前缀
			$this -> mysql_prefix = $_G['MYSQL']['PREFIX'];
		} else {
			$this -> mysql_prefix = '';
		}
		$this -> table = $table;
	}

	public function getSql($field, $str = NULL) {
		$sql = "";
		if (is_array($field) && is_array($str)) {
			foreach ($field as $key => $value) {
				if (Cstr($field[$key], FALSE, TRUE, 1, 255)) {
					$sql .= "`{$field[$key]}`=" . mysqlstr($str[$key]) . " and ";
				}
			}
			$sql = substr($sql, 0, strlen($sql) - 5);
		} elseif (is_array($field) && (!$str && $str !== "")) {
			foreach ($field as $key => $value) {
				if (Cstr($key, FALSE, TRUE, 1, 255)) {
					$sql .= "`{$key}`=" . mysqlstr($value) . " and ";
				}
			}
			$sql = substr($sql, 0, strlen($sql) - 5);
		} elseif ($field && (!$str && $str !== "")) {
			$sql = $field;
			$w = TRUE;
		} else {
			$str = mysqlstr($str);
			if (Cstr($field, FALSE, TRUE, 1, 255)) {
				$sql = "`{$field}`={$str}";
			}
		}
		if (!$w) {
			$sql = "where {$sql}";
		}
		return $sql;
	}

	public function getId($field = 'order by `id` desc', $str = NULL) {
		$sql = $this -> getSql($field, $str);
		$query = mysql_query("select `id` from `{$this->mysql_prefix}{$this->table}` {$sql}");
		if ($query) {
			$array = mysql_fetch_assoc($query);
			return $array['id'];
		} else {
			return false;
		}
	}

	public function getData($field = NULL, $str = NULL, $fields = '*') {
		if ($field === NULL) {
			return FALSE;
		}
		if (Cnum($field, FALSE)) {
			$sql = "where `id`='{$field}'";
		} else {
			$sql = $this -> getSql($field, $str);
		}
		$query = mysql_query("select {$fields} from `{$this->mysql_prefix}{$this->table}` {$sql}");
		if ($query) {
			$array = mysql_fetch_assoc($query);
			return $array;
		} else {
			return false;
		}
	}

	//pos开始指针位置，rnum读取记录条数
	public function getDatas($pos = 0, $rnum = 10, $field = NULL, $str = NULL, $fields = '*') {
		$limit = " limit {$pos},{$rnum}";
		if ($field) {
			$sql = ' ' . $this -> getSql($field, $str);
		}
		if (!$rnum) {
			$limit = '';
		}
		$query = mysql_query("select {$fields} from `{$this -> mysql_prefix}{$this -> table}`{$sql}{$limit}");
		if ($query) {
			$array = array();
			$index = 0;
			while ($querya = mysql_fetch_assoc($query)) {
				$array[$index] = $querya;
				$index++;
			}
			return $array;
		} else {
			return false;
		}
	}

	public function getColumns($field = NULL, $str = NULL) {
		if (Cstr($field, FALSE, TRUE, 1, 255) && $str == NULL) {
			$sql = " where `Field`='{$field}'";
		} elseif ($field) {
			$sql = ' ' . $this -> getSql($field, $str);
		} else {
			$sql = '';
		}
		$query = mysql_query("show full columns from `{$this -> mysql_prefix}{$this -> table}`{$sql}");
		if ($query) {
			$array = array();
			while ($querya = mysql_fetch_assoc($query)) {
				$array[$querya['Field']] = $querya;
			}
			return $array;
		} else {
			return false;
		}
	}

	public function getCount($field = NULL, $str = NULL) {
		if ($field) {
			$sql = ' ' . $this -> getSql($field, $str);
		}
		$query = mysql_query("select `id` from `{$this->mysql_prefix}{$this->table}`{$sql}");
		return mysql_num_rows($query);
	}

	public function newData(array $array) {
		//获取表字段名
		$array_keys = array_keys($array);
		//print_r($array_keys);
		//获取对应字段的值
		$array_values = array_values($array);
		//print_r($array_values);
		if (array_key_exists('id', $array)) {
			$id = Cnum($array['id']);
		} else {
			$id = 0;
		}
		if ($id) {
			//表信息更新
			$query = "update `{$this->mysql_prefix}{$this->table}` set ";
			for ($i = 0; $i < count($array_keys); $i++) {
				$query .= '`' . $array_keys[$i] . '`=' . mysqlstr($array_values[$i], TRUE, '', TRUE) . ',';
			}
			$query = substr($query, 0, strlen($query) - 1);
			$query .= " where `id`='{$id}'";
		} else {
			//表添加新信息
			$idkey = null;
			$query = "insert into `{$this->mysql_prefix}{$this->table}` (";
			foreach ($array_keys as $key => $value) {
				if ($value == 'id') {
					$idkey = $key;
				} else {
					$query .= "`$value`,";
				}
			}
			$query = substr($query, 0, strlen($query) - 1);
			$query .= ') values (';
			foreach ($array_values as $key => $value) {
				if ($key !== $idkey) {
					$query .= mysqlstr($value, TRUE, '', TRUE) . ',';
				}
			}
			$query = substr($query, 0, strlen($query) - 1);
			$query .= ')';
		}
		//echo $query . '<br>';
		$mysql_r = mysql_query($query);
		return $mysql_r;
	}

	public function delData($field, $str = NULL) {
		if (Cnum($field)) {
			$sql = "where `id`='{$field}'";
		} else {
			$sql = $this -> getSql($field, $str);
		}
		$query = "delete from `{$this->mysql_prefix}{$this->table}` {$sql}";
		$mysql_r = mysql_query($query);
		return $mysql_r;
	}

}
