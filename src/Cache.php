<?php
/**
 * File: Cache.php
 * Created at: 7:57 PM, 6/22/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

class Cache {

	private static $instance = NULL;

	private function __construct() {
	}

	private static function initCache() {
		$config = Config::getInstance();
		$mode = $config('Cache.Mode', 'Disabled');
		$fileName = Utils::joinPath(ROOT, 'Cache', $mode . '.php');
		if(is_file($fileName) && is_readable($fileName)) {
			require_once $fileName;
			$className = Utils::joinNS(__NAMESPACE__, 'Cache', $mode);
			self::$instance = new $className();
			if(!self::$instance instanceof ICache) throw new CacheException("$className is not a valid Cache class.");
			return ;
		}
		$mode = 'Disabled';
		require_once Utils::joinPath(ROOT, 'Cache', 'Disabled.php');
		self::$instance = new Cache\Disabled();
		if(!self::$instance instanceof ICache) throw new CoreException('Default Cache class is broken!');
	}

	public static function getInstance() {
		if(self::$instance === NULL) {
			self::initCache();
		}
		return self::$instance;
	}

}

interface ICache {
	public function __construct();
	public function __destruct();
	public function get($key);
	public function inc($key, $val = 1, $ttl = NULL);
	public function set($key, $val = NULL, $ttl = NULL);
	public function has($key);
	public function remove($key);
	public function clear();
}