<?php

/**
 * File: MySQL.php
 * Created at: 2:43 AM, 6/22/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

class MySQL {

	private static $instance = NULL;
	private $handle;

	private function __construct() {

	}

	public static function getInstance() {
		if(self::$instance === NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function hasInstance() {
		return self::$instance === NULL;
	}
}