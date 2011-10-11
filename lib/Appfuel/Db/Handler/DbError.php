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
namespace Appfuel\Db\Handler;

use Appfuel\Framework\Db\Handler\DbErrorInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class DbError implements DbErrorInterface
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
	 * A 5 character string specified by the ANSI SQL and ODBC this is a
	 * a more standardized error code
	 * @var string
	 */
	protected $sqlState = null;

	/**
	 * @param	string	$code
	 * @param	string	$msg	default empty string
	 * @return	Error
	 */
	public function __construct($code, $msg = null, $sqlState = null)
	{
		$this->code     = $code;
		$this->message  = $msg;
		$this->sqlState = $sqlState;
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
	public function getSqlState()
	{
		return $this->sqlState;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return "ERROR {$this->code} ({$this->sqlState}): {$this->message}";
	}
}
