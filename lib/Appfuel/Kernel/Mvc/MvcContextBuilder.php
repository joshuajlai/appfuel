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
	Appfuel\Kernel\Mvc\MvcRouteManager,
	Appfuel\View\Html\FileViewTemplate,
	Appfuel\View\Html\HtmlViewInterface,
    Appfuel\ClassLoader\StandardAutoLoader,
    Appfuel\ClassLoader\AutoLoaderInterface;

/**
 * Encapsulates the logic necessary to build an MvcContext.
 */
class MvcContextBuilder implements MvcContextBuilderInterface
{
	/**
	 * Used to build and configure html pages
	 *
	 * @var MvcViewBuilderInterface
	 */
	protected $viewBuilder = null;

	/**
	 * The route key points to the action namespace used to create the 
	 * mvc action controller, view, and route detail
	 * @var string
	 */
	protected $routeKey = null;

	/**
	 * Holds information for context building specific to that route
	 * @var string
	 */
	protected $routeDetail = null;

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
	 * Any string or object that supports __toString
	 * @var mixed
	 */
	protected $view = null;

    /**
     * @param   string  $controllerClass
     * @return  MvcActionBuilder
     */
    public function __construct(MvcViewBuilderInterface $builder = null)
    {
        if (null === $builder) {
            $builder = new MvcViewBuilder();
        }
        $this->setViewBuilder($builder);

    }

	/**
	 * @return	AutoLoaderInterface
	 */
	public function getViewBuilder()
	{
		return $this->viewBuilder;
	}

	/**
	 * @param	AutoLoaderInterface $loader
	 * @return	MvcActionBuilder
	 */
	public function setViewBuilder(MvcViewBuilderInterface $builder)
	{
		$this->viewBuilder = $builder;
		return $this;
	}

    /**
     * @param   string $key
     * @return  MvcActionBuilder
     */
    public function setRouteKey($key)
    {
        if (! is_string($key)) {
            $err = 'route key must be a string';
            throw new InvalidArgumentException($err);
        }

        $this->routeKey = $key;

		$detail = MvcRouteManager::getRouteDetail($key);
		if (! $detail) {
			$err = "could not resolve route detail for -($key)";
			throw new RunTimeException($err);
		}
		$this->routeDetail = $detail;
        return $this;
    }

    /**
     * @return  string
     */
    public function getRouteKey()
    {  
        return $this->routeKey;
    }

	/**
	 * @return	MvcRouteDetailInterface
	 */
	public function getRouteDetail()
	{
		return $this->routeDetail;
	}

	/**
	 * @return	bool
	 */
	public function isRoute()
	{
		return $this->routeDetail instanceof MvcRouteDetailInterface;
	}

	/**
	 * @return	MvcContextBuilder
	 */
	public function loadRoute()
	{
		$uri = $this->getUri();
		if (! $uri instanceof RequestUriInterface) {
			$err = 'can not load a route with out setting the uri';
			throw new RunTimeException($err);
		}

		$key = $uri->getRouteKey();
		$this->setRouteKey($key);
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isUri()
	{
		return $this->uri instanceof RequestUriInterface;
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
        if (isset($_SERVER['QUERY_STRING']) &&
            is_string($_SERVER['QUERY_STRING']) &&
            ! empty($_SERVER['QUERY_STRING'])) {
            $uri = '?' . $_SERVER['QUERY_STRING'];
        }
        else if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        else {
            $err  = 'ConextBuilder failed: php super global ';
            $err .= '$_SERVER[\'REQUEST_URI\']';
            throw new RunTimeException("$err is not set");
        }

        return $this->setUri($this->createUri($uri));
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
	 * @return	bool
	 */
	public function isInput()
	{
		return $this->input instanceof AppInputInterface;
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
    public function defineInputFromDefaults($useUri = true)
    {
        $method = 'cli';
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

		/*
		 * This should not happen but if it does the input should not fail
		 */
        if (empty($method) || ! is_string($method)) {
			$method = 'cli';
        }

        if ($useUri) {
            $uri = $this->getUri();
            if (! $uri instanceof RequestUriInterface) {
				$uri = $this->useServerRequestUri()
                            ->getUri();
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
				$uri = $this->useServerRequestUri()
							->getUri();
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
     * Will use the parameters from the uri object as the getParams for the
     * input and set the input method to 'get'
     *
     * @return  MvcActionBuilder
     */
    public function defineUriForInputSource()
    {
        $method = 'get';
        if (PHP_SAPI === 'cli') {
            $method = 'cli';
        }
        return $this->defineInput($method, array(), true);
    }

    /**
     * @return  MvcActionBuilder
     */
    public function noInputRequired()
    {
		$method = 'get';
		if (PHP_SAPI === 'cli') {
			$method = 'cli';
		}

        return $this->defineInput($method, array(), false);
    }

    /**
     * @param   string  $code
     * @return  MvcActionBuilder
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
     * @return  MvcActionBuilder
     */
    public function loadAclCodes(array $list)
    {
        foreach ($list as $code) {
            $this->addAclCode($code);
        }

        return $this;
    }

	/**
	 * @param	array	$list
	 * @return	MvcActionBuilder
	 */
	public function setAclCodes(array $list)
	{
		$this->aclCodes = array();
		return $this->loadAclCodes($list);
	}

	/**
	 * @return	array
	 */
	public function getAclCodes()
	{
		return $this->aclCodes;
	}

	/**
	 * @return	mixed
	 */
	public function getView()
	{
		return $this->view;
	}

	/**
	 * @param	mixed	$view
	 * @return	MvcContextBuilder
	 */
	public function setView($view)
	{
		$this->view = $view;
		return $this;
	}

	/**	
	 * @return	MvcContextInterface
	 */
	public function build()
	{
		if (! $this->isRoute()) {
			$this->loadRoute();
		}

		$key = $this->getRouteKey();
		$detail = $this->getRouteDetail();
		if (! $detail instanceof MvcRouteDetailInterface) {
			$err  = "could not build context unable to obtain routing info ";
			$err .= "for -($key)";
			throw new RunTimeException($err);	
		} 

		$input = $this->getInput();
		if (! $this->isInput()) {
			$err = "input must set before building context: -(route is $key)";
			throw new RunTimeException($err);
		}
		
		$view = $this->getView();
		if (null === $view) {
			$viewBuilder = $this->getViewBuilder();
			$view = $viewBuilder->buildView($detail);
		}

		$this->clear();
		return new MvcContext($key, $detail, $input, $view);
	}

	/**
	 * @return	null
	 */
	public function clear()
	{
		$this->routeKey = null;
		$this->routeDetail = null;
		$this->uri = null;
		$this->input = null;
		$this->aclCodes = array();
		$this->view = null;
		return $this;
	}
}
