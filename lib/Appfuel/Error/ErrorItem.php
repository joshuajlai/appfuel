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

use InvalidArgumentException;

/**
 * Value object used to hold the basic information of an error
 */
class ErrorItem implements ErrorInterface
{
	/**
	 * Number or text used to represent the error
	 * @var scalar
	 */
	protected $code = null;

	/**
	 * Text used to describe the error
	 * @return	null
	 */
	protected $message = null;

	/**
	 * @param	string	$msg 
	 * @param	scalar	$code	optional
	 * @return	Error
	 */
	public function __construct($msg, $code = null)
	{
		$this->setMessage($msg);
		
		if (null !== $code) {
			$this->setCode($code);
		}
	}

	/**
	 * @return	string
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * @return	scalar
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		$code = $this->getCode();
		$str  = '';
		if (strlen($code) > 0) {
			$str = "[$code]: ";
		}
		return "$str{$this->getMessage()}";
	}

	/**
	 * @param	string	$msg
	 * @return	null
	 */
	protected function setMessage($msg)
	{
		if (is_scalar($msg) ||
			(is_object($msg) && is_callable(array($msg, '__toString')))) {
			$msg =(string)$msg;
		}
		else {
			$err  = 'Error message must be a scalar value or an object that ';
			$err .= 'implements __toString method'; 
			throw new InvalidArgumentException($err);
		}

		$this->message = trim($msg);
	}

	/**
	 * @param	scalar	$code
	 * @return	null
	 */
	protected function setCode($code)
	{
		if (is_scalar($code) ||
			(is_object($code) && is_callable(array($code, '__toString')))) {
			$code =(string)$code;
		}
		else {
			$err  = 'Error code must be a scalar value or an object that ';
			$err .= 'implements __toString method'; 
			throw new InvalidArgumentException($err);
		}

		$this->code = trim($code);
	}
}
