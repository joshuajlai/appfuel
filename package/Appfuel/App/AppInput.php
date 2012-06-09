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

use	DomainException,	
	InvalidArgumentException,
	Appfuel\DataStructure\Dictionary,
	Appfuel\Validate\ValidationFactory,
	Appfuel\Validate\ValidationHandlerInterface;

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
     * Method used for this request get, post, put, delete or cli
     * @var string
     */
    protected $method = null;

	/**
	 * @var ValidationHandlerInterface
	 */
	protected $handler = null;

    /**
	 * @param	string	$method	
	 * @param	array	$params
     * @return	AppInput
     */
    public function __construct($method, 
								array $params = array(),
								ValidationHandlerInterface $handler = null)
    {
		$this->setMethod($method);
		$this->setParams($params);

		if (null === $handler) {
			$handler = ValidationFactory::createHandler();
		}
		$this->setValidationHandler($handler);
    }

	/**
	 * @return	ValidationHandlerInterface
	 */
	public function getValidationHandler()
	{
		return $this->handler;
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
		if ($this->isCli()) {
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
		if (! isset($this->params['args']['cmd'])) {
			return false;
		}

		return $this->params['args']['cmd'];
	}
	
	/**
	 * @return	array
	 */
	public function getArgs()
	{
		if (! isset($this->params['args']['list'])) {
			return false;
		}
	
		return $this->params['args']['list'];
	}

	/**
	 * @param	string	$opt
	 * @return	bool
	 */
	public function isShortOptFlag($opt)
	{
		if (! is_string($opt) ||
			! isset($this->params['short']) || 
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
		if (! is_string($opt) ||
			! isset($this->params['short']) || 
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
		if (! is_string($opt) ||
			! isset($this->params['long']) || 
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
		if (! is_string($opt) ||
			! isset($this->params['long']) || 
			! isset($this->params['long'][$opt])) {
			return $default;
		}

		return $this->params['long'][$opt];
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
		if (! is_string($type) || empty($type)) {
			return $default;
		}
		$type = strtolower($type);

		if (! is_string($key) || empty($key)) {
			return $default;
		}

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

        return $this->params[$type];
    }

	/**
	 * @param	string	$type	
	 * @return	bool
	 */
	public function isValidParamType($type)
	{
		if (! is_string($type) || ! isset($this->params[$type])) {
			return false;
		}
		
		return true;
	}

	/**
	 * 
	 * @param	array	$list
	 * @return	bool
	 */
	public function isSatisfiedBy(array $list)
	{
		$handler = $this->getValidationHandler();
		
		$raw = array();
		$notFound = '__AF_FIELD_NOT_FOUND__';
		foreach ($list as $data) {
			if (is_array($data)) {
				$key = null;
				if (isset($data['spec'])) {
					$key = $data['spec'];
				}
				$spec = ValidationFactory::createFieldSpec($data, $key);
				
			}
			else if ($data instanceof FieldSpecInterface) {
				$spec = $data;
			}
			else {
		
				$err  = 'validation field specification must be an array ';
				$err .= 'of an object that implements -(Appfuel\Validate';
				$err .= '\FieldSpecInterface';
				throw new DomainException($err);
			}
			$fields   = $spec->getFields();
			$location = $spec->getLocation();
			if (! is_string($location) || empty($location)) {
				$err  = "in order for input validation to work the handler ";
				$err .= "needs to know where the field is located -(location)";
				$err .= " is missing or invalid: should be -(get,post,etc...)";
				throw new DomainException($err);
			}
			foreach ($fields as $field) {
				$rawValue = $this->get($location, $field, $notFound);
				if ($rawValue !== $notFound) {
					$raw[$field] = $rawValue;
				}
			}
			$handler->loadSpec($spec);
		}

		return $handler->isSatisfiedBy($raw);
	}

	/**
	 * @param	string	$key
	 * @param	mixed	$default
	 * @return	mixed
	 */
	public function getClean($key, $default = null)
	{
		return $this->getValidationHandler()
					->getClean($key, $default);
	}

	/**
	 * @return	array
	 */
	public function getAllClean()
	{
		return $this->getValidationHandler()
					->getAllClean();
	}

	/**
	 * @return	AppInput
	 */
	public function clearClean()
	{
		$this->getValidationHandler()
					->clearClean();
	
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getErrorString()
	{
		$stack = $this->getErrorStack();
		$msg = '';
		foreach ($stack as $error) {
			$msg .= $error->getMessage() . ' ';
		}

		return trim($msg);
	}

	/**
	 * @return	ErrorStackInterface
	 */
	public function getErrorStack()
	{
		return $this->getValidationHandler()
					->getErrorStack();
	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return $this->getValidationHandler()
					->isError();
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

	/**
	 * @param	string	$method
	 * @return	null
	 */
	protected function setMethod($method)
	{
		if (! is_string($method) || empty($method)) {
			$err = "input method must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->method = strtolower($method);
	}

	/**
	 * @param	array	$params
	 * @return	null
	 */
	protected function setParams(array $params)
	{
		foreach ($params as $type => $data) {
			if (! is_string($type) || empty($type)) {
				$err = "param type must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_array($data)) {
				$datatype = gettype($data);
				$err = "data for -($type) must be an array: -($datatype) given";
				throw new DomainException($err);
			}

			$type = strtolower($type);
		}

		$this->params = $params;
	}

	/**
	 * @param	ValidationHandlerInterface $hndlr
	 * @return	null
	 */
	protected function setValidationHandler(ValidationHandlerInterface $hndlr)
	{
		$this->handler = $hndlr;
	}
}
