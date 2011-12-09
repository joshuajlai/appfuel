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
 * Provides a basic interface to describe an error. Errors have a code
 * an a message that all. We offload formatting and aggregation of errors
 * to other objects so we can be just an error item. It is recommended to
 * pass these members through the constructor and make the setter protected.
 */
interface ErrorItemInterface
{
	/**
	 * Any scalar value used to describe the error.
	 *
	 * Requirements:
	 * 1) Must support any scalar value
	 * 2) Must support any object that implements __toString
	 * 3) Empty values are allowed
	 * 4) Must trim value for whitespaces on the left and right
	 * 5) Must throw an InvalidArgumentException if requirements 1 or 2 are 
	 *	  not ment
	 *
	 * @throws	InvalidArgumentException
	 * @return	scalar
	 */
	public function getMessage();
	
	/**
	 * Any scalar value used to identify the error 
	 * 
	 * Follows the same rules as getMessage
	 * 6) The code is optional. This rule should implemented in the constructor
	 *
	 * @throws	InvalidArgumentException
	 * @return	scalar
	 */
	public function getCode();

	/**
	 * Allow this error to be used in the context of a string. 
	 * 
	 * @return	string
	 */
	public function __toString();
}
