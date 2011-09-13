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
namespace Appfuel\View\Formatter;

use Appfuel\Framework\Exception,
	Appfuel\Framework\View\Formatter\ViewFormatterInterface;

/**
 * Converts each key value pair in an associative array as a new line 
 */
class TextFormatter implements ViewFormatterInterface
{
	/**
	 * Used to delimit key and value
	 * @var	string
	 */
	protected $keyDelimiter = ' ';

	/**
	 * Used to delimit each item of an array
	 * @var string
	 */
	protected $itemDelimiter = ' ';

	/**
	 * Strategy used to determine how to treat arrays
	 * @var	string
	 */
	protected $arrayStrategy = 'assoc';

	/**
	 * @param	string	$keyDelimiter	
	 * @param	string	$itemDelimiter
	 * @return	TextFormatter
	 */
	public function __construct($kdel = null, $idel = null, $strategy = null)
	{
		if (null === $kdel) {
			$kdel = ' ';
		}

		if (null === $idel) {
			$idel = ' ';
		}
	
		if (! is_string($kdel) || ! is_string($idel)) {
			throw new Exception("delimiters must be a string");
		}

		$this->keyDelimiter  = $kdel;
		$this->itemDelimiter = $idel;
				
		$err = "array strategy must be a string -(accoc|values|keys)";
		if (null === $strategy) {
			$strategy = 'assoc';
		}

		if (! is_string($strategy)) {
			throw new Exception($err);
		}

		$strategy = strtolower($strategy);
		$valid = array('assoc', 'values', 'keys');
		if (! in_array($strategy, $valid)) {
			throw new Exception($err);
		}
		$this->arrayStrategy = $strategy;
	}

	/**
	 * @return	bool
	 */
	public function isFormatArrayValues()
	{
		return $this->arrayStrategy === 'values';
	}

	/**
	 * @return	bool
	 */
	public function isFormatArrayKeys()
	{
		return $this->arrayStrategy === 'keys';
	}

	/**
	 * @return	bool
	 */
	public function isFormatArrayAssoc()
	{
		return $this->arrayStrategy === 'assoc';
	}

	/**
	 * @return	string
	 */
	public function getArrayStrategy()
	{
		return $this->arrayStrategy;
	}

	/**
	 * @return	TextFormatter
	 */
	public function setFormatArrayKeys()
	{
		$this->arrayStrategy = 'keys';
		return $this;
	}
	
	/**
	 * @return	TextFormatter
	 */
	public function setFormatArrayValues()
	{
		$this->arrayStrategy = 'values';
		return $this;
	}

	public function setFormatArrayAssoc()
	{
		$this->arrayStrategy = 'assoc';
		return $this;
	}
	
	/**
	 * @return	string
	 */
	public function getItemDelimiter()
	{
		return $this->itemDelimiter;	
	}

	/**
	 * @return	string
	 */
	public function getKeyDelimiter()
	{
		return $this->keyDelimiter;	
	}

    /** 
     * @param   mixed	$data
	 * @return	string
     */
    public function format($data)
    {
		if (is_string($data)) {
			return $data;
		}
		else if (is_object($data) && is_callable(array($data, '__toString'))) {
			return $data->__toString();
		}
		else if (! is_array($data)) {
			return '';
		}

		$itemDelimiter = $this->getItemDelimiter();
		$keyDelimiter  = $this->getKeyDelimiter();

		switch ($this->getArrayStrategy()) {
			case 'keys':
				$result = implode($keyDelimiter, array_keys($data));
				break;
			case 'values':
				$result = implode($itemDelimiter, array_values($data));
				break;
			case 'assoc':
				$result  = '';
				foreach ($data as $key => $value) {
					$result .= $key . $keyDelimiter . $value . $itemDelimiter;
				}
				$result = trim($result, "$itemDelimiter");
				break;
			default:
				$result = '';
		}

		return $result;
    }
}
