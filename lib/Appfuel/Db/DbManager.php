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

use Appfuel\Framework\Exception,
	Appfuel\Framework\Registry,
	Appfuel\Db\Connection\Connector,
	Appfuel\Db\Connection\ConnectionDetail,
	Appfuel\Framework\Db\Connection\ConnectorInterface;

/**
 * Handles the raw database information to create connectors, dbusers, schemas
 * etc ...
 */
class DbManager
{
	/**
	 * Name of the default connector which will be loaded into the connection
	 * pool
	 * @var string
	 */
	static protected $defaultConnector = null;

	/**
	 * Raw data structure which holds all the information about the databases,
	 * servers and anythingelse the framework can interface with.
	 * @var array
	 */
	static protected $raw = array();

	/**
	 * List of named pools of master/slave or just master connections
	 * @var	array
	 */
	static protected $connectors = array();

	/**
	 * @param	array	$conns
	 * @return	null
	 */ 
	static public function setRawData(array $data)
	{
		/* list of required sections that need to be available */
		$sections = array(
			'servers', 
			'databases', 
			'privilege-groups', 
			'users',
			'connectors'
		);
		foreach ($sections as $section) {
			if (! isset($data[$section]) || ! is_array($data[$section])) {
				throw new Exception("invalid section or format for $section");
			}
		}
		self::$raw = $data;
	}

	/**
	 * @return	array
	 */
	static public function getRawData()
	{
		return self::$raw;
	}

	/**
	 * @param	string	$key	used to identify the connector
	 * @return	false | when not found
	 */
	static public function getConnector($key = null)
	{
		if (null === $key) {
			$key = self::getDefaultConnectorKey();
		}

		if (! self::isConnector($key)) {
			return false;
		}

		return self::$connectors[$key];
	}

	/**
	 * Allows the intialization of more than one connector based on 
	 * connector keys found in the Registry. Also sets the default
	 * connector, which the orm system will use to access the database
	 *
	 * @return null
	 */
	static public function initialize()
	{
		$err = 'Database initialization error: ';
		if (! defined('AF_ENV')) {
			throw new Exception("$err AF_ENV constant not defined");
		}

		$keys = Registry::get('db-connectors');
		if (empty($keys)) {
			$err .= 'database connectors were not found in the Registry ';
			$err .= 'with key -(db-connectors)';
			throw new Exception($key);
		}
	
		if (is_string($keys)) {
			$keys = array($keys);
		}
		
		foreach ($keys as $key) {
			$connector = self::buildConnector($key);
			self::$connectors[$key] = $connector;
		}

		$default = Registry::get('db-default-connector');
		if (empty($default) || ! is_string($default)) {
			$err .= 'default datatbase connector -(db-default-connector) ';
			$err .= 'was not found or was not a string';
			throw new Exception($err);
		}

		/*
		 * A valid default connector must have already been loaded before
		 * we set it
		 */
		if (! self::isConnector($default)) {
			throw new Exception("$err default connector not initialized");
		}
		self::setDefaultConnectorKey($default);
		
	}

	/**
	 * Flag used to determine if a valid connector is available for the 
	 * given key
	 *
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isConnector($key)
	{
		if (empty($key)			|| 
			! is_string($key)	|| 
			! isset(self::$connectors[$key]) ||
			! self::$connectors[$key] instanceof ConnectorInterface) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Build a connector object from data collected in the db config for
	 * the connector found with $key. A connector needs a master connection
	 * and or an optional slave connection. 
	 *
	 * @param	string	$key
	 * @return	Connector
	 */
	static public function buildConnector($key)
	{
		if (empty($key) || ! is_string($key)) {
			throw new Exception("Connector key must be a non empty string");
		}

		/*
		 * collect details of the connector pointed to by $key
		 */
		$conns = self::$raw['connectors'];
		if (! isset($conns[$key]) || ! is_array($conns[$key])) {
			throw new Exception("Connector not found with key -($key)");
		}
		$conn = $conns[$key];
		
		/* 
		 * this is the php class used as the adapter specific connection 
		 * an example would be Appfuel\Db\Mysql\AfMysqli\Connection
		 */
		if (! isset($conn['php-conn-class'])) {
			throw new Exception("php-conn-class must be set");
		}
		$connClass = $conn['php-conn-class'];

		/*
		 * collect the user connection info if like the username and password
		 */
		if (! isset($conn['user-key']) || ! is_string($conn['user-key'])) {
			throw new Exception("user-key must be set and a string");
		}
		$userData = self::getUserConnectionData($conn['user-key']);

		/*
		 * collect the database information the database name and servers for
		 * this connector. A connector can have a master and slave.
		 */
		if (!isset($conn['db-key']) || ! is_string($conn['db-key'])) {
			throw new Exception("db-key must be set and a string");
		}
		$dbData = self::getDatabaseData($conn['db-key']);
	
		$server = self::getServerData($dbData['master']);
		$master = array(
			'type'	   => 'master',
			'dbname'   => $dbData['dbname'],
			'username' => $userData['username'],
			'password' => $userData['password'],
			'hostname' => $server['hostname'],
			'port'     => $server['port'],
			'socket'   => $server['socket']
		);
		$masterConn = new $connClass(new ConnectionDetail($master));
		$slaveConn  = null;
		if (isset($dbData['slave']) && is_string($dbData['slave'])) {
			$server = self::getServerData($dbData['slave']);
			$slave = array(
				'type'	   => 'slave',
				'dbname'   => $dbData['dbname'],
				'username' => $userData['username'],
				'password' => $userData['password'],
				'hostname' => $server['hostname'],
				'port'     => $server['port'],
				'socket'   => $server['socket']
			);
			$slaveConn = new $connClass(new ConnectionDetail($slave));
		}

		return new Connector($masterConn, $slaveConn);
	}
	
