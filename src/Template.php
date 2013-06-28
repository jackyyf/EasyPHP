<?php
/**
 * File: Template.php
 * Created at: 11:11 AM, 6/28/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

class Template {

	/**
	 * @var ITemplate
	 */

	private static $instance = NULL;

	private static function initTemplate() {
		$config = Config::getInstance();
		$engine = $config('Template.Engine');
		$fileName = Utils::joinPath(ROOT, 'Template', $engine . '.php');
		if(is_file($fileName) && is_readable($fileName)) {
			require_once $fileName;
			$className = Utils::joinNS(__NAMESPACE__, 'Template', $engine);
			self::$instance = new $className();
			if(!self::$instance instanceof ITemplate) {
				self::$instance = NULL;
				throw new TemplateException("$className is not a valid Template class.");
			}
			return ;
		}
		throw new TemplateException('Non-valid Template engine!');
	}

	public static function getInstance() {
		if (self::$instance === NULL) self::initTemplate();
		return self::$instance;
	}
}

interface ITemplate {

}