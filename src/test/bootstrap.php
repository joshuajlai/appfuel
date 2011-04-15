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
use Appfuel\App\Manager,
	Test\AfTestCase as TestCase;

$basePath = realpath(dirname(__FILE__) . '/../');
 
$file = $basePath . DIRECTORY_SEPARATOR . 
		'lib'     . DIRECTORY_SEPARATOR .
		'Appfuel' . DIRECTORY_SEPARATOR .
		'App'	  . DIRECTORY_SEPARATOR .
		'Manager.php';

if (! file_exists($file)) {
	throw new \Exception("Could not find app manager file at $file");
}
require_once $file;

$configFile = $basePath . DIRECTORY_SEPARATOR .
			  'test'    . DIRECTORY_SEPARATOR . 
			  'config'  . DIRECTORY_SEPARATOR .
			  'test.ini';

$oPath = get_include_path();
Manager::Initialize($basePath, $configFile);
TestCase::setOriginalIncludePath($oPath);

