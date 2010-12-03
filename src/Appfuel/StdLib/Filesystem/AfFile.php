<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @category 	Appfuel
 * @package 	StdLib
 * @subpackage	Filesystem
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel\StdLib\Filesystem;

/**
 * @category 	Appfuel
 * @package		StdLib
 * @subpackage	Filesystem
 */
class AfFile extends File
{
	
	/**
	 * @return	NULL
	 */
	public function	__construct($filePath)
	{
		$path = $this->getBasePath() . DIRECTORY_SEPARATOR . $filePath;
		parent::__construct($path);
	}

	/**
	 * @return	string
	 */	
	public function getBasePath()
	{
		return Manager::classNameToDir(get_class($this));
	}
}

