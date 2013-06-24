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
use EasyPHP\Utils;
use mysqli;

class MySQL implements \EasyPHP\IDatabase {

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
			throw new DatabaseException(Utils::format('Connect Failed! Errno: [[$1]] Errmsg: [[$2]',
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
	}
}
 