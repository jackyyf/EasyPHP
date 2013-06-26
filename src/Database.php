<?php

/**
 * File: Database.php
 * Created at: 2:43 AM, 6/22/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

class Database {

	/**
	 * @var IDatabase
	 */

	private static $instance;

	private function __construct() {
	}

	private static function initDB() {
		$config = Config::getInstance();
		$engine = $config('Database.Engine');
		$fileName = Utils::joinPath(ROOT, 'Database', $engine . '.php');
		if(is_file($fileName) && is_readable($fileName)) {
			require_once $fileName;
			$className = Utils::joinNS(__NAMESPACE__, 'Database', $engine);
			self::$instance = new $className();
			if(!self::$instance instanceof IDatabase) throw new DatabaseException("$className is not a valid Database class.");
			return ;
		}
		throw new DatabaseException('Non-valid Database engine!');
	}

	public static function getInstance() {
		if(self::$instance === NULL) {
			self::initDB();
		}
		return self::$instance;
	}

	public static function hasInstance() {
		return self::$instance === NULL;
	}
}

interface IDatabase {
	public function __construct();
	public function __destruct();
	public function query($query); // Return array(row1, row2, row3), each row contains key=>value
	public function injectionCheck($query); // Check SQL Injection.
	public function safeQuery($query); // With SQL Injection Check.
	public function queryCount();
	public function getRows($tableName, $cols = array('*'), $condition = '1', $count = 1, $start = 0);
	public function insertRow($tableName, $data); // Insert one row, $data is an array and key => value.
	public function deleteRows($tableName, $condition, $count = 1); // For safety, $condition has no default value.
	public function updateRows($tableName, $data, $condition, $count = 1); // $data is just like in insertRow
	public function escape($data);
	public function lockTable($tableName, $exclusive = false);
	public function unlockTable($tableName);
}