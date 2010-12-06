<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel\StdLib\Config;

use Appfuel\StdLib\Filesystem\AfFile as AfFile;
use Appfuel\StdLib\Ds\AfList\Basic	 as ConfigList;

/**
 *
 * @package 	Appfuel
 */
class Factory
{
	/**
	 * @return	string
	 */
	static public function createAdapter($fileType)
	{
		$class = __NAMESPACE__ . '\\Adapter\\' . ucfirst($fileType);
		
		try {
			return new $class();
		} catch (\Exception $e) {
			throw new Exception("No file adapter exists for $fileType");
		}
	}
	
	/**
	 * @return	File
	 */	
	static public function createFile($filePath)
	{
		return new AfFile($filePath);
	}

	/**
	 * @return	ConfigList
	 */
	static public function createConfigList(array $data = array())
	{
		return new ConfigList($data);
	}
}

