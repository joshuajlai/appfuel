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
	Appfuel\Framework\Db\Connection\ConnectionInterface;

/**
 * The only responsibility of the pool is to hold database connections
 * so they can be retrieved by the db handlers
 */
class Pool
{
	/**
	 * The master connection in a system with replication, also acts
	 * as connection in a system without replication
	 * @var	ConnectionInterface
	 */
	static protected $master = null;
	
	/**
	 * The slave connection in a system with replication, ignored
	 * by systems that don't use replication. Only one slave 
	 * because this was designed to work with HA proxy
	 * @var ConnectionInterface
	 */
	static protected $slave = null;

	/**
	 * @return	ConnectionInterface | null
	 */
	static public function getMaster()
	{
		return self::$master;
	}
}
