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

use Iterator,
	Countable;

/**
 * This interface is an vendor agnostic object used to encapsulate the
 * resultset returned from database execution. This Object also delegates
 * all errors to the Appfuel\Error\ErrorStack. It is encouraged to pass
 * the error stack through the constructor making it immutable.
 */
interface DbResponseInterface extends Iterator, Countable
{
	/**
	 * The error stack is an Appfuel\Error\ErrorStackInterface that 
	 * encapsulates handling a list of errors.
	 *
	 * Requirements:
	 * 1) No public setters 
	 * 2) ErrorStackInterface should be passed into the constructor
	 *
	 * @return	ErrorStackInterface
	 */
	public function getErrorStack();
	
	/**
	 * Should grap the current error out of the error stack. 
	 *
	 * @return	ErrorInterface
	 */
	public function getError();

	/**
	 * Determines if any errors are in the error stack.
	 *
	 * @return	bool
	 */	
	public function isError();

	/**
	 * @return	array
	 */
	public function getResultSet();

	/**
	 * Manually set the resultset. The resultset is the array arrays
	 * with key value key => values pairs that is returned from the
	 * database. If the vendor adapter supports multi queries then the 
	 * resultset will be an array of DbResponseInterfaces each one 
	 * representing that queries resultset.
	 *
	 * @param	array	$result
	 * @return	DbResponseInterface
	 */
	public function setResultSet(array $results);

	/**
	 * Allows you to add a result to the result set.
	 *
	 * Requirements:
	 * 1) $result must be an array or DbResponseInterface
	 *	  It is an InvalidArgumentException	for result to be anythingelse
	 * 2) If key is not supplied or null than key will take the integer of
	 *	  DbResponseInterface::count(). 
	 * 3) Key must be an integer or a non empty string 
	 *	  It is an InvalidArgumentException for key to be anythingelse
	 * 4) If $key is a string then key must be trimed left and right
	 *
	 * @throws	InvalidArgumentException
	 * @param	mixed array|DbResponseInterface $result
	 * @param	mixed int|string $key
	 * @return	DbResponseInterface
	 */
	public function addResult($result, $key = null);
}
