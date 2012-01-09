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

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\KernelRegistry,
    Appfuel\ClassLoader\StandardAutoLoader,
    Appfuel\ClassLoader\AutoLoaderInterface;

/**
 */
class MvcActionBuilder implements MvcActionBuilderInterface
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
	 * The uri is designed to hold the route key and optionally get parameters
	 * for an http get request. 
     * @var RequestUriInterface
     */
    protected $uri = null;

    /**
     * Holds the input from the user request
     * @var AppInputInterface
     */
    protected $input = null;

    /**
     * Acl role codes added to the context and used by the mvc action 
     * @var array
     */
    protected $aclCodes = array();

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
		$this->loader = $loader;
		return $this;
	}

    /**
     * @return  RequestUriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param   RequestUriInterface $uri
     * @return  MvcActionBuilder
     */
    public function setUri($uri)
    {
		if (is_string($uri)) {
			$uri = $this->createUri($uri);
		} 
		else if (! $uri instanceof RequestUriInterface) {
			$err  = 'uri must be a string or an objec the implements the ';
			$err .= 'Appfuel\Kernel\Mvc\RequestUriInterface';
			throw new InvalidArgumentException($err);
		}

        $this->uri = $uri;
        return $this;
    }

    /**
     * Use the uri string from the server super global $_SERVER['REQUEST_URI']
     * to create the uri and set it
     *
     * @return  MvcActionBuilder
     */
    public function useServerRequestUri()
    {
        $err  = 'php super global $_SERVER[\'REQUEST_URI\'] is not set';
        if (! isset($_SERVER['REQUEST_URI'])) {
            throw new LogicException($err);
        }

        $uri = $_SERVER['REQUEST_URI'];

		$err = 'request uri found in $_SERVER is not a string';
        if (! is_string($uri)) {
            throw new LogicException($err);
        }

        return $this->setUri($uri);
    }

    /**
     * @param   string  $uriString
     * @return  RequestUri
     */
    public function createUri($uriString)
    {
        return new RequestUri($uriString);
    }

    /**
     * @return  AppInputInterface
     */
    public function getInput()
    {  
        return $this->input;
    }

    /**
     * @param   AppInputInterface   $input
     * @return  MvcActionBuilder
     */
    public function setInput(AppInputInterface $input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * @param   string  $method 
     * @param   array   $params 
     * @return  ContextInput
     */
    public function createInput($method, array $params = array())
    {
        return new AppInput($method, $params);
    }

    /**
     * By default we will use the parameters from the uri object, 
     * super global $_POST for post, super global $_FILES for any files
     * super global $_COOKIE for any cookies and super global $_SERVER['argv']
     * for any commandline parameters.
     *
     * @return  ContextBuilder
     */
    public function buildInputFromDefaults($useUri = true)
    {
        $method = 'cli';
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        if (empty($method) || ! is_string($method)) {
            $err = 'request method is empty or not a string';
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
                    $err  = 'Default get params come from the request uri.';
                    $err .= 'we can not build the request uri without a uri ';
                    $err .= 'string. Since no uri was given we looked for ';
                    $err .= 'the uri string in $_SERVER[REQUEST_URI] and ';
                    $err .= 'found it was not set.Please manually set super ';
                    $err .= 'global or use builder to manually configure uri ';
                    $err .= 'with method setUri';
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
     * @param   string  $method
     * @param   array   $params
     * @return  MvcActionBuilder
     */
    public function defineInputAs($method, array $params = array())
    {
        return $this->setInput($this->createInput($method, $params));
    }

    /**
     * @param   string  $code
     * @return  MvcActionDispatcher
     */
    public function addAclCode($code)
    {
        if (! is_string($code) || empty($code)) {
            $err = 'role code must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        /* no duplicates */
        if (in_array($code, $this->aclCodes)) {
            return $this;
        }

        $this->aclCodes[] = $code;
        return $this;
    }

    /**
     * @param   array   $list
     * @return  MvcActionDispatcher
     */
    public function addAclCodes(array $list)
    {
        foreach ($list as $code) {
            $this->addAclCode($code);
        }

        return $this;
    }

	/**
	 * @return	array
	 */
	public function getAclCodes()
	{
		return $this->aclCodes;
	}
}
