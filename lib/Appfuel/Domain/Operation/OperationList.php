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
class OperationList
{
	/**
	 * Relative location of the php file holding the operations
	 * @var string
	 */
	static protected $filePath = '/codegen/operations.php';
	
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
	 * @return	string
	 */
	static public function getFilePath()
	{
		return AF_BASE_PATH . self::$filePath;
	}

	/**
	 * Determine that the file exists at self::getFilePath, require the file
	 * and ensure the php variable $opList is in scope. 
	 *
	 * @throws	Appfuel\Framework\Exception
	 * @return	false | array
	 */
	static public function getOperationFileData()
	{
		$err  = "getOperationFileData failed:";
		$path = self::getFilePath();
		if (! file_exists($path)) {
			$err .= " Could not find generated operations file at $path";
			throw new Exception($err);
		}
		
		require_once $path;

		if (! isset($opList) || ! is_array($opList)) {
			$err .= ' Generated operation list is missing $opList or $opList';
			$err .= ' is not an array';
			throw new Exception($err);
		}

		return $opList;
	}

	/**
	 * @return	null
	 */
	static public function loadOperationFile()
	{
		self::loadOperations(self::getOperationFileData());
	}

	/**
	 * Load the raw data after validating that the route string is a non
	 * empty string and points to a non empty array of data
	 *
	 * @param	array	$raw	operations for the application
	 * @return	null
	 */
	static public function loadOperations(array $operations)
	{
		self::clearOperations();
		$err = 'LoadData failed:';
		foreach ($operations as $route => $data) {
			if (empty($route) || ! is_string($rotue)) {
				throw new Exception("$err route must be a non empty string");
			}
			
			if (empty($data) || ! is_array($data)) {
				throw new Exception("$err operation data must be an array");
			}
		}

		self::$raw = $operations;
	}

	/**
	 * Clear out raw data and constructed objects
	 *
	 * @return	null
	 */
	static public function clearOperations()
	{
		self::$raw	   = array();
		self::$objects = array();
	}
}
