<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use DomainException,
	InvalidArgumentException;

/**
 */
class RouteAction implements RouteActionInterface
{
	/**
	 * Name of the mvc action class. This is not the qual
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var array
	 */
	protected $map = array();

	/**
	 * Should fail if no name is found in the map
	 * @var bool
	 */
	protected $isStrict = true;

	/**
	 * @return	RouteAction
	 */
	public function enableStrict()
	{
		$this->isStrict = true;
		return $this;
	}

	/**
	 * @return	RouteAction
	 */
	public function disableStrict()
	{
		$this->isStrict = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isStrict()
	{
		return $this->isStrict;
	}

	/**
	 * @param	string	$method 
	 * @return	string | false
	 */
	public function findActionName($method = null)
	{
		if ($this->isMapEmpty()) {
			$name = $this->getActionName();
		}
		else {
			$name = $this->getNameInMap($method);
		}

		return $name;
	}

	/**
	 * @return	string
	 */
	public function getActionName()
	{	
		return $this->name;
	}

	/**
	 * @param	string	$name
	 * @return	RouteAction
	 */
	public function setActionName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "action name must be a non empty string";
			throw new InvalidArgumentException($name);
		}

		$this->name = $name;
		return $this;
	}

	/**
	 * @param	string	$method
	 * @return	string | false
	 */
	public function getNameInMap($method)
	{
		if (! is_string($method) || ! isset($this->map[$method])) {
			return false;
		}

		return $this->map[$method];
	}

	/**
	 * @return	bool
	 */
	public function isMapEmpty()
	{
		return empty($this->map);
	}

	/**
	 * @return	array
	 */
	public function getMap()
	{
		return $this->map;
	}

	/**
	 * @param	array	$map
	 * @return	RouteAction
	 */
	public function setMap(array $map)
	{
		foreach ($map as $method => $action) {
			if (! is_string($method) || empty($method)) {
				$err = "action map method must be non empty string";
				throw new DomainException($err);
			}

			if (! is_string($action) || empty($action)) {
				$err = "action map action must be non empty string";
				throw new DomainException($err);
			}
		}

		$this->map = $map;
		return $this;
	}

}
