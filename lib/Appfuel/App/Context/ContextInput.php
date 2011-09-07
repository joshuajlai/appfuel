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
namespace Appfuel\App\Context;

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\Context\ContextInputInterface;

/**
 * Handles user input for the application context. 
 */
class ContextInput implements ContextInputInterface
{
    /**
	 * User input parameters separated by parameter type.
     * @var array
     */
    protected $params = array();

    /**
     * Method used for this request POST | GET
     * @var string
     */
    protected $method = null;

	/**
	 * Input method represents how the request was made
	 *
	 * @var	array
	 */
	protected $validMethods = array('get', 'post', 'cli');

    /**
     * Assign the uri, parameters and request method. Because the uri contains
	 * all the get parameters we pull them out and add them to the
	 * the others (post, cookie, files) which are passed into the constructor.
	 * We also look for additional get params and merge them as required.
	 *
	 * @param	Uri		$uri
	 * @param	array	$params		holds post, files, cookie parameters
	 * @param	string	$rm			request method
     * @return	Request
     */
    public function __construct($method, array $params = array())
    {
		$err = 'Failed to instantiate request: ';
		if (empty($method) || ! is_string($method)) {
			throw new Exception("$err method must be a non empty string");
		}
	
		$method = strtolower($method);
		if (! in_array($method, array('get', 'post', 'cli'))) {
			throw new Exception("$err method must be one of -(get|post|cli)");
		}
        $this->method = $method;

		/*
		 * Ensure each type exists as an array. because searching the
		 * params uses functions the work on arrays
		 */
		$types = array('post', 'get', 'files', 'cookie', 'argv');
		foreach ($types as $type) {

			/* add missing type */
			if (! isset($params[$type])) {
				$params[$type] = array();
			}
			
			if (! is_array($params[$type])) {
				$err .= "request param for -($type) must be an array";
				throw new Exception($err);
			}
			
			/* make sure he the array of key value pairs have valid keys */
			foreach ($params[$type] as $key => $value) {
				if (empty($key)) {
					$err .= "request param for -($type) key must not be empty";
					throw new Exception($err);
				}
			}
		}

		$this->params = $params;
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return 'post' === $this->getMethod();
    }

    /**
     * @return string
     */
    public function isGet()
    {
        return 'get' === $this->getMethod();
    }

	/**
	 * @return	string
	 */
	public function isCli()
	{
		return 'cli' === $this->getMethod();
	}

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * The params member is a general array that holds any or all of the
     * parameters for this request. This method will search on a particular
     * parameter and return its value if it exists or return the given default
     * if it does not
     *
     * @param   string  $key        used to find the label
     * @param   string  $type       type of parameter get, post, cookie etc
     * @param   mixed   $default    value returned when key is not found
     * @return  mixed
     */
	public function get($type, $key, $default = null)
	{
		if (! $this->isValidParamType($type) || ! is_scalar($key)) {
			return $default;
		}

		$type = strtolower($type);
        if (! isset($this->params[$type][$key])) {
            return $default;
        }

		return $this->params[$type][$key];
	}

	/**
	 * Used to collect serval parameters based on an array of keys.
	 * 
	 * @param	array	$keys	list of parameter labels to collect
	 * @param	array	$type	which request type get, post, argv etc..
	 * @param	array	$returnArray 
	 * @return	Dictionary
	 */
	public function collect($type, array $keys, $returnArray = false) 
	{
		$result = array();
		$notFound = '__AF_KEY_NOT_FOUND__';
		foreach ($keys as $key) {
			$value = $this->get($type, $key, $notFound);

			/* 
			 * null or false could be accepted values and we need to
			 * know when default comes back as true not not found vs 
			 * the real value and default being the same
			 */
			if ($value === $notFound) {
				continue;
			}
			$result[$key] = $value;
		}

		if (true === $returnArray) {
			return $result;
		}

		return new Dictionary($result);
	}

    /**
     * @param   string  $type
     * @return  array
     */
    public function getAll($type = null)
    {
		if (null === $type) {
			return $this->params;
		}

		if (! $this->isValidParamType($type)) {
			return false;
		}

        return $this->params[strtolower($type)];
    }

	/**
	 * @param	string	$type	
	 * @return	bool
	 */
	public function isValidParamType($type)
	{
		if (empty($type) || ! is_string($type)) {
			return false;
		}

		$type  = strtolower($type);
		if (! isset($this->params[$type])) {
			return false;
		}
		
		return true;
	}
}
