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

use InvalidArgumentException;

/**
 * Used to build action controllers
 */
class ActionFactory implements ActionFactoryInterface
{
	/**
	 * The php class name of an action controller
	 * @var string
	 */
	protected $ctrClass = 'ActionController';

	/**
	 * @param	string	$controllerClass
	 * @return	ActionFactory
	 */
	public function __construct($controllerClass = null)
	{
		if (null === $controllerClass) {
			$controllerClass = 'ActionController';
		}
		$this->setControllerClassName($controllerClass);
	}

	/**
	 * @return	string
	 */
	public function getControllerClassName()
	{
		return $this->crtClass;
	}

	/**
	 * @param	string	$className
	 * @return	ActionFactory
	 */
	public function setControllerClassName($className)
	{
		if (empty($className) || ! is_string($className)) {
			throw new InvalidArgumentException(
				"Controller class name  must be a non empty string"
			);
		}

		$this->ctrClass = $className;
		return $this;
	}

	/**
	 * @param	string	$namespace
	 * @return	ActionControllerInterface
	 */
	public function createActionController($namespace)
	{
		if (! is_string($namespace)) {
			throw new InvalidArgumentException(
				"Controller namespace must be a string"
			);
		}
		$class = "$namespace\{$this->getControllerClassName()}";
		return new $class();
	}
}
