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


/**
 * The connector holds master/slave(optional) connection objects and hides
 * the details when you get a master or a slave through getConnection. However,
 * you can manually access each connection.
 */
interface DbConnectorInterface
{
	/**
	 * @return	ConnectionDetailInterface | null when shut down
	 */
	public function getConnection($type = 'write');

	/**	
	 * @return	ConnectionInterface | null after shutdown
	 */
	public function getMaster();

	/**
	 * @return	bool
	 */
	public function isMaster();
	
	/**	
	 * @return	ConnectionInterface | null if does not exist
	 */
	public function getSlave();
	
	/**
	 * @return	bool
	 */
	public function isSlave();

	/**
	 * Shutdown the master or the slave or both with null
	 * @return	null
	 */
	public function shutDown($conn = null);
}
