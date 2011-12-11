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
 * response of database execute from the vendor adapter which is 
 * is run by the DbHandler.
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
	 * Determines if any errors are in the error stack
	 *
	 * @return	bool
	 */	
	public function isError();

	public function getResultSet();
	public function setResultSet(array $results);
	public function addResult($results);
}
