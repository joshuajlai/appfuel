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
namespace Appfuel\App\Action;


use Appfuel\Framework\Exception,
	Appfuel\Framework\App\Action\ActionBuilderInterface,
	Appfuel\Framework\App\Route\RouteInterface,
    Appfuel\Framework\App\MessageInterface,
    Appfuel\Framework\View\DocumentInterface,
	Appfuel\Framework\View\ViewManagerInterface;

/**
 *
 */
class ActionBuilder implements ActionBuilderInterface
{
	/**
	 * Used to determine the namespaces for objects required to build the
	 * controller
	 * @var RouteInterface
	 */
	protected $route = null;

	/**
	 * Text to decribe the error that has just occured
	 * @var string
	 */
	protected $error = null;

	/**
	 * flag used to indicate the build has an error
	 * @var bool
	 */
	protected $isError = false;

	/**
	 * @param	RouteInterface	$route
	 * @return	ActionBuilder
	 */
	public function __construct(RouteInterface $route)
	{
		$this->route = $route;
	}

	/**
	 * @return	RouteInterface
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return $this->isError;
	}

	/**
	 * @param	string	$text
	 * @return	ActionBuilder
	 */
	public function setError($text)
	{
		$this->error = $text;
		$this->isError = true;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return ActionBuilder
	 */
	public function clearError()
	{
		$this->error   = null;
		$this->isError = false;
		return $this;
	}

	/**
	 * @return	ControllerInterface
	 */
	public function createController()
	{
		$route = $this->getRoute();
		$ns    = $route->getActionNamespace();
		$class = "$ns\\Controller";
    
		try {
			$controller = new $class();
		} catch (Exception	$e) {
			$this->setError("Controller for ($class) could not be found");
			return false;
		}

		return $controller;
	}
}
