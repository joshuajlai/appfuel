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
use Appfuel\App\Registry,
	Appfuel\App\AppManager,
	Appfuel\Db\Handler\DbInitializer,
	Appfuel\Db\Handler\DbHandler,
	Test\AfTestCase as TestCase;

$base = realpath(dirname(__FILE__) . '/../');
 

$file = "{$base}/lib/Appfuel/App/AppManager.php";

if (! file_exists($file)) {
	throw new \Exception("Could not find app manager file at $file");
}
require_once $file;

$oPath = get_include_path();

$manager = new AppManager($base, "test/config/test.ini");
$manager->initialize();
TestCase::setOriginalIncludePath($oPath);
