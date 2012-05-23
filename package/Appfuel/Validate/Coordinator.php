<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Validate;

use InvalidArgumentException,
	Appfuel\Error\ErrorStack,
	Appfuel\Error\ErrorStackInterface;

/**
 * Coordinates the movement of data. This includes raw fields, clean or 
 * filtered fields and all errors.
 */
class Coordinator implements CoordinatorInterface
{
    /**
     * Data source to validate on
     * @var mixed
     */
    protected $source = array();

    /**
     * Hold data that has been considered safe
     * @var array
     */
    protected $clean = array();

    /**
     * Error message used when criteria fails
     * @var ErrorStackInterface
     */
    protected $errors = null;

    /**
     * @param   ErrorStackInterface $stack
     * @return  Coordinator
     */
    public function __construct(ErrorStackInterface $stack = null)
    {
		if (null === $stack) {
			$stack = new ErrorStack();
		}
		$this->setErrorStack($stack);
    }

    /**
     * @return array
     */
    public function getAllClean()
    {
        return $this->clean;
    }

    /**
	 * @throws	InvalidArgumentException
     * @param   string  $field
     * @param   mixed   $value
     * @return  Coordinator
     */
    public function addClean($field, $value)
    {
        if (! is_scalar($field) || empty($field)) {
			$err = "Can not add to clean field must be scalar";
            throw new InvalidArgumentException($err);
        }

        $this->clean[$field] = $value;
        return $this;
    }

    /**
     * @param   string  $field
     * @param   mixed   $default
     * @return  mixed
     */
    public function getClean($field, $default = null)
    {
		if (! is_scalar($field) || ! array_key_exists($field, $this->clean)) {
			return $default;
		}

        return $this->clean[$field];
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
	 * The raw source must be an array of key=>value or a dictionary object
	 * 
     * @param   mixed
     * @return  Validator
     */
    public function setSource(array $source)
    {
        $this->source = $source;
        return $this;
    }

	/**
	 * Key that is not likely to be a value in the raw data. This is used so
	 * we can know the difference between a key that does not exist in the 
	 * raw source and one that exists but has a value of null or false, the
	 * values that are normally returned when a key is not found
	 *
	 * @return	string
	 */
	public function rawFieldNotFound()
	{
		return '___AF_FIELD_NOT_FOUND___';
	}

	/**
	 * @param	scalar	$field
	 * @return	bool
	 */
	public function isRaw($field)
	{
		if (! is_scalar($field) || ! array_key_exists($field, $this->source)) {
			return false;
		}

		return true;
	}

    /**
     * @param   string  $key
     * @return  mixed | special token to indicate not found
     */
    public function getRaw($field)
    {
        if (! $this->isRaw($field)) {
            return self::FIELD_NOT_FOUND;
        }

        return $this->source[$field];
    }

    /**
	 * @param	string	$field	the field this error is for
     * @param   string  $txt
     * @return  FilterValidator
     */
    public function addError($msg, $code = 500)
    {
		$this->getErrorStack()
			 ->addError($msg, $code);
	
        return $this;
    }

	/**
	 * @return	ErrorStackInterface
	 */
	public function getErrorStack()
	{
		return $this->errorStack;
	}

	/**
	 * @param	ErrorStackInterface $stack
	 * @return	Coordinator
	 */
	public function setErrorStack(ErrorStackInterface $stack)
	{
		$this->errors = $stack;
		return $this;
	}

	/**
	 * This is used when you want to re-use the coordinator for the same fields
	 * but a new set of raw input
	 *
	 * @return null
	 */	
	public function reset()
	{
		$this->clean = array();
		$this->raw   = array();
		$this->getErrorStack()
			 ->clear();
	}

}
