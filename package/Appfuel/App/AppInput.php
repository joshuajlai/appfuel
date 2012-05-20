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
namespace Appfuel\App;

use InvalidArgumentException,
	Appfuel\DataStructure\Dictionary;

/**
 * Holds all the input for a given request to the application
 */
class AppInput implements AppInputInterface
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
	 * @param	string	$method	
	 * @param	array	$params
     * @return	AppInput
     */
    public function __construct($method, array $params = array())
    {
		if (! is_string($method) || empty($method)) {
			$err = "method must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->method = strtolower($method);
		$this->params = $params;
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return 'post' === $this->method;
    }

    /**
     * @return string
     */
    public function isGet()
    {
        return 'get' === $this->method;
    }

	/**
	 * @return	bool
	 */
	public function isPut()
	{
		return 'put' === $this->method;
	}

	/**
	 * @return	bool
	 */
	public function isDelete()
	{
		return 'delete' === $this->method;
	}

	/**
	 * @return	string
	 */
	public function isCli()
	{
		return 'cli' === $this->method;
	}

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
   
    /**
     * @param   string  $key 
     * @param   mixed   $default
     * @return  mixed
     */
    public function getParam($key, $default = null)
    {
		$method = $this->getMethod();
		if ('cli' !== $method) {
			return $this->get($method, $key, $default);
		}

		/*
		 * cli short options are only a single character
		 */
		if (1 === strlen($key)) {
			$result = $this->get('short', $key, $default);
		}
		else {
			$result = $this->get('long', $key, $default);
		}

		return $result;
    }

	/**
	 * Used only with command line input. Gets the command name that was used
	 * on the commandline
	 *
	 * @return	string | false
	 */
	public function getCmd()
	{
		$result = false;
		if (isset($this->params['cmd'])) {
			$result = $this->params['cmd'];
		}

		return $result;
	}
	
	/**
	 * @param	string	$opt
	 * @return	bool
	 */
	public function isShortOptFlag($opt)
	{
		if (! isset($this->params['short']) || 
			! isset($this->params['short'][$opt]) ||
			true !== $this->params['short'][$opt]) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string	$opt
	 * @param	mixed	$default
	 * @return	mixed
	 */
	public function getShortOpt($opt, $default = null)
	{
		if (! isset($this->params['short']) || 
			! isset($this->params['short'][$opt])) {
			return $default;
		}

		return $this->params['short'][$opt];
	}

	/**
	 * @param	string	$opt
	 * @return	bool
	 */
	public function isLongOptFlag($opt)
	{
		if (! isset($this->params['long']) || 
			! isset($this->params['long'][$opt]) ||
			true !== $this->params['long'][$opt]) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string	$opt
	 * @param	mixed	$default
	 * @return	mixed
	 */
	public function getLongOpt($opt, $default = null)
	{
		if (! isset($this->params['long']) || 
			! isset($this->params['long'][$opt])) {
			return $default;
		}

		return $this->params['long'][$opt];
	}

	/**
	 * @return	array
	 */
	public function getArgs()
	{
		if (! isset($this->params['args'])) {
			return array();
		}
	
		return $this->params['args'];
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
        if (! array_key_exists($key, $this->params[$type])) {
            return $default;
        }

		return $this->params[$type][$key];
	}

	/**
	 * Used to collect serval parameters based on an array of keys.
	 * 
	 * @param	array	$type	type of parameter stored
	 * @param	array	$key	which request type get, post, argv etc..
	 * @param	array	$isArray 
	 * @return	Dictionary
	 */
	public function collect($type, array $keys, $isArray = false) 
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

		if (true === $isArray) {
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

	/**
	 * Check for the direct ip address of the client machine, try for the 
	 * forwarded address, check for the remote address. When none of these
	 * return false
	 * 
	 * @return	int
	 */
	public function getIp($isInt = true)
	{
		$client  = 'HTTP_CLIENT_IP';
		$forward = 'HTTP_X_FORWARDED_FOR';
		$remote  = 'REMOTE_ADDR'; 
		if (isset($_SERVER[$client]) && is_string($_SERVER[$client])) {
			$ip = $_SERVER[$client];
		}
		else if (isset($_SERVER[$forward]) && is_string($_SERVER[$forward])) {
			$ip = $_SERVER[$forward];
		}
		else if (isset($_SERVER[$remote]) && is_string($_SERVER[$remote])) {
			$ip = $_SERVER[$remote];
		}
		else {
			$ip = false;
		}

		if (false === $ip) {
			return false;
		}

		$isInt = ($isInt === false) ? false : true;
		$format = "%s";
		if (true === $isInt) {
			$format = "%u";
			$ip = ip2long($ip);
		}

		return sprintf($format, $ip);
	}
}
