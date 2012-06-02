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
	Appfuel\Validate\Filter\FilterInterface,
	Appfuel\Validate\Filter\FilterSpecInterface;

/**
 * Mappings for validator and filters which decouple the validations 
 * classes from the validaton handlers.
 */
class ValidationFactory
{
	/**
	 * @var array
	 */
	static protected $map = array(
		'validator' => array(
			'field-validator' => 'Appfuel\Validate\FieldValidator',
			'field-spec'      => 'Appfuel\Validate\FieldSpec',
			'coordinator'	  => 'Appfuel\Validate\Coodinator',
		),

		'filter' => array(
			'int'			=> 'Appfuel\Validate\Filter\IntFilter',
			'bool'			=> 'Appfuel\Validate\Filter\BoolFilter',
			'string'		=> 'Appfuel\Validate\Filter\StringFilter',
			'email'			=> 'Appfuel\Validate\Filter\EmailFilter',
	        'regex'			=> 'Appfuel\Validate\Filter\RegexFilter',
			'ip'			=> 'Appfuel\Validate\Filter\IpFilter',
			'float'			=> 'Appfuel\Validate\Filter\FloatFilter',
			'filter-spec'	=> 'Appfuel\Validate\Filter\FilterSpec',
		),
	);

	/**
	 * @return	array
	 */
	static public function getMap()
	{
		return self::$map;	
	}

	/**
	 * @param	array	$map	
	 * @return	null
	 */
	static public function setMap(array $map)
	{
		if (! isset($map['validator']) || ! is_array($map['validator'])) {
			$err = "the validator mapping is missing: key -(validator)";
			throw new DomainException($err);
		}
		self::setValidatorMap($map['validator']);
		
		if (! isset($map['filter']) || ! is_array($map['filter'])) {
			$err = "the filter mapping is missing: key -(filter)";
			throw new DomainException($err);
		}
		self::setFilterMap($map['filter']);	
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setValidatorMap(array $map)
	{
		self::validateMap($map, 'validator');
		self::$map['validator'] = $map;	
	}

	/**
	 * @return	array
	 */
	static public function getValidatorMap()
	{
		return	self::$map['validator'];
	}

	/**
	 * @return	null
	 */
	static public function clearValidatorMap()
	{
		self::$map['validator'] = array();
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setFilterMap(array $map)
	{
		self::validateMap($map, 'filter');
		self::$map['filter'] = $map;	
	}

	/**
	 * @param	string	$key
	 * @param	string	$class
	 * @return	null
	 */
	static public function addToFilter($key, $class)
	{
		if (! is_string($key) || empty($key)) {
			$err = "key in filter category must be a non empty string";
			throw new DomainException($err);
		}

		if (! is_string($class) || empty($class)) {
			$err = "class in filter category must be a non empty string";
			throw new DomainException($err);
		}

		self::$map['filter'][$key] = $class;
	}

	/**
	 * @return	array
	 */
	static public function getFilterMap()
	{
		return self::$map['filter'];
	}

	/**
	 * @return	null
	 */
	static public function clearFilterMap()
	{
		self::$map['filter'] = array();
	}

	/**
	 * @param	string	$name
	 * @param	string	$key
	 * @return	mixed
	 */
	static public function create($name, $key)
	{
		$class = self::map($name, $key);
		if (false === $class) {
			$err = "could not create object: could not map -($name, $key)";
			throw new DomainException($err);
		}

		return new $class();
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function map($name, $key)
	{
		if (! is_string($name) ||
			! is_string($key) ||
			! isset(self::$map[$name]) ||
			! isset(self::$map[$name][$key])) {
			return false;
		}

		return self::$map[$name][$key];
	}

	/**
	 * @param	array	$map
	 * @param	string	$name	name of the map to validate
	 * @return
	 */
	static protected function validateMap(array $map, $name)
	{
		if ($map === array_values($map)) {
			$err  = "-($name) map must be an associative array of key ";
			$err .= "to class name mappings";
			throw new DomainException($err);
		}

		foreach ($map as $key => $value) {
			if (! is_string($key) || empty($key)) {
				$err = "-($name) key must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_string($value) || empty($value)) {
				$err = "-($name) class must be a non empty string";
				throw new DomainException($err);
			}
		}
	}

	/**
	 * @param	string	$key
	 * @return	CoordinatorInterface
	 */
	static public function createCoordinator($key = null)
	{
		if (null === $key) {
			return new Coordinator();
		}

		$coord = self::create('validator', $key);
		if (! $coord instanceof CoordinatorInterface) {
			$class = get_class($coord);
			$iface = 'Appfuel\Validate\CoordinatorInterface';
			$err   = "coordinator -($key,$class) does not implment -($iface)";
			throw new DomainException($err);
		}

		return $coord;
	}

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function createValidator($key = null)
	{
		$validator = self::create('validator', $key);
		if (! $validator instanceof ValidatorInterface) {
			$class = get_class($validator);
			$iface = 'Appfuel\Validate\ValidatorInterface';
			$err  = "validator -($key, $class) must implement -($iface)";
			throw new DomainException($err);
		}

		return $validator;
	}

	/**
	 * @param	string $key
	 * @return	Filter\FilterInterface
	 */
	static public function createFilter($key)
	{
		$filter = self::create('filter', $key);
		if (! $filter instanceof FilterInterface) {
			$class = get_class($filter);
			$iface = 'Appfuel\Validate\Filter\FilterInterface';
			$err   = "filter -($key, $class) must implement -($iface)";
			throw new DomainException($err);
		}

		return $filter;
	}

	/**
	 * @param	array	$data
	 * @param	string	$key
	 * @return	Filter\FilterSpecInterface
	 */
	static public function createFilterSpec(array $data, $key = null)
	{
		if (null === $key) {
			return new FilterSpec($data);
		}
		$class = self::map('filter', $key);
		if (false === $class) {
			$err = "could not map filter with -($key)";
			throw new DomainException($err);
		}

		$filter = new $class($data);
		if (! $filter instanceof FilterSpecInterface) {
			$iface = 'Appfuel\Validate\Filter\FilterSpecInterface';
			$err   = "filter spec -($key, $class) must implement -($iface)";
			throw new DomainException($err);
		}

		return $filter;
	}

	/**
	 * @return	null
	 */
	static public function clear()
	{
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
