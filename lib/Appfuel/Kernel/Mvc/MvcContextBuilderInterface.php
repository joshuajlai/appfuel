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

use Appfuel\Kernel\Mvc\MvcRouteManager,
	Appfuel\View\Html\FileViewTemplate,
	Appfuel\View\Html\HtmlViewInterface,
    Appfuel\ClassLoader\StandardAutoLoader,
    Appfuel\ClassLoader\AutoLoaderInterface;

/**
 * Encapsulates the logic necessary to build an MvcContext.
 */
interface MvcContextBuilderInterface
{
	/**
	 * @return	AutoLoaderInterface
	 */
	public function getViewBuilder();

	/**
	 * @param	AutoLoaderInterface $loader
	 * @return	MvcActionBuilder
	 */
	public function setViewBuilder(MvcViewBuilderInterface $builder);

    /**
     * @param   string $key
     * @return  MvcActionBuilder
     */
    public function setRouteKey($key);

    /**
     * @return  string
     */
    public function getRouteKey();

	/**
	 * @return	MvcRouteDetailInterface
	 */
	public function getRouteDetail();

	/**
	 * @return	bool
	 */
	public function isRoute();

	/**
	 * @return	MvcContextBuilder
	 */
	public function loadRoute();

	/**
	 * @return	bool
	 */
	public function isUri();

    /**
     * @return  RequestUriInterface
     */
    public function getUri();

    /**
     * @param   RequestUriInterface $uri
     * @return  MvcActionBuilder
     */
    public function setUri($uri);

    /**
     * Use the uri string from the server super global $_SERVER['REQUEST_URI']
     * to create the uri and set it
     *
     * @return  MvcActionBuilder
     */
    public function useServerRequestUri();

    /**
     * @param   string  $uriString
     * @return  RequestUri
     */
    public function createUri($uriString);

	/**
	 * @return	bool
	 */
	public function isInput();

    /**
     * @return  AppInputInterface
     */
    public function getInput();

    /**
     * @param   AppInputInterface   $input
     * @return  MvcActionBuilder
     */
    public function setInput(AppInputInterface $input);

    /**
     * @param   string  $method 
     * @param   array   $params 
     * @return  ContextInput
     */
    public function createInput($method, array $params = array());

    /**
     * By default we will use the parameters from the uri object, 
     * super global $_POST for post, super global $_FILES for any files
     * super global $_COOKIE for any cookies and super global $_SERVER['argv']
     * for any commandline parameters.
     *
     * @return  ContextBuilder
     */
    public function defineInputFromDefaults($useUri = true);

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
    public function defineInput($method, array $params, $useUri = true);

    /**
     * Will use the parameters from the uri object as the getParams for the
     * input and set the input method to 'get'
     *
     * @return  MvcActionBuilder
     */
    public function defineUriForInputSource();

    /**
     * @return  MvcActionBuilder
     */
    public function noInputRequired();

    /**
     * @param   string  $code
     * @return  MvcActionBuilder
     */
    public function addAclCode($code);

    /**
     * @param   array   $list
     * @return  MvcActionBuilder
     */
    public function loadAclCodes(array $list);

	/**
	 * @param	array	$list
	 * @return	MvcActionBuilder
	 */
	public function setAclCodes(array $list);

	/**
	 * @return	array
	 */
	public function getAclCodes();

	/**
	 * @return	mixed
	 */
	public function getView();

	/**
	 * @param	mixed	$view
	 * @return	MvcContextBuilder
	 */
	public function setView($view);

	/**	
	 * @return	MvcContextInterface
	 */
	public function build();

	/**
	 * @return	null
	 */
	public function clear();
}
