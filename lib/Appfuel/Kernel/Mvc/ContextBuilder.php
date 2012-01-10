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
namespace Appfuel\Kernel\Mvc;

use RunTimeException,
	InvalidArgumentException
	Appfuel\Kernel\KernelRegistry,
    Appfuel\ClassLoader\StandardAutoLoader,
    Appfuel\ClassLoader\AutoLoaderInterface;

/**
 * The context build holds all the logic for create uri strings, requests,
 * fetching the operational route, using all these objects to create the 
 * application context
 */
class ContextBuilder implements ContextBuilderInterface
{
	/**
	 * The class name used to create the action controller class found in
	 * the namespace the route maps too
	 * @var	string
	 */
	static protected $actionClassName = 'ActionController';

	/**
	 * We reuse the autoloader class to parse the namespace into a dir path
	 * to find the mvc action, view, and route detail.
	 * @var AutoLoaderInterface
	 */
	protected $loader = null;

    /**
     * Request Parameters. We parse the uri string and create our own parameters
     * instead of using super global $_GET. This is due to the way we use the 
     * url for holding mvc data plus key value pairs
     * @var array
     */
    protected $uri = null;

    /**
     * Method used for this request POST | GET
     * @var string
     */
    protected $input = null;

    /**
     * @param   string  $controllerClass
     * @return  MvcActionBuilder
     */
    public function __construct(AutoLoaderInterface $loader = null)
    {
        /*
         * Note that we use the load class from the lib directory. This 
         * constant is set during intialization. I will refactor next to a 
         * a path finder. (on a deadline right now) --rsb
         */
        if (null === $loader) {
            $loader = new StandardAutoLoader(AF_LIB_PATH);
        }
        $this->setClassLoader($loader);
    }

	/**
	 * @param	string	$name
	 * @return	null
	 */
	static public function setActionClassName($name)
	{
		if (! is_string($name) || ! ($name = trim($name))) {
			$err = 'class name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		self::$actionClassName = $name;
	}

	/**
	 * @return	string
	 */
	static public function getActionClassName()
	{
		return self::$actionClassName;
	}

	/**
	 * @return	AutoLoaderInterface
	 */
	public function getClassLoader()
	{
		return $this->loader;
	}

	/**
	 * @param	AutoLoaderInterface $loader
	 * @return	MvcActionBuilder
	 */
	public function setClassLoader(AutoLoaderInterface $loader)
	{
		$this->loader $loader;
		return $this;
	}

	/**
	 * @return	RequestUriInterface
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * @param	RequestUriInterface	$uri
	 * @return	ContextBuilder
	 */
	public function setUri(RequestUriInterface $uri)
	{
		$this->uri = $uri;
		return $this;
	}
	
	/**
	 * @param	string	$uriString
	 * @return	RequestUri
	 */
	public function createUri($uriString)
	{
		return new RequestUri($uriString);	
	}

	/**
	 * Use the uri string from the server super global $_SERVER['REQUEST_URI']
	 * to create the uri and set it
	 *
	 * @return	ContextBuilder
	 */
	public function useServerRequestUri()
	{
		$err  = 'ConextBuilder failed: php super global ';
		$err .= '$_SERVER[\'REQUEST_URI\']';
		if (! isset($_SERVER['REQUEST_URI'])) {
			throw new RunTimeException("$err is not set");
		}

		$uri = $_SERVER['REQUEST_URI'];
		if (! is_string($uri)) {
			throw new RunTimeException("$err must be a valid string");
		}
		return $this->setUri($this->createUri($uri));
	}

	/**
	 * @param	string	$uri
	 * @return	ContextBuilder
	 */
	public function useUriString($uri)
	{
		if (! is_string($uri)) {
			$err  = 'ConextBuilder failed: uri string given must be ';
			$err .= 'a valid string';
			throw new InvalidArgumentException($err);
		}

		return $this->setUri($this->createUri($uri));
	}

	/**
	 * @return	AppInputInterface
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * @param	AppInputInterface	$input
	 * @return	ContextBuilder
	 */
	public function setInput(AppInputInterface $input)
	{
		$this->input = $input;
		return $this;
	}

	/**
	 * By default we will use the parameters from the uri object, 
	 * super global $_POST for post, super global $_FILES for any files
	 * super global $_COOKIE for any cookies and super global $_SERVER['argv']
	 * for any commandline parameters.
	 *
	 * @return	ContextBuilder
	 */
	public function buildInputFromDefaults($useUri = true)
	{
		$method = 'cli';
		$err    = 'ContextBuilder failed:';
		if (isset($_SERVER['REQUEST_METHOD'])) {
			$method = $_SERVER['REQUEST_METHOD'];
		}
		
		if (empty($method) || ! is_string($method)) {
			$err .= 'request method is empty or not a string';
			throw new RunTimeException($err);
		}

		if ($useUri) {
			$uri = $this->getUri();
			if (! $uri instanceof RequestUriInterface) {
				if (isset($_SERVER['REQUEST_URI'])) {
					$uri = $this->useServerRequestUri()
								->getUri();
				}
				else {
					$err .= 'Default get params come from the request uri.';
					$err .= 'we can not build the request uri without a uri ';
					$err .= 'string. Since no uri was given we looked for ';
				    $err .= 'the uri string in $_SERVER[REQUEST_URI] and ';
					$err .= 'found it was not set.Please manually set super ';
					$err .= 'global or use builder to manually configure uri ';
					$err .= 'with method useUriString';
					throw new RunTimeException($err);
				}
			}
			$getParams = $uri->getParams();
		} else {
			$getParams = $_GET;
		}

		$params = array();
		$params['get']    = $getParams;
		$params['post']   = $_POST;
		$params['files']  = $_FILES;
		$params['cookie'] = $_COOKIE;
		$params['argv']   = array();
		if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
			$params['argv'] = $_SERVER['argv'];
		}

		return $this->setInput($this->createInput($method, $params));
	}

