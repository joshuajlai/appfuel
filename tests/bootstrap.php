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
use Appfuel\StdLib\App\Dependency			as AppDependency;
use Appfuel\StdLib\Autoloader\ClassLoader   as ClassLoader;
 
/*
 * setup include paths so that we can autoload. This bootstrap script
 * has positional importance and thus should not be moved. The two
 * paths currently assumed are the appfuel and the test dir path. We 
 * also keep the original include path otherwise we need to include 
 * phpunit files
 */
$tDir  = dirname(__FILE__);
$afDir = realpath($tDir . '/../') . '/src';

$pathString = get_include_path() . PATH_SEPARATOR .
			  $afDir . PATH_SEPARATOR . $tDir;	  

set_include_path($pathString);

define ('AF_TEST_PATH', $tDir);

$dependFile = $afDir   . DIRECTORY_SEPARATOR .
			 'Appfuel' . DIRECTORY_SEPARATOR .
			 'StdLib'  . DIRECTORY_SEPARATOR .
			 'App'     . DIRECTORY_SEPARATOR .
			 'Dependency.php';

require_once $dependFile;

AppDependency::load($afDir);

$loader = new \Appfuel\StdLib\Autoloader\ClassLoader();
$loader->register();
