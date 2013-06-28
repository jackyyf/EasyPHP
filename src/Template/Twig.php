<?php
/**
 * File: Twig.php
 * Created at: 2:10 PM, 6/28/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP\Template;

use EasyPHP\Cache;
use EasyPHP\Config;
use EasyPHP\ITemplate;
use EasyPHP\TemplateException;
use EasyPHP\Utils;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Twig implements ITemplate {

	private $cache;
	private $base;
	private $vars;
	private $functions;

	public function __construct() {
		$config = Config::getInstance();
		$this -> vars = array(
			'config' => $config,
			'cache' => Cache::getInstance(),
		);
		$this -> functions = array(
			'config' => $config,
			'cache' => Cache::getInstance(),
		);
		$this -> base = $config('Template.Base');
		$this -> cache = $config('Template.Cache', false);
		require_once Utils::joinPath(ROOT, 'Template', 'Twig', 'Autoloader.php');
		Twig_Autoloader::register();
	}

	public function __destruct() {}

	public function setVar($key, $value) {
		$this -> vars[$key] = $value;
	}

	public function addFunction($name, $callable) {
		if (!is_callable($callable))
			throw new TemplateException('$callable is not callable!');
		$this -> functions[$name] = $callable;
	}

	public function &render($template, $option = array()) {
		$option['cache'] = &$this -> cache;
		$loader = new Twig_Loader_Filesystem($this -> base);
		$twig = new Twig_Environment($loader, $option);
		foreach($this -> functions as $name => $callable) {
			$twig -> addFunction($name, $callable);
		}
		$template = $twig -> loadTemplate($template);
		return $template -> render($this -> vars);
	}

}