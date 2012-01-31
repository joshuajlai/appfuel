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
	Appfuel\View\Html\FileViewTemplate,
	Appfuel\View\Html\HtmlPage,
	Appfuel\View\Html\HtmlPageInterface,
	Appfuel\View\Html\HtmlViewInterface,
    Appfuel\ClassLoader\StandardAutoLoader,
    Appfuel\ClassLoader\AutoLoaderInterface;

/**
 * Encapsulates the logic necessary to build an MvcContext.
 */
class MvcContextBuilder implements MvcContextBuilderInterface
{
	/**
	 * The autoloader is used to determine if the class exists. 
	 * @var AutoLoaderInterface
	 */
	protected $loader = null;

	/**
	 * The strategy is used to determine how to build the view, currently
	 * four strategies exist: html-page, html, ajax, console
	 * @var string
	 */
	protected $strategy = null;

	/**
	 * The route key points to the action namespace used to create the 
	 * mvc action controller, view, and route detail
	 * @var string
	 */
	protected $routeKey = null;

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
    public function __construct(AutoLoaderInterface $loader = null)
    {
        if (null === $loader) {
            $loader = new StandardAutoLoader(AF_LIB_PATH);
        }
        $this->setClassLoader($loader);
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
     * @param   string  $strategy 
     * @return  MvcActionBuilder
     */
    public function setStrategy($strategy)
    { 
       if (! is_string($strategy)) {
            $err = 'mvc action strategy must be a string';
            throw new InvalidArgumentException($err);
        }

        $this->strategy = $strategy;
        return $this;
    }

    /**
     * @return  string
     */
    public function getStrategy()
    {  
        return $this->strategy;
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
            is_string($_SERVER['QUERY_STRING'])) {
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
     * Will use the parameters from the uri object as the getParams for the
     * input and set the input method to 'get'
     *
     * @return  MvcActionBuilder
     */
    public function defineUriForInputSource()
    {
        return $this->defineInput('get', array(), true);
    }

    /**
     * @return  MvcActionBuilder
     */
    public function noInputRequired()
    {
		$method = 'get';
		$strategy = $this->getStrategy();
		if ('console' === $strategy) {
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
    public function addAclCodes(array $list)
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
		return $this->addAclCodes($list);
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
		if (! is_string($view) && 
			! (is_object($view) && is_callable(array($view, '__toString')))) {
			$err  = 'view must be a string or an object that ';
			$err .= 'implments __toString';
			throw new InvalidArgumentException($err);
		}

		$this->view = $view;
		return $this;
	}

	/**
	 * @param	string	$routeKey
	 * @param	string	$namespace
	 * @return	MvcRouteDetailInterface
	 */
	public function createRouteDetail($routeKey, $namespace)
	{
        $class    = "$namespace\\RouteDetail";
        $isDetail = $this->getClassLoader()
					   ->loadClass($class);

		if (! $isDetail) {
			$err  = "a concrete implementation of the route detail must be ";
			$err .= "available at -($class)";
			throw new RunTimeException($err);
		}
		$detail = new $class();

        if (! $detail instanceof MvcRouteDetailInterface) {
			$err  = 'route detail must implement -(Appfuel\Kernel\Mvc';
			$err .= '\RouteDetailInterface';
            throw new RunTimeException($err);
        }
		
		if ($routeKey !== $detail->getRouteKey()) {
			$err  = 'route detail created does not have the same route key ';
			$err .= 'as the one given to the MvcActionBuilder';
			throw new RunTimeException($err);
		}

		return $detail;
	}

	/**
	 * 
	 * @param	string	$namespace
	 * @param	string	$strategy
	 * @return	
	 */
	public function createView($namespace, $strategy)
	{
		switch ($strategy) {
			case 'html-page' :
				$view = $this->buildHtmlPage($namespace);
				break;
			case 'html':
				$view = $this->buildHtmlSection($namespace);
				break;
			case 'ajax':
				$view = $this->buildAjaxView($namespace);
				break;
			case 'console':
				$view = $this->buildConsoleView($namespace);

			default:
				$view = '';
		}
		
		return $view;
	}

	/**
	 * @param	string	$namespace
	 * @return	HtmlViewInterface
	 */
	public function createHtmlView($namespace)
	{
		$class  = "$namespace\HtmlView";
	    $isView = $this->getClassLoader()
					   ->loadClass($class);

		if (! $isView) {
			$err  = "a concrete implementation of HtmlViewInterface  must ";
			$err .= "be available at -($class)";
			throw new LogicException($err);
		}

		$htmlView = new $class();
		if (! ($htmlView instanceof HtmlViewInterface)) {
			$err  = 'html view does not implement Appfuel\View\Html\HtmlView';
			$err .= 'Interface';
			throw new LogicException($err);
		}

		return $htmlView;
	}

	/**
	 * @param	HtmlViewInterface $view
	 * @return	HtmlViewInterface | HtmlLayoutInterface
	 */
	public function createHtmlLayout(HtmlViewInterface $view)
	{
	    /*
         * if this view belongs to an html layout then create the layout
         * set the view in the layout and replace the view with the layout
         */
        $layoutClass = $view->getLayoutClass();
        if (! is_string($layoutClass) || empty($layoutClass)) {
			return $view;
		}
            
		$layout = new $layoutClass();
        if (! ($layout instanceof HtmlLayoutInterface)) {
			$err  = 'html layout does not implement Appfuel\View\Html\Html';
            $err .= 'LayoutInterface';
            throw new LogicException($err);
		}

        $layout->setView($view);
		return $layout;
	}

	/**
	 * @param	string|ViewTemplateInterface
	 * @param	string	$class
	 * @return	HtmlPageInterface
	 */
	public function createHtmlPage($view, $class = null)
	{
		if (null === $class) {
			return new HtmlPage($view);
		}

	    if (is_string($pageClass) && ! empty($pageClass)) {
            $page = new $pageClass($view);
            if (! ($page instanceof HtmlPageInterface)) {
                $err  = 'html doc does not implement Appfuel\View\Html\Html';
                $err .= 'PageInterface';
                throw new RunTimeException($err);
            }

			return $page;
        }
			
		$err = 'class must be a non empty string';
		throw new InvalidArgumentException($err);
	}

	/**
	 * @param	string	$file		path to the config data
	 * @param	HtmlPageInterface	
	 * @return	null
	 */
	public function configureHtmlPage($file, HtmlPageInterface $page)
	{
		$configurer = $this->createHtmlPageConfigurer();
		if (file_exists($filePath)) {
			return false;
		}
		
		$data = require $filePath;
		if (! is_array($config)) {
			$err = 'html page configuration must be an array';
			throw new RunTimeException($err);
		}
		$configurer->configure($data, $page);
		return true;
	}

	public function createHtmlPageConfigurer()
	{
		return new HtmlPageConfigurer();
	}

	/**
	 * @param	string	$namespace
	 * @return	HtmlPageInterface
	 */
	public function buildHtmlPage($namespace)
	{
		$htmlView = $this->createHtmlView($namespace);
		$pathFinder = $htmlView->getViewCompositor()
							   ->getPathFinder();

		/* used to create the html page class */
		$pageClass  = $htmlView->getHtmlPageClass();
		$pageConfigFile = $htmlView->getPageConfigurationFile();
 
		/*
		 * if the view has a layout it will be create here and the view
		 * will set into the layout and layout will be handed back. When
		 * no view exists just the layout is returned
		 */
		$htmlView = $this->createHtmlLayout($htmlView);

        $jsContent = null;
        if ($htmlView->isInlineJsTemplate()) {
			$jsContent = $htmlView->buildInlineJsTemplate();
        }

		$page = $this->createHtmlPage($htmlView, $pageClass); 

		if ($page->isJs() && is_string($jsContent) && !empty($jsContent)) {
			$page->addToInlineScript($jsContent, 'prepend');
		}

		$this->configureHtmlPage($pathFinder->getPath($pageConfigFile), $page);

		return $page;
	}

	/**	
	 * @return	MvcContextInterface
	 */
	public function build()
	{
		$strategy = $this->getStrategy();
		if (! is_string($strategy)) {
			$strategy = '';
		}

		$route = $this->getRouteKey();
		if (null === $route) {
			$uri   = $this->getUri();
			$route = $uri->getRouteKey();
		}

		$namespace = $this->getActionNamespace($route);
		if (false === $namespace) {
            $err .= "namespace -($namespace) was not found for route -($route)";
            throw new RunTimeException($err);
        }

		$detail = $this->createRouteDetail($route, $namespace);
		$view   = $this->createView($namespace, $strategy);
	}

    /**
     * @return  string | false when not mapped
     */
	protected function getActionNameSpace($routeKey)
	{
		return KernelRegistry::getActionNamespace($routeKey);
	}
}
