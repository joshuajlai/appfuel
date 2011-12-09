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

use RunTimeException;

/**
 * The database handler is responsible for handling database requests without
 * knowledge of the database vendor or what adapter is used with that vendor.
 * Its only job is to grab the correct connection, use it to create an 
 * adaoter  and decide which adapter method to use based on the request, 
 * finishing by returning a DbResponse.
 */
class DbHandler implements DbHandlerInterface
{
	/**
	 * Use the connection to create an adapter that will service the request.
	 * Every request has a code that the connection object uses to determine
	 * which adapter will be used. 
	 *
	 * @param	DbRequestInterface $request
	 * @return	DbResponse
	 */
	public function execute(DbRequestInterface $request)
	{
		$connector = DbRepository::getConnector();

		$conn = $connector->getConnection($request->getStrategy());
		if (! $conn instanceof DbConnectionInterface) {
			throw new RunTimeException("DbHandler not properly initialized");
		}

		if (! $conn->isConnected()) {
			$conn->connect();
		}

		$adapter  = $conn->createAdapter($request->getCode());
		return $adapter->execute($request);
	}
}
