<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @category 	Appfuel
 * @package 	Util
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel\StdLib\Filesystem;

/**
 * File
 * Used to encapsulate php basic file operations arround a single object
 * 
 * @package 	Appfuel
 */
class File extends \SplFileInfo
{
	/**
	 * Exists
	 * Wrapper for PHP file_exists call
	 *
	 * @return 	bool
	 */
	public function exists()
	{
		return $this->isFile();
	}
}

