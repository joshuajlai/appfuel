<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *  
 * @category    Appfuel
 * @package     Util
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Appfuel\StdLib\App;

/**
 * App Manager
 
 */
class Dependency
{
	static protected $depends = array(
		'Appfuel\StdLib\Autoloader\LoaderInterface',
		'Appfuel\StdLib\Autoloader\ClassLoader',
		'Appfuel\StdLib\Filesystem\Manager'
	);

	static public function load($basePath)
	{
		$list = self::$depends;
		foreach ($list as $depend) {
			$path = str_replace('\\', DIRECTORY_SEPARATOR, $depend) . '.php';
			$fullPath = $basePath . DIRECTORY_SEPARATOR . $path;
			if (class_exists($depend) || interface_exists($depend)) {
				continue;
			}

			if (! file_exists($fullPath)) {
				throw new \Exception("Could not find dependency ($fullPath)");
			}

			require $fullPath;
		}
	}
}
