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
     * @param   string  $label
     * @param   mixed   $value
     * @return  Coordinator
     */
    public function addClean($label, $value)
    {
        if (empty($label) || ! is_scalar($label)) {
            throw new Exception("Can not add to clean label must be label");
        }

        $this->clean[$label] = $value;
        return $this;
    }

    /**
     * @param   string  $label
     * @param   mixed   $default
     * @return  mixed
     */
    public function getClean($label, $default = null)
    {
		if (empty($label) || ! is_scalar($label) ||
			! array_key_exists($label, $this->clean)) {
			return $default;
		}

        return $this->clean[$label];
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
    public function getRaw($key)
    {
        if (empty($key) || ! is_scalar($key) || 
				! array_key_exists($key, $this->source)) {
            return $this->rawKeyNotFound();
        }

        return $this->source[$key];
    }

    /**
     * @param   string  $txt
     * @return  FilterValidator
     */
    public function addError($txt)
    {
        $this->errors[] = $txt;
        return $this;
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
}
