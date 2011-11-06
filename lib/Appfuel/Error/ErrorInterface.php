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
 * Value object used to hold the details of an error in the application
 */
interface ErrorInterface
{
	/**
	 * @return	string
	 */
	public function getMessage();
	
	/**
	 * @return	scalar
	 */
	public function getCode();

	/**
	 * Allow the error to be used in the context of a string
	 * @return	string
	 */
	public function __toString();
}
