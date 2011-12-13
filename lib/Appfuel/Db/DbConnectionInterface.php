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
 * Connection interface describes the functionality of appfuel connection 
 * class. These classes are vendor specific usually wrapping low level 
 * libraries like myqli. Because they are used by the appfuel db handler which 
 * has no idea about the details of db vendors or their adapters the connection
 * classes must adhere to this interface
 */
interface DbConnectionInterface
{
	/**
	 * Value object used to connected to the database server. I recommend 
	 * passing this through the constructor, but this not enforced. 
	 *
	 * @return	ConnectionDetailInterface
	 */
	public function getConnectionDetail();
	
	/**
	 * Vendor specific database driver. This is initialized and set with
	 * ConnectionInterface::loadDriver()
	 *
	 * @return	mixed
	 */
	public function getDriver();

	/**
	 * Initialize and assign vendor specific driver.
	 *
	 * Requirements:
	 * 1) It is a RunTimeException to initialize the driver and not 
	 *    create a vendor specific driver. For example the MysqliAdapter
	 *	  will throw when mysqli_init fails
	 *
	 * @throws	RunTimeException
	 * @return	DbConnectionInterface
	 */
	public function loadDriver();

	/**
	 * Determines is the vendor database driver is set. Each adapter should
	 * check against type with instanceof or is_a
	 *
	 * @return	bool
	 */
	public function isDriver();

	/**
	 * Connect to the database server using the connection details from
	 * the ConnectionDetail object. 
	 * 
	 * Requirements:
	 * 1) If already connected then return true
	 * 2) If no driver exists then load driver
	 * 3) If connection fails set error with failure message and code and
	 *	  set connection flag to false
	 * 4) If connection passes set connection flag to true
	 *
	 * @return	bool
	 */
	public function connect();

	/**
	 * Close the database connection.
	 * 
	 * Requirements:
	 * 1) If not connected return true since we are already closes.
	 * 2) If no driver exits return true since we can not be connected
	 * 3) If the driver fails to close return false
	 * 4) If the driver closes set isConnected flag to false and return true
	 *
	 * @return bool
	 */
	public function close();

	/**
	 * Flag used to detemine if a connection to the database is open
	 *
	 * @return	bool
	 */
	public function isConnected();

	/**
	 * A vendor specific object used to run queries based on the 
	 * DbRequestInterface
	 *
	 * @return	DbQueryInterface
	 */	
	public function createDbQuery();

	/**
	 * Flag used to determine if a connection error has occured
	 *
	 * @return	bool
	 */
	public function isError();

	/**
	 * @return	Appfuel\Error\ErrorItemInterface
	 */
	public function getError();
}
