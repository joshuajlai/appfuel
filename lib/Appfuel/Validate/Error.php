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

use Appfuel\Framework\Exception,
	Appfuel\Framework\Validate\ErrorInterface,
	Appfuel\Framework\DataStructure\DictionaryInterface;

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
	protected $sep = ':';

    /**
     * @param   mixed   $source   data source used for validation
     * @return  Coordinator
     */
    public function __construct($field, $message)
    {
		$this->setField($field);
		$this->setError($message);  
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
			throw new Exception("separator must be a string");
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
			throw new Exception("error message must be a non empty string");
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
		key($this->errors);
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

	public function __toString()
	{
		$sep = $this->getSeparator();
		return implode($sep, $this->errors);
		
	}

	protected function setField($field)
	{
		if (empty($field) || ! is_scalar($field)) {
			throw new Exception("field must be a non empty scalar value");
		}

		$this->field = $field;
	}

	protected function setError($error)
	{
		if (! empty($error) && is_string($error)) {
			$this->errors[] = $error;
			return;
		}
		else if (! empty($error) && is_array($error)) {
			foreach ($error as $msg) {
				if (empty($msg) && ! is_string($msg)) {
					throw new Exception("error list must have all strings");
				}
				$this->errors[] = $msg;
			}
			return;
		}

		throw new Exception("invalid set error: error format not known");
	}
}
