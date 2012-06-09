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

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\ClassLoader\NamespaceParser;

/**
 */
class MvcRouteManager
{
	/**
	 * Maps routes (and aliases) to namespaces
	 * @var array
	 */
	static protected $map = array();

	/**
	 * List of route detail objects used by the view builder, front controller,
	 * and action dispatcher
	 * @var array
	 */
	static protected $cache = array();

	/**
	 * Route key of the route dispatched by the front controller
	 * @var string
	 */
	static protected $currentKey = null;

	/**
	 * Used to read in the route details configuration
	 * @var FileReaderInterface
	 */
	static protected $reader = null;

	/**
	 * Name of the file that holds an array of file details 
	 * @var string
	 */
	static protected $routeDetailFilename = 'route-details.php';

    /**
     * @return  array
     */
    static public function getRouteMap()
    {
        return  self::$map;
    }

    /**
     * @return null
     */
    static public function clearRouteMap()
    {
        self::$map = array();
    }

	/**
	 * @param	string	$key
	 * @return	null
	 */
	static public function setCurrentRouteKey($key)
	{
		if (false === self::getNamespace($key)) {
			$err  = "can not set current route key with a route that has not ";
			$err .= "been mapped";
			throw new LogicException($err);
		}

		self::$currentKey = $key;
	}

	/**
	 * @return	string
	 */
	static public function getCurrentRouteKey()
	{
		return self::$currentKey;
	}

	/**
	 * @return	MvcRouteDetailInterface
	 */
	static public function getCurrentRoute()
	{
		return self::getRouteDetail(self::getCurrentRouteKey());
	}

    /**
     * @param   string  $key
     * @param   string  $class
     * @return  null
     */
    static public function addRoute($key, $namespace)
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException("route key must be a string");
        }

        if (! is_string($namespace)) {
			$err = "action namespace must be a string";
            throw new InvalidArgumentException($err);
        }

		if (self::isRoute($namespace)) {
			$namespace = self::$map[$namespace];
		}

        self::$map[$key] = $namespace;
    }

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isRoute($key)
	{
		if (! is_string($key) || ! isset(self::$map[$key])) {
			return false;
		}

		return true;
	}
	
	/**
	 * @param	array	$list
	 * @return	null
	 */
	static public function setRouteMap(array $list)
	{
		self::clearRouteMap();
		foreach ($list as $key => $namespace) {
			self::addRoute($key, $namespace);
		}
	}

	/**
	 * @param	array  $list
	 * @return	null
	 */
	static public function loadRouteMap(array $list)
	{
		foreach ($list as $key => $namespace) {
			self::addRoute($key, $namespace);
		}
	}

    /**
     * @param   string  $key    domain key
     * @return  string
     */
    static public function getNamespace($key)
    {
        if (! is_string($key) || ! isset(self::$map[$key])) {
            return false;
        }

        return self::$map[$key];
    }

	/**
	 * Look into the cache for the detail if not found, build a list of routes
	 * from the routes detail file add them to the cache then return the 
	 * requested route
	 *
	 * @param	string	$key
	 * @return	MvcRouteDetailInterface | null
	 */
	static public function getRouteDetail($key)
	{
		$detail = self::getFromCache($key);
		if ($detail instanceof MvcRouteDetailInterface) {
			return $detail;
		}

		$list = self::buildRouteDetails($key);
		if (empty($list)) {
			return false;
		}

		if (! isset($list[$key])) {
			$err = "could not find -($key) in route detail spec";
			throw new LogicException($err);
		}

		foreach ($list as $routeKey => $detail) {
			self::addToCache($routeKey, $detail);
		}

		if (is_string($list[$key])) {
			$key = $list[$key];
		}

		return $list[$key];
	}

	/**
	 * @param	string	$key
	 * @param	string|MvcRouteHandlerInterface
	 * @return	null
	 */
	static public function addToCache($key, $detail)
	{
		if (! is_string($key)) {
			$err = 'can not add to cache: rotue key must be a string';
			throw new InvalidArgumentException($err);
		}

		if (! is_string($detail) && 
			! $detail instanceof MvcRouteDetailInterface) {
			$err  = "can not add to cache -($key) : detail must be a string ";
			$err .= "(pointer to a detail) or an object that ";
			$err .= "implments Appfuel\Kernel\Mvc\MvcRouteDetailInterface";
			throw new InvalidArgumentException($err);
		}

		if (is_string($detail) && ! isset(self::$cache[$detail])) {
			$err  = "can not add to cache -($key) because the pointer key ";
			$err .= "-($detail) was not found";
			throw new LogicException($err);
		}

		self::$cache[$key] = $detail;
	}

	/**
	 * @param	string	$key
	 * @return	MvcRouteHandlerInterface | false
	 */
	static public function getFromCache($key)
	{
		if (! is_string($key) || ! isset(self::$cache[$key])) {
			return false;
		}

		$detail = self::$cache[$key];
		if ($detail instanceof MvcRouteDetailInterface) {
			return $detail;
		}

		if (is_string($detail) && isset(self::$cache[$detail])) {
			$detail = self::$cache[$detail];
			if ($detail instanceof MvcRouteDetailInterface) {
				return $detail;
			}
		}		

		return false;
	}

	/**
	 * @return	array
	 */
	static public function getAllCache()
	{
		return self::$cache;
	}

	/**
	 * @return	null
	 */
	static public function clearCache()
	{
		self::$cache = array();
	}

	/**
	 * @return	string
	 */
	static public function getRouteDetailFilename()
	{
		return self::$routeDetailFilename;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	static public function setRouteDetailFilename($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'route detail filename must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (false === strpos($name, ".php")) {
			$err  = "route detail must be php file no -(.php) extension found ";
			$err .= "-($name)";
			throw new InvalidArgumentException($err);
		}

		self::$routeDetailFilename = $name;
	}

    /**
	 * Generate a list of route details from the route details file. The route
	 * builder implements inheritance among route detail specifications. Since
	 * all route details from the file share the same namespace the are all
	 * added to the route map. This allows alias routes to be found by the
	 * framework. 
	 *
     * @param   string  $key
     * @return  MvcRouteHandlerInterface
     */
    static public function buildRouteDetails($key)
    {
		$reader = self::$reader;
		if (! $reader) {
			$finder = new FileFinder(AF_CODE_PATH, false);
			$reader = new FileReader($finder);
			self::$reader = $reader;
		}
        $namespace = self::getNamespace($key);

		/*
		 * 404 is used as the error code because the fault handler will
		 * catch this and use a 404 http reponse for any thing that is not
		 * commandline and exit with 404 for commandline
		 */
		if (false === $namespace) {
			$err = "could not resolve namespace for route key -($key)";
			throw new RunTimeException($err, 404);
		}

		/*
		 * grap the route detail file form disk and use the route
		 * build to create a list of routes from its specifications
		 */
		$path   = NamespaceParser::parseNs($namespace);
		$file   = self::getRouteDetailFilename();
		$path   = "$path/$file";		
		$data   = $reader->import($path, true);
		$routes = RouteBuilder::buildRoutes($data);
		if (empty($routes)) {
			return false;
		}

		/*
		 * Make alias routes visible to the framework
		 */
		$routeKeys = array_keys($routes);
		foreach ($routeKeys as $routeKey) {
			if (! self::isRoute($routeKey)) {
				self::addRoute($routeKey, $namespace);
			}			
		}

        return $routes;
    }
}
