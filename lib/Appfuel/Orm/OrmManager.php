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
namespace Appfuel\Orm;

use LogicException,
	InvalidArgumentException,
	Appfuel\Orm\OrmFactoryInterface,
	Appfuel\Orm\DbSource\DbMapInterface;

/**
 * Holds a map of domain-key => domain\namespace, also a facade for creating 
 * domain repos and datasource maps
 */
class OrmManager
{
	/**
	 * Class name of the factory that creates domain objects like repositories
	 * and Database, File, Web Sources
	 * @var string
	 */
	static protected $ormFactoryClass = 'OrmFactory';
	
	/**
	 * List of domain key to domain namepsace mappings
	 * @var array
	 */
	static protected $map = array();

	/**
	 * map the points all childer to their parents
	 * @var array
	 */
	static protected $children = array();

	/**
	 * List of cache 
	 * @var array
	 */
	static protected $cache = array(
		'repo'    => array(),
		'factory' => array(),
		'db-map'  => array(),
	);

	/**
	 * @return	string
	 */
	static public function getOrmFactoryClassName()
	{
		return self::$ormFactoryClass;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	static public function setOrmFactoryClassName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'Orm factory class name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		self::$ormFactoryClass = $name;
	}

	/**
	 * @return	array
	 */
	static public function getRegistedDomains()
	{
		return self::$map;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	static public function setRegisteredDomains(array $list)
	{
		self::clearRegisteredDomains();
		self::registerDomains($list);
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	static public function registerDomains(array $list)
	{
		foreach ($list as $key => $namespace) {
			self::registerDomain($key, $namespace);
		}
	}

	/**
	 * @param	string	$key	
	 * @param	string	$namespace
	 * @return	null
	 */
	static public function registerDomain($key, $spec)
	{
		if (! is_string($key) || empty($key)) {
			$err = 'domain key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (is_string($spec)) {
			self::$map[$key] = $spec;
		}
		else if (! is_array($spec)) {
			$err  = "can not register domain for -($key): invalid ";
			$err .= "specification must be a string or an array";
			throw new InvalidArgumentException($err);
		}

		if (! isset($spec['namespace']) || ! is_string($spec['namespace'])) {
			$err = "can not register domain for -($key): namespace is missing";
			throw new InvalidArgumentException($err);
		}
		self::$map[$key] = $spec['namespace'];

		/* ensure each child points back to its parent */
		if (isset($spec['children']) && is_array($spec['children'])) {
			$children = $spec['children'];
			foreach($children as $subDomain) {
				if (! is_string($subDomain) || empty($subDomain)) {
					$err = "child domain key must be an non empty string";
					throw new InvalidArgumentException($err);
				}
				self::$children[$subDomain] = $key;
			}
		}
	}

	/**
	 * @param	string	$key
	 * @return	string | false when not found
	 */
	static public function getParentKey($key)
	{
		if (! is_string($key) || ! isset(self::$children[$key])) {
			return false;
		}

		return self::$children[$key];
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function getDomainNamespace($key)
	{
		if (! is_string($key) || ! isset(self::$map[$key])) {
			return false;
		}

		return self::$map[$key];
	}

	/**
	 * @return	null
	 */
	static public function clearRegisteredDomains()
	{
		self::$map = array();
	}

	/**
	 * @param	string	$type
	 * @param	string	$key
	 * @return	mixed
	 */
	static public function getFromCache($type, $key)
	{
		if (! is_string($type) || 
			! is_string($key)  ||
			! isset(self::$cache[$type]) ||
			! isset(self::$cache[$type][$key])) {
			return false;
		}

		return self::$cache[$type][$key];

	}

	/**
	 * @param	string	$type
	 * @param	string	$key
	 * @param	mixed	$object
	 * @return	null
	 */
	static public function addToCache($type, $key, $object)
	{
		if (! is_string($type) || ! isset(self::$cache[$type])) {
			$err = "can not add to cache invalid cache type";
			throw new InvalidArgumentException($err);
		}

		if (! is_string($key) || empty($key)) {
			$err = "can not add to cache: invalid domain key";
			throw new InvalidArgumentException($err);
		}

		self::$cache[$type][$key] = $object;
	}

	/**
	 * @param	string	$key
	 * @return	OrmFactoryInterface
	 */
	static public function getOrmFactory($key)
	{
		$factory = self::getFromCache('factory', $key);
		if ($factory instanceof OrmFactoryInterface) {
			return $factory;
		}
		$factory = self::createOrmFactory($key);
		if (false === $factory) {
			$err = "failed to create orm factory: domain -($key) not mapped";
			throw new InvalidArgumentException($err);
		}
		self::addToCache('factory', $key, $factory);
		return $factory;
	}

	/**
	 * @param	string	$key
	 * @return	OrmRepositoryInterface
	 */
	static public function getRepository($key, $sourceType = 'db')
	{
		$repo = self::getFromCache('repo', $key);
		if ($repo instanceof OrmRepositoryInterface) {
			return $repo;
		}
		
		$parent = self::getParentKey($key);
		if (false !== $parent) {
			$repo = self::getFromCache('repo', $parent);
			if ($repo instanceof OrmRepositoryInterface) {
				return $repo;
			}
			$key = $parent;
		}

		$factory = self::getOrmFactory($key);
		if (! $factory) {
			$err = "domain key -($key) has not been registered";
			throw new LogicException($err);
		}

		$repo = $factory->createRepository($sourceType);
		if (! $repo instanceof OrmRepositoryInterface) {
			$err = "could not create a domain repository for -($key)";
			throw new RunTimeException($err);
		}
		self::addToCache('repo', $key, $repo);

		return $repo;
	}

	/**
	 * @param	string	$key
	 * @return	DbMapInterface
	 */
	static public function getDbMap($key)
	{
		$map = self::getFromCache('db-map', $key);
		if ($map instanceof DbMapInterface) {
			return $map;
		}

		$parent = self::getParentKey($key);
		if (false !== $parent) {
			$repo = self::getFromCache('db-map', $parent);
			if ($repo instanceof DbMapInterface) {
				return $repo;
			}
			$key = $parent;
		}


		$factory = self::getOrmFactory($key);
		if (! $factory) {
			$err = "domain key -($key) has not been registered";
			throw new LogicException($err);
		}
		
		$map = $factory->createDbMap();
		if (! $map instanceof DbMapInterface) {
			$err  = "map created by orm factory does not implment ";
			$err .= "-(Appfuel\Orm\DbSource\DbMapInterface)";
			throw new LogicException($err);
		}
		self::addToCache('db-map', $key, $map);
		return $map;
	}

	/**
	 * @param	string	$key
	 * @return	DbMapInterface
	 */
	static public function createDbMap($key)
	{
		$factory = self::getOrmFactory($key);
	}

	/**
	 * @param	string	$key
	 * @return	OrmRepositoryInterface
	 */
	static public function createOrmFactory($key)
	{
		$namespace = self::getDomainNamespace($key);
		if (false === $namespace) {
			return false;
		}
		
		$class = $namespace . '\\' . self::getOrmFactoryClassName();
		return new $class();
	}
}
