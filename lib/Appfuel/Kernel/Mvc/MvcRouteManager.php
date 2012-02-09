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
	InvalidArgumentException;

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

		/* add every alias to the cache but have its entry point to the 
		 * handler which uses the master route key
		 */
		$masterKey = $handler->getMasterKey();
		$aliases   = $handler->getAliases();
		self::addToCache($masterKey, $handler);
		foreach ($aliases as $alias) {
			self::addToCache($alias, $masterKey);
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
     * @param   string  $key
     * @return  MvcRouteHandlerInterface
     */
    static public function createRouteHandler($key)
    {
        $namespace = self::getNamespace($key);
		if (false === $namespace) {
			$err = "could not resolve namespace for route key -($key)";
			throw new RunTimeException($err);
		}

        $class  = "$namespace\\RouteHandler";
        $handler = new $class();

        return $handler;
    }
}
