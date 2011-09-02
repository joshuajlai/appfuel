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
namespace Appfuel\Db\Connection;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface;

/**
 * Holds information neccessary to determine the vendor, vendor's adaptor and
 * connection details.
 */
class ConnectionManager
{
	/**
	 * Name of the default connection group
	 * @var string
	 */
	static protected $defaultName = null;

	/**
	 * List of named connections. Each named group is an isolated list
	 * of connections to a particular database for a particular user. The
	 * reason we hold more than one connection in a group is to account for
	 * replication systems
	 * @var array
	 */
	static protected $conns = array();

	/**
	 * @return	string
	 */
	static public function getDefaultConnectionName()
	{
		return self::$defaultName;
	}

	/**
	 * @param	string	$name	name of the default connection group
	 * @return	null
	 */
	static public function setDefaultConnectionName($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new Exception("Invalid name must be non empty string");
		}

		self::$defaultName = $name;
	}

	static public function isConnectionGroup($name)
	{
		if (empty($name) || !is_string($name) || !isset(self::$conns[$name])) {
			return false;
		}
		
		return true;
	}

	/**
	 * @param	array	$conns
	 * @return	null
	 */ 
	static public function setConnections(array $conns)
	{
		self::$conns = $conns;
	}

	static public function getConnections()
	{
		return self::$conns;
	}
}
