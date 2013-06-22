<?php

/**
 * File: Core.php
 * Created at: 10:30 AM, 5/18/2013
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

if (!defined('_EASYPHP')) {
	if(!headers_sent()) {
		header('HTTP/1.1 404 Not Found');
	}
	die();
}

class Core {
	private static $instance = NULL;
	private $compress;
	private $compressionEnabled = false;


	public static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function _checkCompress() {

		/**
		 * Check for compression support from both client and server side.
		 * @return string: represent the support compression encoding
		 * @return boolean: false, if no support compression found.
		 */

		$supportedCompression = array(
			'gzip' => true,
			'x-gzip' => true,
			'deflate' => true,
		);

		if (headers_sent()) return false; // Can't set accept-encoding header.
		if (! function_exists('gzencode')) return false; // Server doesn't support compression.
		$encoding = explode(',', $_SERVER['HTTP_ACCEPT_ENCODING']);
		foreach($encoding as $encode) {
			$encode = strtolower($encode);
			if (array_key_exists($encode, $supportedCompression)) return $encode; // Support compression found.
		}
		return false; // Client does not support compression.
	}

	private function __construct() {
		// Register autoLoader
		spl_autoload_register(array($this, 'autoLoader'));
	}

	public function getScriptTime() {
		/**
		 * Get running time of current request.
		 * @return double: Running time of current request in second.
		 */
		return microtime(true) - STARTTIME;
	}

	public function getScriptMemory() {
		return memory_get_peak_usage();
	}

	public function getSQLQueries() {
		if(MySQL::hasInstance()) return MySQL::getInstance() -> queryCount(); // Yes, we have connected to MySQL server!
		return 0; // No MySQL connection are established, so return 0.
	}

	public function getSQLConnection() {
		return MySQL::getInstance();
	}

	public function autoLoader($className) {
		$prefix = 'EasyPHP\\'; // Namespace Prefix
		if (strncmp($className, $prefix, strlen($prefix)) == 0) {
			$realName = substr($className, 8);
			$fileName = ROOT . $realName . '.php';
			if(file_exists($fileName)) {
				require_once $fileName;
			} else if(substr($realName, -9) == 'Exception') { // Exceptions are defined in Exceptions.php
				require_once ROOT . 'Exceptions.php';
			}
		}
	}
}