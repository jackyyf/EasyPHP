<?php
/**
 * File: Cache.php
 * Created at: 7:57 PM, 6/22/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

abstract class Cache {

	private static $instance = NULL;

	private static function initCache() {
		$config = Config::getInstance();
		$mode = $config('Cache.Mode', 'Disabled');
		$fileName = Utils::joinPath(ROOT, 'Cache', $mode . '.php');
		if(is_file($fileName) && is_readable($fileName)) {
			require_once $fileName;
			$className = 'Cache' . $mode;
			self::$instance = new $className();
			if(!self::$instance instanceof self) throw new CacheException("$className is not a valid Cache class.");
			return ;
		}
		$mode = 'Disabled';
		require_once Utils::joinPath(ROOT, 'Cache', 'Disabled.php');
		self::$instance = new CacheDisabled();
		if(!self::$instance instanceof self) throw new CoreException('Default Cache class is broken!');
	}

	public static function getInstance() {
		if(self::$instance === NULL) {
			self::initCache();
		}
		return self::$instance;
	}

	public abstract function __construct();

	public abstract function __destruct();

	public abstract function get($key);

	public abstract function inc($key, $val = 1, $ttl = NULL);

	public abstract function set($key, $val = NULL, $ttl = NULL);

	public abstract function has($key);

	public abstract function remove($key);

	public abstract function clear();
}