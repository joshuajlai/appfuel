<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
 
$tDir = realpath(dirname(__FILE__) . '/../');
$file = $tDir	  . DIRECTORY_SEPARATOR . 
		'include' . DIRECTORY_SEPARATOR .
		'appfuel.php';

if (! file_exists($file)) {
	throw new \Exception("Could not locate appfuel dependency script ($file)");
}
require_once $file;

$configFile = 'test'   . DIRECTORY_SEPARATOR . 
			  'config' . DIRECTORY_SEPARATOR .
			  'test.ini';
 
$initializer = new \Appfuel\Framework\App\Initializer($basePath);
$initializer->initialize($configFile);
