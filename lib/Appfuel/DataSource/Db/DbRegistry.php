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
namespace Appfuel\DataSource\Db;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\DataStructure\Dictionary,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * Holds two types of data, raw connection parameters, and database connectors.
 * Raw connection parameters are a list of name/value pairs held in an 
 * DictionaryInterface. They are used by DbConnInterface objects that are db 
 * vendor specific objects used to connect to the db server.
 * DbConnectorInterface objects are db vendor agnostic objects that encapulate
 * DbConnInterfaces for master/slave replication systems or single db servers.
 * There are two separate interfaces for getting connectors and parameters, 
 * because they share the same key
 */
class DbRegistry
{
	/**
	 * List of DictionaryInterfaces identified by a label
	 * @var array
	 */
	static protected $params = array();

	/**
	 * List of DbConnectorInterfaces 
	 * @var array
	 */
	static protected $connectors = array();

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isConnectionParams($key)
	{
		if (is_string($key) || isset(self::$params[$key])) {
			return true;
		}

		return false;
	}

	/**
	 * @return	array
	 */
	static public function getAllConnectionParams()
	{
		return self::$params;
	}

	/**
	 * @param	string	$key
	 * @return	DictionaryInterface | false 
	 */
	static public function getConnectionParams($key)
	{
		if (! self::isConnectionParams($key)) {
			return false;
		}

		return self::$params[$key];
	}

	/**
	 * @param	string	$key
	 * @param	mixed	array | DictionaryInterface	 $params
	 * @return	null
	 */
	static public function addConnectionParams($key, $params)
	{
		if (! is_string($key)) {
			$err = 'connection parameter key must be a string';
			throw new InvalidArgumentException($err);
		}

        if (is_array($params)) {
            $params = new Dictionary($params);
        }
        else if (! ($params instanceof DictionaryInterface)) {
            $err  = 'db connection parameters must be either an array ';
            $err .= 'or an object that implements Appfuel\DataStructure';
            $err .= '\DictionaryInterface';
            throw new InvalidArgumentException($err);
        }

		self::$params[$key] = $params;
	}

	/**
	 * @param	array	$list	list of named database connection params
	 * @return	null
	 */
	static public function loadConnectionParams(array $list)
	{
		if ($list === array_values($list)) {
			$err  = 'list of connection parameters must be an associative ';
			$err .= 'of named parameters';
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $key => $params) {
			self::addConnectionParams($key, $params);
		}
	}

	/**
	 * @param	array	$list	list of named database connection params
	 * @return	null
	 */
	static public function setConnectionParams(array $list)
	{
		self::clearConnectionParams();
		self::loadConnectionParams($list);
	}

	/**
	 * @return	null
	 */
	static public function clearConnectionParams()
	{
		self::$params = array();
	}
}
