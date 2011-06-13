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
namespace Appfuel\Db\Mysql\Adapter;

use Appfuel\Framework\Db\Adapter\ErrorInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class Error implements ErrorInterface
{
	/**
	 * Error code handed back from mysqli handle
	 * @var string
	 */
	protected $code = null;

	/**
	 * Error string associated with the error code
	 * @var string
	 */
	protected $message = null;

	/**
	 * @param	string	$code
	 * @param	string	$msg	default empty string
	 * @return	Error
	 */
	public function __construct($code, $msg = '')
	{
		$this->code    = $code;
		$this->message = $msg;
	}

	/**
	 * @return	string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getCode() . ': ' . $this->getMessage();
	}
}
