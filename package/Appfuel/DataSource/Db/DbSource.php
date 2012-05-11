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

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\FileViewTemplate;

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
		if (! $conn instanceof DbConnInterface) {
			$err  = 'Database connector has not been correctly instatiated ';
			$err .= 'connection object must implment an Appfuel\DataSource';
			$err .= '\Db\DbConnInterface';
			throw new LogicException($err);
		}

		if (! $conn->isConnected()) {
			$conn->connect();
		}

		$handler = $conn->createDbAdapter($request->getType());
		if (! $handler instanceof DbHandlerInterface) {
			$class = get_class($handler);
			$err   = "database vendor adapter -($class) does not implement ";
			$err  .= "Appfuel\DataSource\Db\DbAdapterInterface";
			throw new LogicException($err);
		}

		return $handler->execute($request, $response);
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
	 * @param	string	$sql
	 * @param	string	$type
	 * @param	string	$stategy
	 * @param	mixed	$values 
	 * @return	DbRequest
	 */
	public function createRequest($sql,$type=null,$strategy=null,$values=null)
	{
		$request =  new DbRequest($sql, $type, $strategy);
		if (null !== $values) {
			if (is_scalar($values)) {
				$values = array($values);
			}
			else if (! is_array($values)) {
				$paramType = gettype($values);
				$err = "values must be a scalar of an array -($paramType)";
				throw new InvalidArgumentException($err);
			}
			$request->setValues($values);
		}

		return $request;
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