	/**
     * This will allow you to manual define the input used in the context 
     * that will be dispatched. If a uri has also been defined then its 
     * parameters will be used as the inputi's get parameters by default. If
     * you already have get parameters then the uri params will be merged
     *
     * @param   string  $method  get|post or cli
     * @param   array   $params  input parameters
     * @param   bool    $useUri  flag used to determine if the get parameters
     *                           will be obtained from the uri
     * @return  MvcActionBuilder
	 */
	public function defineInput($method, array $params, $useUri = true)
	{
        if (true === $useUri) {
            $uri = $this->getUri();
            if (! ($uri instanceof RequestUriInterface)) {
                $err  = "defineInput failed: uri is required for its get ";
                $err .= "params, but has not been set";
                throw new RunTimeException($err);
            }
            $getParams = $uri->getParams();
            if (array_key_exists('get', $params)) {
                $getParams = array_merge($params['get'], $getParams);
            }
            $params['get'] = $getParams;
        }

        return $this->setInput($this->createInput($method, $params));
	}

	/**
	 * @param	string	$method	
	 * @param	array	$params	
	 * @return	ContextInput
	 */
	public function createInput($method, array $params = array())
	{
		return new AppInput($method, $params);
	}

	/**
	 * @return	AppContext
	 */
	public function build($routeKey, $strategy)
	{
		$err = 'context build failed: ';
		$route  = $this->getRoute();
		if (null === $route) {
			$err .= 'route must be set before build';
			throw new RunTimeException($err);
		}

		$strategy = $this->getStrategy();
		if (null === $strategy) {
			$err .= 'strategy must be set before build';
			throw new RunTimeException($err);
		}


		$input = $this->getInput();
		if (! $input instanceof AppInputInterface) {
			$uri = $this->getUri();
			if (! $uri instanceof RequestUriInterface) {
				$uri = $this->useServerRequestUri();
			}

			$input = $this->buildInputFromDefaults()
						  ->getInput();
		}

		$this->clear();

		/* clear out build state */
		return new MvcContext($route, $strategy, $input);
	}

	public function buildView($namespace)
	{
		
	}

	/**
	 * @return	null
	 */
	protected function clear()
	{
		$this->route = null;
		$this->strategy = null;
		$this->input = null;
		$this->uri   = null;
	}
}
