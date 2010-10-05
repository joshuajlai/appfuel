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
namespace 	Appfuel\Filesystem;

/**
 * Exception
 *
 * @package 	Appfuel
 */
class File
{
	/**
	 * Path
	 * Location of of file on dist
	 * @var string
	 */
	protected $path = NULL;

	/**
	 * Constructor
	 * Assign path as readonly
	 *
	 * @param 	string 	$path
	 * @return	File
	 */
	public function __construct($path)
	{
		$this->setPath = $path;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Exists
	 * Wrapper for PHP file_exists call
	 *
	 * @return 	bool
	 */
	public function exists()
	{
		return file_exists($this->getPath());
	}
}

