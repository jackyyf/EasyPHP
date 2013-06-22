<?php

/**
 * File: Engine.php
 * Created at: 10:04 AM, 5/18/2013
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP; // Use Namespace to avoid name conflict.

\error_reporting(0); // Turn off all error output.

function define($name, $value = NULL) {
	// Define a constant in namespace EasyPHP
	\define('EasyPHP\\' . $name, $value);
}

function defined($name) {
	return \defined('EasyPHP\\' . $name);
}

define('EASYPHP', 1); // We are now in EasyPHP.
define('VERSION', '0.0.1 Alpha');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__)); // Path of engine.php
define('STARTTIME', microtime(true)); // Page start time.

// Load Engine Core.

$engineCore = ROOT . DS . 'Core.php';
if(is_readable($engineCore) && !is_dir($engineCore)) {
	require_once($engineCore);
} else {
	if(!headers_sent()) {
		header('HTTP/1.1 500 Internal Server Error');
		header('X-PHP-Engine: EasyPHP/' . VERSION);
	}
	echo 'Could not load core of Framework.';
	exit(1);
}

// Init engine.

$core = Core::getInstance();