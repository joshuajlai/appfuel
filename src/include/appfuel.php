<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2011 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */

/* we kmow we are in the bin directory and one level up is the base path */
$basePath = realpath(dirname(__FILE__) . '/..');
if (! defined('AF_BASE_PATH')) {
	define('AF_BASE_PATH', $basePath); 
}

$dir  = $basePath . DIRECTORY_SEPARATOR . 'lib';

$file = $dir      . DIRECTORY_SEPARATOR . 
		'Appfuel' . DIRECTORY_SEPARATOR .
		'Dependency.php';

if (! file_exists($file)) {
	throw new \Exception("Could not locate Dependency file at ($file)");
}
require_once $file;

$depend = new \Appfuel\Dependency($dir);
$depend->load();

unset($dir);
unset($file);
unset($depend);
