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
namespace Appfuel\Domain\Operation;

use Appfuel\Framework\Exception;

/**
 * Used to manage database interaction throught a uniform interface that 
 * does not care about the specifics of the database
 */
class OpRouteList
{
	/**
	 * Array of operations created during build\deployment. This static raw
	 * data is used to search for the raw operation data by route
	 */
	static protected $raw = array();
	
	/**
	 * Array of already constructed objects which we create when the route
	 * was first found
	 * @var array
	 */
	static protected $objects = array();

	/**
	 * @return	null
	 */
	static public function loadOperationFile()
	{
		self::loadOperations(self::getOperationFileData());
	}

	/**
	 * @param	array
	 * @return	null
	 */
	static public function setOperationalRoutes(array $routes)
	{
		self::$raw = $routes;
	}

	static public function findOperationalRoute 

	/**
	 * Return all the operational routes in their raw form
	 * 
	 * @return	array
	 */
	static public function getOperationalRoutes()
	{
		return self::$raw;
	}

	/**
	 * Clear out raw data and constructed objects
	 *
	 * @return	null
	 */
	static public function clearOperationalRoutes()
	{
		self::$raw	   = array();
		self::$objects = array();
	}
}
