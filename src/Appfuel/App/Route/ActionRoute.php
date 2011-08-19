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
namespace Appfuel\App\Route;

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\Route\RouteInterface;

/**
 * Value object used to hold routing information
 */
class ActionRoute implements RouteInterface
{
    /**
     * Method used for this request POST | GET
     * @var string
     */
    protected $routeString = null;

	/**
	 * Access policy, public or private
	 * @var string
	 */
	protected $access = null;

	/**
	 * Type of document returned. Html, json, cli, xml etc..
	 * @var string
	 */
	protected $responseType = null;

	/**
	 * Namespace for all module controllerss
	 * @var string
	 */
	protected $rootActionNamespace = null;
	
	/**
	 * Appfuel\App\Action\Error
	 * Namespace for all sub modules
	 * @var string
	 */
	protected $moduleNamespace = null;

	/**
	 * Namespace for all action namespace
	 * @var string
	 */
	protected $subModuleNamespace = null;

    /**
     * Namespace that holds the action controllere
     * @var string
     */
    protected $actionNamespace = null;

    /**
	 * Value object used to translate a route string into the name of the
	 * controller class. This is created during startup and used in dispatching
	 *
	 * @param	string  $routeString 
	 * @param	string	$namespace
	 * @param	string	$access
	 * @param	string	$responseType
     * @return	Route
     */
    public function __construct($route, $actionNs, $access, $responseType)
    {
		$this->setRouteString($route)
			 ->setAccessPolicy($access)
			 ->setResponseType($responseType)
			 ->loadNamespace($actionNs);
    }

    /**
     * @return string
     */
    public function getRouteString()
    {
		return $this->routeString;
    }

	/**
	 * @param	string	$route
	 * @return	ActionRoute
	 */
	protected function setRouteString($route)
	{
		if (! $this->isValidString($route)) {
			throw new Exception("route string must be a non empty string");
		}
		$this->routeString = $route;
		
		return $this;
	}

    /**
     * @return string
     */
    public function getAccessPolicy()
    {
		return $this->access;
    }

	/**
	 * @param	string	$policy
	 * @return	ActionRoute
	 */
	protected function setAccessPolicy($policy)
	{
		if (! $this->isValidString($policy)) {
			throw new Exception("access policy must be a non empty string");
		}
		$this->access = $policy;
		
		return $this;
	}

    /**
     * @return string
     */
    public function getResponseType()
    {
		return $this->responseType;
    }

	/**
	 * @param	string	$policy
	 * @return	ActionRoute
	 */
	protected function setResponseType($type)
	{
		if (! $this->isValidString($type)) {
			throw new Exception("response type must be a non empty string");
		}
		$this->responseType = $type;
		
		return $this;
	}
	
	/**
	 * Setter for action, subModule, module and global controller namespaces
	 *
	 * @param	string	$actionNamespace
	 * @return	ActionRoute
	 */
	protected function loadNamespace($actionNamespace)
	{
		$errText = "Invalid namespace give :";
		if (! $this->isValidString($actionNamespace)) {
			throw new Exception("$errText must be a non empty string");
		}
		$this->actionNamespace = $actionNamespace;
	
		$pos       = strrpos($actionNamespace, '\\');
		$subModule = substr($actionNamespace, 0, $pos);
		if (! $this->isValidString($subModule)) {
			throw new Exception(
				"$errText sub module namespace must be a non empty string");
		}
		$this->subModuleNamespace = $subModule;

		$pos    = strrpos($subModule, '\\');
		$module = substr($subModule, 0, $pos);
		if (! $this->isValidString($module)) {
			throw new Exception(
				"$errText module namespace must be a non empty string");
		}
		$this->moduleNamespace = $module;

		$pos  = strrpos($module, '\\');
		$root = substr($module, 0, $pos);
		if (! $this->isValidString($root)) {
			throw new Exception(
				"$errText root action namespace must be a non empty string");
		}
		$this->rootActionNamespace = $root;
		return $this;
	}

    /**
     * @return string
     */
    public function getActionNamespace()
    {
		return $this->actionNamespace;
    }

    /**
     * @return string
     */
    public function getSubModuleNamespace()
    {
		return $this->subModuleNamespace;
    }

    /**
     * @return string
     */
    public function getModuleNamespace()
    {
		return $this->moduleNamespace;
    }

    /**
     * @return string
     */
    public function getRootActionNamespace()
    {
		return $this->rootActionNamespace;
    }

	/**
	 * @param	string $str
	 * @return	bool
	 */
	protected function isValidString($str)
	{
		if (is_string($str) && ! empty($str)) {
			return true;
		}

		return false;
	}
}
