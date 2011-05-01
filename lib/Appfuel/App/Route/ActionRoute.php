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

use Appfuel\Framework\RouteInterface,
	Appfuel\Framework\Exception;

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
     * Uri Object that holds request info
     * @var string
     */
    protected $namespace = NULL;

	/**
	 * Access policy, public or private
	 * @var string
	 */
	protected $access = null;

	/**
	 * Type of document returned. Html, json, cli, xml etc..
	 * @var string
	 */
	protected $returnType = null;


    /**
	 * Value object used to translate a route string into the name of the
	 * controller class. This is created during startup and used in dispatching
	 *
	 * @param	string  $routeString 
	 * @param	string	$controllerClass
     * @return	Route
     */
    public function __construct($route, $namespace, $access, $returnType)
    {
		$err = "Route constructor failed: a non empty string is required for";
		if (! $this->isValidString($route)) {
			throw new Exception("$err route string");
		}
		$this->route = $route;

		if (! $this->isValidString($namespace)) {
			throw new Exception("$err action controller namespace");
		}
		$this->namespace = $namespace;

		if (! $this->isValidString($access)) {
			throw new Exception("$err access policy");
		}
		$this->access = $access;

		if (! $this->isValidString($returnType)) {
			throw new Exception("$err return type");
		}
		$this->returnType = $returnType;
    }

    /**
     * @return string
     */
    public function getRouteString()
    {
		return $this->route;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
		return $this->namespace;
    }

    /**
     * @return string
     */
    public function getAccessPolicy()
    {
		return $this->access;
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
		return $this->returnType;
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
