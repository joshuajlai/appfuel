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
	Appfuel\Framework\App\Context\UriInterface,
	Appfuel\Framework\App\Context\RequestInterface;

/**
 * Common logic to handle requests given to the application from any type.
 * Different types include web requests, cli request and api requests.
 * The request provides a common interface that all application types can
 * follow.
 */
class Request implements RequestInterface
{
    /**
     * Uri Object that holds request info
     * @var string
     */
    protected $uri = NULL;

    /**
     * Request Parameters. We parse the uri string and create our own parameters
     * instead of using super global $_GET. This is due to the way we use the 
     * url for holding mvc data plus key value pairs
     * @var array
     */
    protected $params = array();

    /**
     * Method used for this request POST | GET
     * @var string
     */
    protected $method = null;

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
    public function __construct(UriInterface $uri)
    {
        $this->uri = $uri;
		$argv = array();
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = strtolower($_SERVER['REQUEST_METHOD']);
        }
		else if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
			$method = 'cli';
            $argv = $_SERVER['argv'];
		}
		else {
			throw new Exception("Request could not be create method unkown");
		}

        $this->method = $method;

        $params = array(
			'get'	 => $uri->getParams(),
            'post'   => $_POST,
            'files'  => $_FILES,
            'cookie' => $_COOKIE,
            'argv'   => $argv
        );

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
	 * @return string
	 */
	public function getUriString()
	{
		return $this->getUri()
					->getUriString();
	}

	/**
	 * @return	string
	 */
	public function getRouteString()
	{
		return $this->getUri()
					->getPath();
	}

	/**
	 * @return	string
	 */
	public function getParamString()
	{
		return $this->getUri()
					->getParamString();
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
	public function get($key, $type, $default = null)
	{
        /*
         * These values must be compatible with arrays
         */
        if (! is_scalar($key) || ! is_scalar($type)) {
            return $default;
        }

        if (! isset($this->params[$type]) || ! is_array($this->params[$type])) {
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
	 * @param	array	$keys	list of parameter labels to collect
	 * @param	array	$type	which request type get, post, argv etc..
	 * @param	array	$returnArray 
	 * @return	Dictionary
	 */
	public function collect(array $keys, $type, $returnArray = false) 
	{
		$result = array();
		$notFound = '__AF_KEY_NOT_FOUND__';
		foreach ($keys as $key) {
			$value = $this->get($key, $type, $notFound);

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
    public function getAll($type)
    {
        $type = strtolower($type);
        if (! array_key_exists($type, $this->params)) {
            return array();
        }

        return $this->params[$type];
    }

	/**
	 * @return Uri
	 */
	protected function getUri()
	{
		return $this->uri;
	}
}
