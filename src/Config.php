<?php
/**
 * File: Config.php
 * Created at: 2:47 AM, 6/22/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

class Config {

	private static $instance = NULL;
	private $content;

	private function __construct() {
		// Load from Config.yml

		// Do we have Config.yml?

		$file = Utils::joinPath(ROOT, 'Config.yml');
		if(!file_exists($file)) throw new ConfigException('Config.yml not found!');
		if(!(is_readable($file) && is_file($file))) throw new ConfigException('Config.yml is not readable!');

		// Do we have php_yaml extension?

		if(function_exists('yaml_parse_file')) {
			// Yes, so use it.
			$this -> content = @yaml_parse_file($file);
			if(empty($this -> content)) {
				throw new ConfigException('Config.yml is an illegal yaml file!');
			}
		} else {
			// Too bad :(, use Spyc instead.
			$spyc = Utils::joinPath(ROOT, 'Yaml', 'Spyc.min.php');
			if(!file_exists($spyc)) throw new CoreException('Spyc not found.');
			if(!(is_readable($spyc) && is_file($spyc))) throw new CoreException('Unable to load Spyc.');
			require_once $spyc;
		}
	}

	public static function getInstance() {
		if(self::$instance === NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}