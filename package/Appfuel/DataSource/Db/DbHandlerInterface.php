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

/**
 * The database adapter is 
 */
interface DbHandlerInterface
{

	/**
	 * 1) $driver is any vendor specific driver/handle used send queries or 
	 *	  commands to the database server. For example, mysqli is native php
	 *	  object used as a driver for mysql.
	 * 2) This method must validate if the driver matches the expected type.
	 *    Because this interface is used by many different vendors we can not
	 *    type hint.
	 *    i) throw an InvalidArgumentException if the type of driver is not 
	 *       valid
	 * 
	 * @throw	InvalidArgumentException
	 * @param	mixed	$driver
	 * @return	DbHandlerInterface
	 */
	public function setDriver($driver);

	/**
	 * @return	mixed
	 */
	public function getDriver();

	/**
	 * 1) Use the driver to execute the appropriate commands on the database
	 *    server, describe by the request object
	 * 2) The request interface has a 'getType' this can be used to create
	 *	  a strategy for the following types: query, multiQuery and 
	 *    preparedStmt. If your vendor can not support a request type you
	 *	  must throw a RunTimeException
	 * 3) Once the commands have been issued on the database server the
	 *    results are put in the response object
	 * 4) All database command related errors are put in the response
	 * 5) Return the response 
	 *
	 * @param	DbRequestInterface $request
	 * @return	DbReponseInterface
	 */
	public function execute(DbRequestInterface $request,
							DbResponseInterface $response);
}
