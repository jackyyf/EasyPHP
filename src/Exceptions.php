<?php

/**
 * EasyPHP Develop Framework.
 * Project Maintained By: JackYYF <root@jackyyf.com>
 * File Created at: 11:17 May 22nd, 2013 GMT
 */

namespace EasyPHP;

use \Exception;

class CoreException extends Exception {
}

class ConfigException extends Exception {
}

class CacheException extends Exception {
}

class DatabaseException extends Exception {

	private $sql;

	public function __construct($message = '', $sql = NULL) {
		$this -> message = $message;
		$this -> sql = $sql;
	}

	public function getSql() {
		return $this -> sql;
	}
}

class TemplateException extends Exception {
}