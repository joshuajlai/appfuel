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

use RunTimeException;

/**
 * The database handler is responsible for handling database requests without
 * knowledge of the database vendor or what adapter is used with that vendor.
 * Its only job is to grab the correct connection, use it to create an 
 * adaoter  and decide which adapter method to use based on the request, 
 * finishing by returning a DbResponse.
 */
interface DbSourceInterface
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
							$key = null);
	/**
	 * @param	mixed $connector
	 * @return	bool
	 */
	public function createResponse();

	/**
	 * @param	string	$key
	 * @return	DbConnectorInterface | false 
	 */
	public function getConnector($key = null);
}
