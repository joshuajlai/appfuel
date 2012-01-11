<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\App;

use InvalidArgumentException;


/**
 */
class ResourceManager
{
	/**
	 * Name of the directory used to hold html resources
	 * @var string
	 */
	static protected $resourceDir = 'ui';

	/**
	 * @var	array
	 */
	static protected $siteUrls = array();

	/**
	 * @return	string
	 */
	static public function getResourceDir()
	{
		return self::$resourceDir;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$name
	 * @return	null
	 */
	static public function setResourceDir($name)
	{
		if (! is_string($name) || ! ($name = trim($name))) {
			$err = 'resource dir name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		self::$resourceDir = $name;
	}

	
}
