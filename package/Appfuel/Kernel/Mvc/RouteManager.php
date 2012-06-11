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

use Exception,
	DomainException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\ClassLoader\NamespaceParser;

/**
 */
class RouteManager
{
	/**
	 * List of routes stored as an associative array route key => detail
	 * @var array
	 */
	static protected $routes = array();

	/**
	 * Flag used to determine if regexes will be used to find the route
	 * @var bool
	 */
	static protected $isPatternMatching = true;

	/**
 	 * Associative array of regex to route key. Used by the router where
	 * any successful match will point to the route key its associated with
	 * @var array
	 */ 
	static protected $patternMap = array();

	/**
	 * @return	bool
	 */
	static public function isPatternMatching()
	{
		return self::$isPatternMatching;
	}

	/**
	 * @return	null
	 */
	static public function enablePatternMatching()
	{
		self::$isPatternMatching = true;
	}

	/**
	 * @return	null
	 */
	static public function disablePatternMatching()
	{
		self::$isPatternMatching = false;
	}

	/**
	 * @return	null
	 */
	static public function clearPatternMap()
	{
		self::$patternMap = array();
	}

	/**
	 * @return	array
	 */
	static public function getPatternMap()
	{
		return self::$patternMap;
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setPatternMap(array $map)
	{
		self::clearPatternMap();
		self::loadPatternMap($map);
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function loadPatternMap(array $map)
	{
		foreach ($map as $pattern => $key) {
			self::addPattern($pattern, $key);
		}
	}

	/**
	 * @param	string	$key
	 * @param	string	$pattern
	 * @return	null
	 */
	static public function addPattern($pattern, $key)
	{	
		if (! is_string($key)) {
			$err = "route key must be a string";
			throw new InvalidArgumentException($err);
		}

		if (! is_string($pattern)) {
			$err = "regex route pattern must be a string";
			throw new InvalidArgumentException($err);
		}

		self::$patternMap[$pattern] = $key;
	}

	/**
	 * @param	array | MvcRouteDetailInterface
	 * @return	null
	 */
	static public function addRouteDetail($detail)
	{
		if (is_array($detail)) {
			$detail = self::createRouteDetail($detail);
		}
		else if (! $detail instanceof RouteInterface) {
			$interface = "Appfuel\Kernel\Mvc\MvcRouteDetailInterface";
			$err  = "route detail must be an array (detail spec) or an oject ";
			$err .= "that implements -($interface)";
			throw new DomainException($err);
		}

		$key = $detail->getKey();
		self::$routes[$key] = $detail;

		if (! self::isPatternMatching() || ! $detail->isPattern()) {
			return;
		}

		self::addPattern($detail->getPattern(), $key);
	}

	/**
	 * @param	array	$data
	 * @return	MvcRouteDetailInterface
	 */
	static public function createRotueDetail(array $data)
	{
		if (! isset($data['route-class'])) {
			return new MvcRouteDetail($data);
		}
			
		$class = $data['route-class'];
		if (! is_string($class) || empty($class)) {
			$err  = "class declared by -(route-detail-class) must be ";
			$err .= "non empty string";
			throw new DomainException($err);
		}
		
		try {
			$detail = new $class($data);
		}
		catch (Exception $e) {
			$err = "could not instantiate route detail -($class)";
			throw new DomainException($err);
		}
	
		if (! $detail instanceof RouteInterface) {
			$err  = "route detail -($class) does not implement -(Appfuel";
			$err .= "\Kernel\Mvc\\RouteInterface)";
			throw new DomainException($err);
		}

		return $detail;
	}
}
