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
class AppfuelError implements ErrorInterface
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
	 * @param	scalar	$code
	 * @param	scalar	$level
	 * @return	AppfuelError
	 */
	public function __construct($msg, $code = null)
	{
		$this->setMessage($msg);
		$this->setCode($code);
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
	 * @return	string
	 */
	public function __toString()
	{
		$code = $this->getCode();
		if (strlen($code) > 0) {
			$code = "[$code]";
		}
		return "Error{$code}: {$this->getMessage()}";
	}

	/**
	 * @param	string	$msg
	 * @return	null
	 */
	protected function setMessage($msg)
	{
		if (is_object($msg) && is_callable(array($msg, '__toString'))) {
			$msg =(string) $msg;	
		}
		else if (! is_string($msg)) {
			$type = gettype($msg);
			$msg  = "<error setting this message unkown format -($type)>";
		}
		$this->msg = $msg;
	}

	/**
	 * @param	scalar	$code
	 * @return	null
	 */
	protected function setCode($code)
	{
		if (! is_scalar($code)) {
			return;
		}

		$this->code = $code;
	}

}
