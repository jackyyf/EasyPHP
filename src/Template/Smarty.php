<?php
use EasyPHP\Config;
use EasyPHP\ITemplate;
use EasyPHP\TemplateException;
use EasyPHP\Utils;

/**
 * File: Smarty.class.php
 * Created at: 10:51 PM, 7/26/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

class Smarty implements ITemplate {

	private $smarty;

	public function __construct() {
		$path = Utils::joinPath(ROOT, 'Template', 'Smarty', 'Smarty.class.php');
		if(! (is_readable($path) && is_file($path))) {
			throw new TemplateException('Could not load Smarty!');
		}
		require_once $path;
		$this -> smarty = new Smarty();
		$config = Config::getInstance();
		$cache = $config('Template.Cache');
		if($cache) {
			$cache = strtolower($cache);
			switch($cache) {
				case 'file':
					$cacheDir = $config('Template.CacheDir');
					$this -> smarty -> setCacheDir($cacheDir);
			}
		}
	}

	public function __destruct() {
		// TODO: Implement __destruct() method.
	}

	public function setVar($key, $value) {
		// TODO: Implement setVar() method.
	}

	public function addFunction($name, $callable) {
		// TODO: Implement addFunction() method.
	}

	public function render($template, $option = array()) {
		// TODO: Implement render() method.
	}
}