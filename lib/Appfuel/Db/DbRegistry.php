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
namespace Appfuel\Db;

use RunTimeException,
	InvalidArgumentException;

/**
 * Decouples the kernal settings from the kernal
 */
class DbRegistry
{
	/**
	 * The connector key that identifies the database connector used 
	 * by the application when no connector is specified
	 * @var string
	 */ 
	static protected $defaultConnectorKey = null;

	/**
	 * Holds a list of domain key to domain class mappings
	 * @var array
	 */
	static protected $connectors = array();

	/**
	 * @return	string
	 */
	static public function getDefaultConnectorKey()
	{
		return self::$defaultConnectorKey;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$key
	 * @return	null
	 */
	static public function setDefaultConnectorKey($key)
	{
		if (empty($key) || ! is_string($key)) {
			$err = 'Default connector key must be a non empty string';
			throw new InvalidArgumentException($key);
		}

		self::$defaultConnectorKey = $key;
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isConnector($key)
	{
		if (empty($key) || 
			! is_string($key) || 
			! isset(self::$connectors[$key]) ||
			! (self::$connectors[$key] instanceof DbConnectionInterface)) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string	$key 
	 * @return	DbConnectionInterface | null when it does not exist
	 */
	static public function getConnector($key = null)
	{
		if (null === $key) {
			$key = self::getDefaultConnectorKey();
			if (null === $key) {
				$err  = 'failed to get connector: default connector key has ';
				$err .= 'not been set';
				throw new RunTimeException($err);
			}
		}

		if (! self::isConnector($key)) {
			return null;
		}

		return self::$connectors[$key];
	}

	/**
	 * @param	string	$key
	 * @param	DbConnectorInterface $connector
	 * @return	null
	 */
	static public function addConnector($key, DbConnectorInterface $connector)
	{
		if (empty($key) || ! is_string($key)) {
			$err = 'failed to add connector: key must be a non empty string';
			throw new InvalidArgumentException($key);
		}
		self::$connectors[$key] = $connector;
	}
}
