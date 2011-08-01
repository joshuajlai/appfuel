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
	Appfuel\Framework\Validate\CoordinatorInterface,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * Handle the movement or raw and clean data as well as handling text. The 
 * coordinator is used by the Validator; sets the source (raw data) into the 
 * coordinator. The Test has the coordinator passed into it for which is can 
 * retreive raw data and set clean data for criterion/criteria that pass and
 * errors for thoses that fail.
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
     * @var array
     */
    protected $errors = array();

    /**
     * @param   mixed   $source   data source used for validation
     * @return  Coordinator
     */
    public function __construct($source = null)
    {   
        if (! empty($source)) {
            $this->setSource($source);
        }
    }

    /**
     * @return array
     */
    public function getAllClean()
    {
        return $this->clean;
    }

    /**
	 * @throws	Appfuel\Framework\Exception
     * @param   string  $field
     * @param   mixed   $value
     * @return  Coordinator
     */
    public function addClean($field, $value)
    {
        if (empty($field) || ! is_scalar($field)) {
            throw new Exception("Can not add to clean field must be scalar");
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
		if (empty($field) || ! is_scalar($field) ||
			! array_key_exists($field, $this->clean)) {
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
    public function setSource($source)
    {
        if ($source instanceof DictionaryInterface) {
            $source = $source->getAll();
        }
        else if (! is_array($source)) {
            throw new Exception("Datasource must be an array or dictionary");
        }

        $this->source = $source;
        return $this;
    }

	/**
	 * Key that is not likely to a value in the raw data. This is used so
	 * we can know the difference between a key that does not exist in the 
	 * raw source and one that exists but has a value of null or false, the
	 * values that are normally returned when a key is not found
	 *
	 * @return	string
	 */
	public function rawKeyNotFound()
	{
		return '___AF_RAW_KEY_NOT_FOUND___';
	}

    /**
     * @param   string  $key
     * @return  mixed | special token to indicate not found
     */
    public function getRaw($field)
    {
        if (empty($field) || ! is_scalar($field) || 
				! array_key_exists($field, $this->source)) {
            return $this->rawKeyNotFound();
        }

        return $this->source[$field];
    }

    /**
	 * @param	string	$field	the field this error is for
     * @param   string  $txt
     * @return  FilterValidator
     */
    public function addError($field, $txt)
    {
        if (empty($field) || ! is_scalar($field)) {
            throw new Exception("Error key must be a non empty scalar");
        }

		if (! $this->isFieldError($field)) {
			$this->errors[$field] = $this->createError($field, $txt);
			return $this;
		}

        $error = $this->errors[$field];
		$error->add($txt);
        return $this;
    }

	/**
	 * Determines if an error exists for a particular field
	 * 
	 * @param	string	$field
	 * @return	bool
	 */
	public function isFieldError($field)
	{
		if (array_key_exists($field, $this->errors) && 
				$this->errors[$field] instanceof ErrorInterface) {
			return true;
		}

		return false;
	}

	/**
	 * @return		string | array | null if not found
	 */
	public function getError($field)
	{
		if (empty($field) || ! is_scalar($field) || 
				! array_key_exists($field, $this->errors)) {
			return null;
		}

		return $this->errors[$field];
	}

    /**
     * @return bool
     */
    public function isError()
    {
        return count($this->errors) > 0;
    }

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

	/**
	 * @return	Coordinator
	 */
	public function clearErrors()
	{
		$this->errors = array();
		return $this;
	}

	/**
	 * @param	string	$field
	 * @param	string	$msg
	 * @return	Error
	 */
	protected function createError($field, $msg)
	{
		return new Error($field, $msg);
	}
}
