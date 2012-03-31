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
     * @param   string  $uriString
     * @return  RequestUri
     */
    public function createUri($uriString)
    {
        return new RequestUri($uriString);
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
