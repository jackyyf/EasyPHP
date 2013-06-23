<?php
/**
 * File: Disabled.php
 * Created at: 3:21 PM, 6/23/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

class CacheDisabled extends Cache {

	/**
	 * This is the default cache (no cache, just like a tmp saving area, destroyed when script exit.)
	 */

	private $tempData;

	public function __construct() { $this -> tempData = array(); }

	public function __destruct() { unset($this -> tempData); }

	public function get($key) { return $this -> tempData[$key]; }

	public function set($key, $val = NULL, $ttl = NULL) {
		$this -> tempData[$key] = $val;
	}

	public function inc($key, $val = 1, $ttl = NULL) {
		if(is_int($this -> tempData[$key])) return ++ $this -> tempData[$key];
		throw new CacheException('Try to increase on non-integer value.');
	}

	public function has($key) {
		return array_key_exists($key, $this -> tempData);
	}

	public function remove($key) {
		unset($this -> tempData[$key]);
	}

	public function clear() {
		$this -> tempData = array();
	}
}