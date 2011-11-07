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
	 * The error header is displayed when error is used in the context of
	 * a string
	 * @var string
	 */
	protected $header = 'Error';

	/**
	 * Flag used to determine if the header should be used in __toString
	 * @var string
	 */
	protected $isHeader = true;

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
	public function getErrorString()
	{
		$header = '';
		if ($this->isErrorHeader()) {
			$header = $this->getErrorHeader();
			$code = $this->getCode();
			if (strlen($code) > 0) {
				$code = "[$code]";
			}
			$header .= "$code: ";
		}

		return "{$header}{$this->getMessage()}";
	}

	/**
	 * @return	string
	 */
	public function getErrorHeader()
	{
		return $this->header;
	}

	/**
	 * @param	string	$text
	 * @return	AppfuelError
	 */
	public function setErrorHeader($text)
	{
		if (! is_string($text)) {
			return $this;
		}

		$this->header = $text;
		return $this;
	}

	/**
	 * @return	AppfuelError
	 */
	public function disableErrorHeader()
	{
		$this->isHeader = false;
		return $this;
	}

	/**
	 * @return	AppfuelError
	 */
	public function enableErrorHeader()
	{
		$this->isHeader = true;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isErrorHeader()
	{
		return $this->isHeader;
	}

	/**
	 * @return	string
	 */
	public function __toString()
	{
		return $this->getErrorString();
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
			$msg  = "<error setting this message unsupported type -($type)>";
		}
		$this->message = trim($msg);
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

		$this->code = trim($code);
	}

}
