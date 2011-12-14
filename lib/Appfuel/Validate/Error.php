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
namespace Appfuel\Validate;

use InvalidArgumentException;

/**
 * Holds one or more error messages for a single field
 */
class Error implements ErrorInterface
{
    /**
     * Field these errors represent
     * @var array
     */
    protected $field = null;

    /**
     * Error message used when criteria fails
     * @var array
     */
    protected $errors = array();

	/**
	 * Default separator for when messages a concatenated into a single string
	 * @var string
	 */
	protected $sep = ' ';

    /**
     * @param   mixed   $source   data source used for validation
     * @return  Coordinator
     */
    public function __construct($field, $message)
    {
		$this->setField($field);
		$this->add($message);  
    }

    /**
     * @return array
     */
    public function getField()
    {
        return $this->field;
    }

	/**
	 * @return string
	 */
	public function getSeparator()
	{
		return $this->sep;
	}

	/**
	 * @param	string $char
	 * @return	Error
	 */
	public function setSeparator($char)
	{
		if (! is_string($char)) {
			throw new InvalidArgumentException("separator must be a string");
		}

		$this->sep = $char;
		return $this;
	}

	/**
	 * Return the full list of errors for this field
	 * 
	 * @return	array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Return total number of errors
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->errors);
	}

	/**
	 * @param	string $msg
	 * @retun	Error
	 */
	public function add($msg)
	{
		if (empty($msg) || ! is_string($msg)) {
			throw new InvalidArgumentException(
				"error message must be a non empty string"
			);
		}

		$this->errors[] = $msg;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function current()
	{
		return current($this->errors);
	}

	/**
	 * @return	null
	 */
	public function next()
	{
		next($this->errors);
	}

	/**
	 * @return	int
	 */
	public function key()
	{
		return key($this->errors);
	}

	/**
	 * @return	bool
	 */
	public function valid()
	{
		return is_int($this->key());
	}

	/**
	 * @return null
	 */
	public function rewind()
	{
		reset($this->errors);
	}

	/**	
	 * Determines how this object looks in the context of a string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$field = $this->getField();
		$sep = $this->getSeparator();
		return $field . $sep . implode($sep, $this->errors);
		
	}

	/**
	 * @param	scalar	$field
	 * @return	null
	 */
	protected function setField($field)
	{
		if ((is_string($field) && empty($field)) || ! is_scalar($field)) {
			throw new InvalidArgumentException(
				"field must be a non empty scalar value"
			);
		}

		$this->field = $field;
	}
}
