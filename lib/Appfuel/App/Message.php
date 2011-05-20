<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\App;

use Appfuel\Data\Dictionary,
	Appfuel\Framework\App\MessageInterface,
	Appfuel\Framework\App\Route\RouteInterface,
	Appfuel\Framework\App\Request\RequestInterface;

/**
 * Message is a specialized disctionary used to pass throught the dispatch
 * system and into the action controllers. It allows the framework to inject
 * all the necessary objects into the action controllers and lets the 
 * controller pass back the document and any other meta data 
 */
class Message extends Dictionary implements MessageInterface
{
	/**
	 * Used by the front controller to build and configure an action controller
	 * @var	RouteInterface
	 */
	protected $route = null;

	/**
	 * Used by the front controller, action controller and possibly the controller
	 * builder to retrieve user input
	 *
	 * @var	RequestInterface
	 */
	protected $request = null;

	/**
	 * @return	RouteInterface
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @param	RouteInterface $route
	 * @return	Message
	 */
	public function setRoute(RouteInterface $route)
	{
		$this->route = $route;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isRoute()
	{
		return $this->route instanceof RouteInterface;
	}
}
