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
class DbSource implements DbSourceInterface
{
	/**
	 * Use the connection to create an adapter that will service the request.
	 * Every request has a code that the connection object uses to determine
	 * which adapter will be used. 
	 *
	 * @param	DbRequestInterface $request
	 * @param	DbResponseInterface $response
	 * @param	string	$key 
	 * @return	DbResponse
	 */
	public function execute(DbRequestInterface $request,
							DbResponseInterface $response = null,
							$key = null)
	{
		$connector = $this->getConnector($key);
		if (! $connector instanceof DbConnectorInterface) {
			$err  = "Database startup task has not been run or your ";
			$err .= "configuration has no database connectors, could not ";
			$err .= "find database connector for -($key)";
			throw new LogicException($err);
		}

		if (null === $response) {
			$response = $this->createResponse();
		}

		$conn = $connector->getConnection($request->getStrategy());
		if (! $conn instanceof DbConnectionInterface) {
			$err  = 'Database connector has not been correctly instatiated ';
			$err .= 'connection object must implment an Appfuel\DataSource';
			$err .= '\Db\DbConnInterface';
			throw new LogicException($err);
		}

		if (! $conn->isConnected()) {
			$conn->connect();
		}

		$adapter = $conn->createAdapter($request->getCode());
		if (! $adapter instanceof DbAdapterInterface) {
			$class = get_class($adapter);
			$err   = "database vendor adapter -($class) does not implement ";
			$err  .= "Appfuel\DataSource\Db\DbAdapterInterface";
			throw new LogicException($err);
		}

		return $adapter->execute($request, $response);
	}

	/**
	 * @param	mixed $connector
	 * @return	bool
	 */
	public function createResponse()
	{
		return new DbResponse();
	}

	/**
	 * @param	string	$key
	 * @return	DbConnectorInterface | false 
	 */
	public function getConnector($key = null)
	{
		if (null === $key) {
			$key = DbRegistry::getDefaultConnectorKey();
		}
		
		return DbRegistry::getConnector($key);
	}
}
