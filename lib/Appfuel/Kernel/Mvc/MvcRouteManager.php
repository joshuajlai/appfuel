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

        self::$map[$key] = $namespace;
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
	 * @param	string	$key
	 * @return	MvcRouteDetailInterface | null
	 */
	static public function getRouteDetail($key)
	{
		$handler = self::getFromCache($key);
		if ($handler instanceof MvcRouteHandlerInterface) {
			return $handler->getRouteDetail($key);
		}

		$handler = self::createRouteHandler($key);
        if (! $handler instanceof MvcRouteHandlerInterface) {
            $err  = 'route handler must implement -(Appfuel\Kernel\Mvc';
            $err .= '\RouteHandlerInterface';
            throw new RunTimeException($err);
        }

		/* 
         * add every alias to the cache but have its entry point to the 
		 * handler which uses the master route key
		 */
		$isMaster  = false;
		$isAlias   = false;
		$masterKey = $handler->getMasterKey();
		if ($masterKey === $key) {
			$isMaster = true;
		}

		self::addToCache($masterKey, $handler);
		$aliases = $handler->getAliases();
		foreach ($aliases as $alias) {
			if ($alias === $key) {
				$isAlias = true;
			}
			self::addToCache($alias, $masterKey);
		}
			
		if (! $isMaster && ! $isAlias) {
			$aliasList = implode(',', $aliases);
			$err  = "route handler key -($masterKey), aliases -($aliasList) ";
			$err .= "keys do not match requested route key -($key)";
			throw new LogicException($err);
		}


		return $handler->getRouteDetail($key);
	}

	/**
	 * @param	string	$key
	 * @param	string|MvcRouteHandlerInterface
	 * @return	null
	 */
	static public function addToCache($key, $handler)
	{
		if (! is_string($key)) {
			$err = 'can not add to cache: rotue key must be a string';
			throw new InvalidArgumentException($err);
		}

		if (! is_string($handler) && 
			! $handler instanceof MvcRouteHandlerInterface) {
			$err  = "can not add to cache -($key) : handler must be a string ";
			$err .= "(pointer to handler, used by aliases) or an object that ";
			$err .= "implments Appfuel\Kernel\Mvc\MvcRouteHandlerInterface";
			throw new InvalidArgumentException($err);
		}

		if (is_string($handler) && ! isset(self::$cache[$handler])) {
			$err  = "can not add to cache -($key) because the master key ";
			$err .= "-($handler) was not found";
			throw new LogicException($err);
		}

		self::$cache[$key] = $handler;
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

		$handler = self::$cache[$key];
		if ($handler instanceof MvcRouteHandlerInterface) {
			return $handler;
		}

		if (is_string($handler) && isset(self::$cache[$handler])) {
			$handler = self::$cache[$handler];
			if ($handler instanceof MvcRouteHandlerInterface) {
				return $handler;
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
     * @param   string  $key
     * @return  MvcRouteHandlerInterface
     */
    static public function createRouteHandler($key)
    {
		$reader = self::$reader;
		if (! $reader) {
			$finder = new FileFinder(AF_LIB_PATH, false);
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
		$path = NamespaceParser::parseNs($namespace);
		$file = self::getRouteDetailFilename();
		$path = "$path/$file";
		
		$data = $reader->import($path, true);
		$routes = RouteBuilder::buildRoutes($data, $namespace);
		echo "<pre>", print_r($data, 1), "</pre>";exit;

        $handler = new $class();

        return $handler;
    }
}
