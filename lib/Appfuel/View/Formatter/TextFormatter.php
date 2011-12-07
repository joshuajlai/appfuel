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

use InvalidArgumentException;

/**
 * Converts each key value pair in an associative array as a new line 
 */
class TextFormatter extends BaseFormatter implements ViewFormatterInterface
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
			throw new InvalidArgumentException("delimiters must be a string");
		}

		$this->keyDelimiter  = $kdel;
		$this->itemDelimiter = $idel;
				
		$err = "array strategy must be a string -(accoc|values|keys)";
		if (null === $strategy) {
			$strategy = 'assoc';
		}

		if (! is_string($strategy)) {
			throw new InvalidArgumentException($err);
		}

		$strategy = strtolower($strategy);
		$valid = array('assoc', 'values', 'keys');
		if (! in_array($strategy, $valid)) {
			throw new InvalidArgumentException($err);
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
    public function format(array $data)
    {
		if (empty($data)) {
			return '';
		}

		if (! $this->isValidFormat($data)) {
			$err = 'Text formatter failed: data must be an associative array';
			throw new InvalidArgumentException($err);
		}

		return $this->parseArray($data, $this->getArrayStrategy());	
    }

	/**
	 * @param	mixed	$data
	 * @return	string
	 */
	public function parseString($data)
	{
		if (is_scalar($data)) {
			return $data;
		}
		else if (is_object($data) && is_callable(array($data, '__toString'))) {
			return $data->__toString();
		}
		else {
			return '';
		}
	}

	/**
	 * Parse Array will parse out the data array depending on type
	 * values:	parse only values - will travel down sub arrays 
	 * keys:	parse only keys	  - will not travel down sub arrays
	 * assoc:	parse both keys and values - will travel down sub arrays
	 * 
	 * @param	array	$data
	 * @param	string	$type	values|keys|assoc
	 * @return	string
	 */
	public function parseArray(array $data, $type = 'assoc')
	{
		$itemDelimiter = $this->getItemDelimiter();
		$keyDelimiter  = $this->getKeyDelimiter();
		$trimDelimiter = ' ';

		$result = '';
		$isProcessValues = in_array($type, array('values', 'assoc'));
		foreach ($data as $key => $value) {
			if (is_array($value) && $isProcessValues) {
				$value = $this->parseArray($value, $type);
			}
			else {
				$value = $this->parseString($value);
			}

			switch($type) {
				case 'assoc':
					$result .= $key . $keyDelimiter . $value . $itemDelimiter;
					$trimDelimiter = $itemDelimiter;
					break;
				case 'keys': 
					$result .= $key . $keyDelimiter;
					$trimDelimiter = $keyDelimiter;
					break;
				case 'values':
					$result .= $value . $itemDelimiter;
					$trimDelimiter = $itemDelimiter;
					break;
				default:
					continue;
			}
		}
		return trim($result, "$trimDelimiter");
	}
}
