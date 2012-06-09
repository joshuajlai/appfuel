<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

use DomainException,
	InvalidArgumentException;

/**
 * Maps the input method (http[get,post,put,delete] or cli)
 * to a concrete MvcAction.
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
	 * @param	string	$method 
	 * @return	string | false
	 */
	public function findAction($method = null)
	{
		if ($this->isMapEmpty()) {
			$name = $this->getName();
		}
		else {
			$name = $this->getNameInMap($method);
		}

		return $name;
	}

	/**
	 * @return	string
	 */
	public function getName()
	{	
		return $this->name;
	}

	/**
	 * @param	string	$name
	 * @return	RouteAction
	 */
	public function setName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "action name must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->name = $name;
		return $this;
	}

	/**
	 * @return	RouteAction
	 */
	public function clearName()
	{
		$this->name = null;
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
				$err = "action map method must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_string($action) || empty($action)) {
				$err = "action map action must be a non empty string";
				throw new DomainException($err);
			}
		}

		$this->map = $map;
		return $this;
	}

	/**
	 * @return	RouteAction
	 */
	public function clearMap()
	{
		$this->map = array();
		return $this;
	}
}
