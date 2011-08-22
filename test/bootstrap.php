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
use TestFuel\TestCase\BaseTestCase;

$base = realpath(dirname(__FILE__) . '/../');
$file = "{$base}/lib/TestFuel/TestCase/BaseTestCase.php";
if (! file_exists($file)) {
	throw new \Exception("Could not find base test case file at $file");
}
require_once $file;
BaseTestCase::initialize($base, "config/test.ini");
unset($file);
unset($base);
