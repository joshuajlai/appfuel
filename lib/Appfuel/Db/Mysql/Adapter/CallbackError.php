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

use Appfuel\Framework\Db\Adapter\CallbackErrorInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class CallbackError extends Error implements CallbackErrorInterface
{
	/**
	 * Index number of row that failed 
	 * @var int
	 */
	protected $rowIndex = null;

	/**
	 * Original db row when failure occured
	 * @var array
	 */
	protected $row = false;

	/**
	 * Name of the callback or closure used
	 * @var string
	 */
	protected $callbackType = null;

	/**
	 * @return	string
	 */
	public function getRowNumber()
	{
		return $this->rowIndex;
	}

	/**
	 * @param	int		$index
	 * @return	null
	 */
	public function setRowNumber($index)
	{
		$this->rowIndex = $index;
	}

	/**
	 * @return array
	 */
	public function getRow()
	{
		return $this->row;
	}

	public function setRow($row = null)
	{
		$this->row = $row;
	}

	/**
	 * @return string
	 */
	public function getCallbackType()
	{
		return $this->callbackType;
	}

	/**
	 * @param	string	$type
	 * @return	null
	 */
	public function setCallbackType($type)
	{
		$this->callbackType = $type;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return "ERROR {$this->code} ({$this->sqlState}): {$this->message} " .
			   "ON ROW NUMBER : {$this->rowIndex} " .
			   "CALLBACK TYPE : {$this->callbackType} ";
	}
}
