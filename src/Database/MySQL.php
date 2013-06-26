<?php
/**
 * File: MySQL.php
 * Created at: 3:31 AM, 6/25/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP\Database;

use EasyPHP\Config;
use EasyPHP\DatabaseException;
use EasyPHP\IDatabase;
use EasyPHP\Utils;
use mysqli;

class MySQL implements IDatabase {

	private $handle;
	private $count = 0;

	public function __construct() {
		$config = Config::getInstance();
		$host = $config('Database.MySQL.Host');
		$user = $config('Database.MySQL.User');
		$pass = $config('Database.MySQL.Pass');
		$database = $config('Database.MySQL.Database');
		$charset = $config('Database.MySQL.Charset', 'UTF8');
		$this -> handle = new mysqli($host, $user, $pass, $database);
		if($this -> handle -> connect_errno) {
			throw new DatabaseException(Utils::format('Connect Failed! Errno: [[$1]] Errmsg: [[$2]]',
				$this -> handle -> connect_errno, $this -> handle -> connect_error));
		}
		$this -> handle -> set_charset($charset);
		if($this -> handle -> errno) {
			throw new DatabaseException('Illegal charset: ' . $charset);
		}
	}

	public function __destruct() {
		if ($this -> handle) $this -> handle -> close();
	}

	public function queryCount() {
		return $this -> count;
	}

	public function injectionCheck($query) {
		//@TODO: Add Injection Check.
	}

	public function query($query) {
		++ $this -> count;
		$result = $this -> handle -> query($query);
		if($result === false) {
			throw new DatabaseException(Utils::format('Failed while execuing query. Errno: [[$1]] Errmsg: [[$2]]',
				$this -> handle -> errno, $this -> handle -> error), $query);
		}
		if($result === true) {
			return true;
		}
		$ret = array();
		while(($nextRow = $result -> fetch_array()) !== NULL) {
			$ret[] = $nextRow;
		}
		return $ret;
	}

	public function safeQuery($query) {
		$this -> injectionCheck($query);
		return $this -> query($query);
	}

	public function escape($data) {
		return $this -> handle -> real_escape_string($data);
	}

	public function insertRow($tableName, $data) {
		// Leave $data unescaped!
		if(! is_array($data))
			throw new DatabaseException('$data should be an array!');
		$this -> lockTable($tableName, true);
		$colName = array(); $value = array();
		foreach($data as $col => $val) {
			$colName[] = "`$col`";
			if(is_int($val) || is_float($val)) {
				$value[] = strval($val);
			} else {
				$value[] = '"' . $this -> escape($val) . '"';
			}
		}
		$colName = implode(',', $colName); $value = implode(',', $value);
		$query = "INSERT INTO `$tableName` ($colName) VALUES ($value)";
		$res = &$this -> query($query);
		$this -> unlockTable();
		return $res;
	}

	public function getRows($tableName, $cols = array('*'), $condition = '1', $count = 1, $start = 0) {
		if(! is_array($cols))
			throw new DatabaseException('$cols should be an array!');
		$this -> lockTable($tableName);
		foreach($cols as $cKey => $cValue) {
			$cols[$cKey] = "`$cValue`";
		}
		$cols = implode(',', $cols);
		$query = "SELECT $cols FROM $tableName WHERE $condition";
		$count = intval($count);
		if($count > 0) {
			$lim = "$count";
			if($start !== NULL) {
				$start = intval($start);
				$lim = "$start,$lim";
			}
			$query .= " LIMIT $lim";
		}
		$res = &$this -> query($query);
		$this -> unlockTable();
		return $res;
	}

	public function deleteRows($tableName, $condition, $count = 1) {
		$this -> lockTable($tableName, true);
		$query = "DELETE FROM $tableName WHERE $condition";
		$count = intval($count);
		if($count > 0) {
			$query .= " LIMIT $count";
		}
		$res = &$this -> query($query);
		$this -> unlockTable();
		return $res;
	}

	public function updateRows($tableName, $data, $condition, $count = 1) {
		if(! is_array($data))
			throw new DatabaseException('$data should be an array!');
		if(empty($data))
			throw new DatabaseException('$data can not be an empty array!');
		$this -> lockTable($tableName, true);
		$query = "UPDATE $tableName SET ";
		$tokens = array();
		foreach($data as $key => $value) {
			$key = "`$key`";
			if(is_int($value) || is_float($value)) {
				$value = strval($value);
			} else {
				$value = '"' . $this -> escape($value) . '"';
			}
			$tokens[] = "$key=$value";
		}
		$query .= implode(',', $tokens);
		$query .= " WHERE $condition";
		$count = intval($count);
		if($count > 0) {
			$query .= " LIMIT $count";
		}

		$res = &$this -> query($query);
		$this -> unlockTable();
		return $res;
	}

	public function lockTable($tableName, $exclusive = false) {
		if(!is_string($tableName)) {
			if(is_array($tableName)) $tableName = implode(',', $tableName);
			else throw new DatabaseException('$tableName must be a string or array!');
		}
		$mode = $exclusive ? 'WRITE' : 'READ';
		$query = "LOCK TABLES $tableName $mode";
		return $this -> query($query);
	}

	public function unlockTable($tableName = NULL) {
		// MySQL ignores $tableName
		return $this -> query('UNLOCK TABLES');
	}
}
 