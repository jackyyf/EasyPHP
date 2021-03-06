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

			$SPYC = new \Spyc();
			$this -> content = $SPYC -> loadFile($file);
			if(empty($this -> content)) {
				throw new ConfigException('Config.yml is an illegal yaml file!');
			}
		}
	}

	public function __invoke() {
		/**
		 * if number of argument is 1: it's an alias to get
		 * if number of argument is 2: it's an alias to getDefault
		 * @throw: ConfigException when no argument are provided.
		 */

		$args = func_get_args();
		$len = count($args);
		if($len == 0) {
			throw new ConfigException('Too few arguments.');
		}
		if($len == 1) return $this -> get($args[0]);
		return $this -> getDefault($args[0], $args[1]);
	}

	public function __isset($node) {
		// Following commented code is a safety check for Twig, but it's slow (Very slow).
/*
		try {
			$this -> get($node);
			return true;
		} catch (ConfigException $e) {
			return false;
		}
*/
		// Default is return true at all situations. It's dirty, but fast.
		return true;
	}

	public function __get($node) {
		/**
		 * an alias to get
		 */
		return $this -> get($node);
	}

	public function get($node) {

		/**
		 * function get
		 * @param: string $node
		 * @return: mixed value to the node.
		 * @throw: ConfigException when no such node in config
		 */

		$node = explode('.', (string)$node);
		$current = &$this -> content;
		foreach($node as $token) {
			if(empty($token)) continue;
			if(! array_key_exists($token, $current)) throw new ConfigException('No such node in config.');
			$current = &$current[$token];
		}

		// Generate a copy of target.

		$ret = $current;
		return $ret;
	}

	public function getDefault($node, $default = NULL) {
		/**
		 * function getDefault
		 * @param: string $node
		 * @param: mixed $default default value if not found.
		 * @return: mixed value to the node, or $default if no such node.
		 */

		try {
			return $this -> get($node);
		} catch(ConfigException $e) {
			return $default;
		}
	}

	public static function getInstance() {
		if(self::$instance === NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}