<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Validate;

use DomainException,
	InvalidArgumentException,
	Appfuel\Validate\Filter\FilterSpec,
	Appfuel\Validate\Filter\FilterInterface;

/**
 * Holds all validators and filters. Also encapsulates create these objects
 * using a mapping system to create theme.
 */
class ValidationFactory implements ValidationFactoryInterface
{
	/**
	 * List of key to class name mappings used to create validators
	 * @var	array
	 */
	static protected $validatorMap = array();

	/**
	 * List of key to class mappings used to create filter specifications
	 * @var array
	 */
	static protected $filterSpecMap = array();
	
	/**
	 * List of key to class name mappings used to create filters
	 * @var array
	 */
	static protected $filterMap = array();

	/**
	 * Qualified class name of coordinator that should be used in place
	 * of the appfuel coordinator
	 * @var string
	 */
	static protected $coordinator = null;


	/**
	 * @return	string
	 */
	static public function getCoordinatorClass()
	{
		return self::$coordinator;
	}

	/**
	 * @param	string	$class
	 * @return	null
	 */
	static public function setCoordinatorClass($class)
	{
		if (! is_string($class) || empty($class)) {
			$err = "coordinator class must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		self::$coordinator = $class;
	}

	/**
	 * @return	CoordinatorInterface
	 */
	static public function createCoordinator()
	{
		$class = self::getCoordintor();
		if ($class) {
			$coord = new $class();
			if (! $coord instanceof CoordinatorInterface) {
				$err  = "coordinator -($class) does not implment -(Appfuel";
				$err .= "\Validate\CoordinateInterface)";
				throw new DomainException($err);
			}
		}
		else {
			$coord = new Coordinator();
		}

		return $coord;
	}

	/**
	 * @return	null
	 */
	static public function clearCoordinatorClass()
	{
		self::$coordinator = null;
	}

	/**
	 * @return	array
	 */
	static public function getValidatorMap()
	{
		return self::$validatorMap;	
	}

	/**
	 * @param	array	$map	
	 * @return	null
	 */
	static public function setValidatorMap(array $map)
	{
		if ($map === array_values($map)) {
			$err  = "validator map must be an associative array of key ";
			$err .= "to validator class name mappings";
			throw new DomainException($err);
		}

		foreach ($map as $key => $value) {
			if (! is_string($key) || empty($key)) {
				$err = "validator key must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_string($value) || empty($value)) {
				$err = "validator class must be a non empty string";
				throw new DomainException($err);
			}
		}

		self::$validatorMap = $map;
	}

	/**
	 * @return	null
	 */
	static public function clearValidatorMap()
	{
		self::$validatorMap = array();
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function mapValidator($key)
	{
		if (! is_string($key) || ! isset(self::$validatorMap[$key])) {
			return false;
		}

		return self::$validatorMap[$key];
	}

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function createValidator($key)
	{
		$class = self::mapValidator($key);
		if (false === $class) {
			$err = "validator -($key) is not mapped";
			throw new DomainException($err);
		}
		$validator = new $class();
		if (! $validator instanceof ValidatorInterface) {
			$err  = "validator -($key, $class) must implement -(Appfuel";
			$err .= "\Validate\ValidatorInterface)";
			throw new DomainException($err);
		}

		return $validator;
	}

	/**
	 * @return	array
	 */
	static public function getFilterMap()
	{
		return self::$filterMap;
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setFilterMap(array $map)
	{
		if ($map === array_values($map)) {
			$err  = "filter map must be an associative array of key ";
			$err .= "to filter class name mappings";
			throw new DomainException($err);
		}

		foreach ($map as $key => $value) {
			if (! is_string($key) || empty($key)) {
				$err = "filter key must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_string($value) || empty($value)) {
				$err = "filter class must be a non empty string";
				throw new DomainException($err);
			}
		}

		self::$filterMap = $map;
	}

	/**
	 * @return	null
	 */
	static public function clearFilterMap()
	{
		self::$filterMap = array();
	}

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function createFilter($key)
	{
		$class = self::mapFilter($key);
		if (false === $class) {
			$err = "filter -($key) is not mapped";
			throw new DomainException($err);
		}
		$filter = new $class();
		if (! $filter instanceof FilterInterface) {
			$err  = "filter -($key, $class) must implement -(Appfuel\Validate";
			$err .= "\Filter\FilterInterface)";
			throw new DomainException($err);
		}

		return $filter;
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function mapFilter($key)
	{
		if (! is_string($key) || ! isset(self::$filterMap[$key])) {
			return false;
		}

		return self::$filterMap[$key];
	}

	static public function createFilterSpec($key, $data)
	{
		return new FilterSpec($data);
	}

	/**
	 * @return	null
	 */
	static public function clear()
	{
		self::clearCoordinatorClass();
		self::clearValidatorMap();
		self::clearFilterMap();
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static protected function isValidKey($key)
	{
		if (! is_string($key) || empty($key)) {
			return false;
		}

		return true;
	}
}
