<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
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
		'handler' => array(
			'general-handler' => 'Appfuel\Validate\ValidationHandler'
		),
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
		if (isset($map['handler'])) {
			self::setHandlerMap($map['handler']);
		}

		if (isset($map['validator'])) {
			self::setValidatorMap($map['validator']);
		}
		
		if (isset($map['filter'])) {
			self::setFilterMap($map['filter']);	
		}
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setHandlerMap(array $map)
	{
		self::clearHandlerMap();
		self::loadHandlerMap($map);
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function loadHandlerMap(array $map)
	{
		foreach ($map as $key => $class) {
			self::addToHandlerMap($key, $class);
		}
	}

	/**
	 * @return	array
	 */
	static public function getHandlerMap()
	{
		return	self::$map['handler'];
	}

	/**
	 * @param	string	$key
	 * @param	string	$class
	 * @return	null
	 */
	static public function addToHandlerMap($key, $class)
	{
		if (! is_string($key) || empty($key)) {
			$err = "key in handler category must be a non empty string";
			throw new DomainException($err);
		}

		if (! is_string($class) || empty($class)) {
			$err = "class in handler category must be a non empty string";
			throw new DomainException($err);
		}

		self::$map['handler'][$key] = $class;
	}

	/**
	 * @return	null
	 */
	static public function clearHandlerMap()
	{
		self::$map['handler'] = array();
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setValidatorMap(array $map)
	{
		self::clearValidatorMap();
		self::loadValidatorMap($map);
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function loadValidatorMap(array $map)
	{
		foreach ($map as $key => $class) {
			self::addToValidatorMap($key, $class);
		}
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
	 * @param	string	$key
	 * @param	string	$class
	 * @return	null
	 */
	static public function addToValidatorMap($key, $class)
	{
		if (! is_string($key) || empty($key)) {
			$err = "key in validator category must be a non empty string";
			throw new DomainException($err);
		}

		if (! is_string($class) || empty($class)) {
			$err = "class in validator category must be a non empty string";
			throw new DomainException($err);
		}

		self::$map['validator'][$key] = $class;
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setFilterMap(array $map)
	{
		self::clearFilterMap();
		self::loadFilterMap($map);
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function loadFilterMap(array $map)
	{
		foreach ($map as $key => $class) {
			self::addToFilterMap($key, $class);
		}
	}

	/**
	 * @param	string	$key
	 * @param	string	$class
	 * @return	null
	 */
	static public function addToFilterMap($key, $class)
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
	 * @param	string	$key
	 * @return	ValidationHandlerInterface
	 */
	static public function createHandler($key = null, 
										 CoordinatorInterface $coord = null)
	{
		if (null === $key) {
			return new ValidationHandler($coord);
		}

		$class = self::map('handler', $key);
		if (false === $class) {
			$err = "could not map validation handler with -($key)";
			throw new DomainException($err);
		}
		$handler = new $class($coord);
		if (! $handler instanceof ValidationHandlerInterface) {
			$iface = 'Appfuel\Validate\ValidationHandlerInterface';
			$err   = "handler -($key,$class) does not implment -($iface)";
			throw new DomainException($err);
		}
	
		return $handler;
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
		if (null === $key) {
			return new FieldValidator();
		}
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
	 * @param	array	$data
	 * @param	string	$key
	 * @return	Filter\FilterSpecInterface
	 */
	static public function createFieldSpec(array $data, $key = null)
	{
		if (null === $key) {
			return new FieldSpec($data);
		}
		$class = self::map('validator', $key);
		if (false === $class) {
			$err = "could not map field spec with -($key)";
			throw new DomainException($err);
		}

		$spec = new $class($data);
		if (! $spec instanceof FieldSpecInterface) {
			$iface = 'Appfuel\Validate\FieldSpecInterface';
			$err   = "field spec -($key, $class) must implement -($iface)";
			throw new DomainException($err);
		}

		return $spec;
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

		$spec = new $class($data);
		if (! $spec instanceof FilterSpecInterface) {
			$iface = 'Appfuel\Validate\Filter\FilterSpecInterface';
			$err   = "filter spec -($key, $class) must implement -($iface)";
			throw new DomainException($err);
		}

		return $spec;
	}

	/**
	 * @return	null
	 */
	static public function clear()
	{
		self::clearHandlerMap();
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
}