	/**
	 * Parse the raw configuration data for the server data found by the
	 * given server key
	 *
	 * @param	string	$key
	 * @return	array
	 */
	static public function getServerData($key)
	{
		$err  = 'failed to get server data from db config: ';
		$env = AF_ENV;
		$servers = self::$raw['servers'];
		if (! isset($servers[$key])) {
			throw new Exception("server ($key) was not found in config");
		}
		$server = $servers[$key];
		if (! isset($server[$env]) || ! is_array($server[$env])) {
			$err .= "environment key -($env) not found in server section ";
			throw new Exception($err);
		}
		$senv = $server[$env];
	
		if (! isset($senv['hostname']) || ! is_string($senv['hostname'])) {
			$err .= "hostname not found with key -($key) in environment ";
			$err .= "-($env) in server section";
			throw new Exception($err);
		}
		$hostname = $senv['hostname'];

		/*
		 * optional sections
		 */
		$port	  = null;
		$socket   = null;
		if (isset($senv['port']) && is_string($senv['port'])) {
			$port = $senv['port'];
		}
		if (isset($senv['socket']) && is_string($senv['socket'])) {
			$socket = $senv['socket'];
		}

		return array(
			'hostname' => $hostname,
			'port'	   => $port,
			'socket'   => $socket
		);
	}
	
	/**
	 * Parse the database information from the database for a given database
	 * identified by key
	 *
	 * @param	string	$key
	 * @return	array
	 */
	static public function getDatabaseData($key)
	{
		$err  = 'failed to get database data from db config: ';
		$dbs = self::$raw['databases'];
		if (! isset($dbs[$key])) {
			throw new Exception("$err key not found -($key)");
		}
				
		$db = $dbs[$key];
		if (! isset($db['dbname']) || ! is_string($db['dbname'])) {
			throw new Exception("$err (dbname) not found for key -($dbkey)");
		}
		$dbname = $db['dbname'];			
				
		if (! isset($db['master']) || ! is_string($db['master'])) {
			throw new Exception("$err master was not found for key -($key)");
		}
		$master = $db['master'];
		
		$slave = null;
		if (isset($db['slave']) && is_string($db['slave'])) {
			$slave = $db['slave'];
		}

		return array(
			'dbname' => $dbname,
			'master' => $master,
			'slave'  => $slave
		);
	}

	/**
	 * Parse the User information from the database for a given user
	 * identified by key
	 *
	 * @param	string	$key
	 * @return	array
	 */
	static public function getUserConnectionData($key)
	{
		$err  = 'failed to get user data from db config: ';
		$env = AF_ENV;
		$users = self::$raw['users'];
		if (! isset($users[$key]) || ! is_array($users[$key])) {
			throw new Exception("$err user key was not found -($key)");
		}

		$user = $users[$key];
		if (! isset($user[$env]) || ! is_array($user[$env])) {
			throw new Exception("$err could not find env -($env) users");
		}
		
		/* cache the user environment so the we are not accessing arrays */
		$uenv = $user[$env];
		if (! isset($uenv['username']) || ! is_string($uenv['username'])) {
			$err .= "username was not found or is not a string for key ";
			$err .= "-($key) with env -($env)";
			throw new Exception($err);
		}
		$username = $uenv['username'];
		
		if (! isset($uenv['password']) || ! is_string($uenv['password'])) {
			$err .= "password was not found or is not a string for key ";
			$err .= "-($key) with env -($env)";
			throw new Exception($err);
		}
		$password = $uenv['password'];
			
		return array(
			'username' => $username,
			'password' => $password,
		);
	}

	/**
	 * @return	string
	 */
	static public function getDefaultConnectorKey()
	{
		return self::$defaultConnector;
	}

	/**
	 * @param	string	$name	name of the default connection group
	 * @return	null
	 */
	static public function setDefaultConnectorKey($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new Exception("Invalid name must be non empty string");
		}

		self::$defaultConnector = $name;
	}

}
