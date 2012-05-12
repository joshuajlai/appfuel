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
namespace Appfuel\DataSource\Db\Mysql\Mysqli;

use mysqli_stmt,
	Appfuel\Error\ErrorStackInterface;

/**
 * Wraps the mysqli_stmt. There is some complex logic we don't want the 
 * adapter to know about
 */
interface MysqliPreparedStmtInterface
{
	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(mysqli_stmt $driver);

	/**
	 * @return	mysqli_stmt
	 */
	public function getDriver();

	/**
	 * @return	bool
	 */
	public function isDriver();

	/**
	 * @return bool
	 */
	public function isClosed();

	/**
	 * Explictly close the handle. Can not close the handle (mysqli_stmt) 
	 * before it has been prepared.
	 *
	 * @throws	Appfuel\Framework\Exeception	when handle is already closed
	 * @throws	\Exception	when trying to close before prepared
	 * @return	bool
	 */
	public function close();

	/**
	 * @return bool
	 */
	public function isError();
	
	/**
	 * @return Error
	 */
	public function getError();

	/**
	 * @return bool
	 */
	public function isPrepared();

	/**
	 * Prepare the sql for execution
	 * 
	 * @param	string	$sql	sql query to be prepared
	 * @return	bool
	 */
	public function prepare($sql);

	/**
	 * Normalize the parameters so they can by used in the bind_params call
	 * and bind them
	 *
	 * @param	array	$params		list of values in the prepared stmt
	 * @return	bool
	 */
	public function organizeParams(array $params);

	/**
	 * In order to work with an unkown number of prameters generically we
	 * need to inspect the orignal parameters and normalized into an array
	 * that it compatable with call_user_func_array. The array structure:
	 * first element is a string where each char 's' represents an item 
	 * in the array. The other elements represent the orignal parameters.
	 *
	 * @param	array	$params		list of values used in prepared stmt
	 * @return	array
	 */
	public function normalizeParams(array $params);

	/**
	 * Binds multiple parameters with one call to call_user_func_array. Each
	 * item in the array (accept the first) represents a parameter marker in
	 * sql stmt. The first parameter is a string where each character 
	 * also represents a sql parameter marker.
	 * 
	 * @param	array	$params		list of arguments for bind_params
	 * @return	bool
	 */
	public function bindParams(array $params);

	/**
	 * @return bool
	 */
	public function isParamsBound();

	/**
	 * @return bool
	 */
	public function isExecuted();

	/**
	 * @param	string	$sql	
	 * @param	array	$params		values in the prepared sql
	 * @return	bool
	 */
	public function execute();

	/**
	 * @return bool
	 */
	public function organizeResults();

	/**
	 * Buffer the full resultset into memory
	 *
	 * @throws	Appfuel\Framework\Exeception	when resultset is not bound
	 * @return bool
	 */
	public function storeResults();

	/**
	 * Frees the result memory associated with the statement, which was 
	 * allocated by mysqli_stmt_store_result().
	 * 
	 * @return null
	 */
	public function freeStoredResults();

	/**
	 * @return bool
	 */
	public function isBufferedResultset();

	/**
	 * @return	bool
	 */
	public function isResultset();

	/**
	 * @return	bool
	 */
	public function isBoundResultset();

	/**
	 * @param	mixed	$filter		callback or closure to filter rows
	 * @return	array
	 */
	public function fetch(ErrorStackInterface $errStack, $filter = null);

	/**
	 * @return bool
	 */
	public function isFetched();

	/**
	 * Get the ID generated from the previous INSERT operation
	 *
	 * @return	int | null
	 */
	public function getLastInsertId();

	/**
	 * Resets a prepared statement on client and server to state after prepare.
	 * It resets the statement on the server, data sent using 
	 * mysqli_stmt_send_long_data(), unbuffered result sets and current errors.
	 * It does not clear bindings or stored result sets. Stored result sets 
	 * will be cleared when executing the prepared statement (or closing it).
	 *
	 * @return bool
	 */
	public function reset();
}
