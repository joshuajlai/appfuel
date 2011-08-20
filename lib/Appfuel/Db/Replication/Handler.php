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

use Appfuel\Framework\Exception;

/**
 * The database handler is responsible for handling database requests without
 * knowledge of the database vendor or what adapter is used with that vendor.
 * Its only job is to grab the correct connection, use it to create an 
 * adaoter  and decide which adapter method to use based on the request, 
 * finishing by returning a DbResponse.
 */
class Handler
{
	/**
	 * Master connection used in systems with replication and main
	 * connections in systems without
	 * @var	ConnectionInterface
	 */
	static protected $master = null;

	/**
	 * Slave connection used in systems with replication and ignored
	 * by systems without
	 * @var ConnectionInteface
	 */
	static protected $slave = null;


	public function execute(DbRequestInterface $request)
	{
	
	}

	/**
	 * Return a connection from the pool base on the request type. 
	 * The type is used to determine if the a master or slave is
	 * returned based on this map : 
	 * write - master, read - slave, both - master
	 *
	 * @param	string	$requestType
	 * @return	ConnectionInterface
	 */
	public function getConnection($requestType)
	{

	}

	/**
	 * @return	ConnectionInterface | null when not initialized
	 */
	static public function getMaster()
	{
		return self::$master;
	}

	/**
	 * @param	ConnectionInterface $conn
	 * @return	null
	 */
	static public function setMaster(ConnectionInterface $conn)
	{
		self::$master = $conn;
	}

	/**
	 * @return	ConnectionInterface | null when not initialized
	 */
	static public function getSlave()
	{
		return self::$slave;
	}

	/**
	 * @param	ConnectionInterface $conn
	 * @return	null
	 */
	static public function setSlave(ConnectionInterface $conn)
	{
		self::$slave = $conn;
	}


}
