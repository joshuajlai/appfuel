<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
use Appfuel\App\AppHandler;

$base = realpath(dirname(__FILE__) . '/../');
$file = "{$base}/lib/Appfuel/App/AppHandler.php";
if (! file_exists($file)) {    
	throw new LogicException("Could not find app runner at -($file)");
}
require $file;

$handler = new AppHandler($base);
$handler->loadConfigFile('app/config/config.php', 'test')   
		->initializeFramework();

unset($file);
unset($base);
