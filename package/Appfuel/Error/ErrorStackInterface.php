<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Error;

/**
 * The error stack handles a collection of errors
 */
interface ErrorStackInterface 
{
	/**
	 * @param	ErrorInterface	$error
	 * @return	ErrorStackInterface
	 */
	public function addErrorItem(ErrorInterface $error);

	/**
	 * @param	string	$text	
	 * @param	scalar	$code
	 * @return	ErrorStackInterface
	 */
	public function addError($msg, $code = null);

	/**
	 * Alias for current
	 *
	 * @return	ErrorInterface | false when no error exists
	 */
	public function getError();

	/**
	 * @return	ErrorInterface | false when no error exists
	 */
	public function getLastError();

	/**
	 * Clears all errors out of the stack
	 * @return	ErrorStackInterface
	 */
	public function clear();
}
