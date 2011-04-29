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
namespace Appfuel\App;

use Appfuel\Framework\RouteInterface,
	Appfuel\Framework\Exception;

/**
 * Value object used to hold routing information
 */
class Route implements RouteInterface
{
    /**
     * Uri Object that holds request info
     * @var string
     */
    protected $controllerClass = NULL;

    /**
     * Method used for this request POST | GET
     * @var string
     */
    protected $routeString = null;


    /**
	 * Value object used to translate a route string into the name of the
	 * controller class. This is created during startup and used in dispatching
	 *
	 * @param	string  $routeString 
	 * @param	string	$controllerClass
     * @return	Route
     */
    public function __construct($routeString, $controllerClass)
    {
		if (! is_string($routeString) || empty($routeString)) {
			throw new Exception("Route string must be a non empty string");
		}
		$this->routeString = $routeString;

		if (! is_string($controllerClass) || empty($controllerClass)) {
			throw new Exception("Invalid controller class given");
		}
		$this->controllerClass = $controllerClass;
    }

    /**
     * @return string
     */
    public function getRouteString()
    {
		return $this->routeString;
    }

    /**
     * @return string
     */
    public function getControllerClass()
    {
		return $this->controllerClass;
    }
}
