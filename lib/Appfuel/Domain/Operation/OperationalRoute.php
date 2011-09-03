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
namespace Appfuel\Domain\Operation;

use Appfuel\Framework\Exception,
	Appfuel\Orm\Domain\DomainModel,
	Appfuel\Framework\Action\ControllerNamespace,
	Appfuel\Framework\Domain\Operation\OperationInterface,
	Appfuel\Framework\Domain\Operation\OperationalRouteInterface;

/**
 * An operational route is an operation that bound to a action controller via
 * the route string. Operational routes have additional properties that don't
 * belong to either operations or controllers such as: accessPolicy, 
 * routeString, defaultFormat and requestType
 */
class OperationalRoute extends DomainModel implements OperationalRouteInterface
{
	/**
	 * Textual name 
	 * @var string
	 */
	protected $operation = null;

	/**
	 * Value object used to holds the namespaces of the action controller
	 * @var	ControllerNamespace
	 */
	protected $controllerNamespace = null;


	/**
	 * Access policy determines if an operation is public|private
	 * @var string
	 */
	protected $accessPolicy = null;

	/**
	 * Part of the url that describes the location of the controller
	 * @var string
	 */	
	protected $routeString = null;

	/**
	 * The default return type of the operation. This can be overwritten by 
	 * the user if the controller supports multiple return types
	 * @var string
	 */
	protected $defaultFormat = null;
	
	/**
	 * List of intercept filters to be added to the front controller
	 * @var string
	 */
	protected $filters = array(
		'pre'  => array(),
		'post' => array()
	);

	/**
	 * The request type is a key that represents the type of request expected 
	 * for this given operation. Example http, cli, http-ajax,
	 * @var	string
	 */
	protected $requestType = null;

	/**
	 * @param	string
	 */
	public function getOperation()
	{
		return $this->operation;
	}

	/**
	 * @param	string	$name
	 * @return	OperationalModel
	 */
	public function setOperation(OperationInterface $op)
	{
		$this->operation = $op;
		$this->_markDirty('operation');
		return $this;
	}

	/**
	 * @return	ControllerNamespaceInterface
	 */
	public function getControllerNamespace()
	{
		return $this->controllerNamespace;
	}

	/**
	 * @param	string	$actionNs	namespace of the action controller
	 * @return	ControllerNamespaceInterface
	 */
	public function setControllerNamespace($actionNs)
	{
		if (! $this->isNonEmptyString($actionNs)) {
			throw new Exception("Action namespace must be a non empty string");
		}
	
		$this->controllerNamespace = new ControllerNamespace($actionNs);
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getAccessPolicy()
	{
		return $this->accessPolicy;
	}

	/**
	 * @param	string	$level	either public | private
	 * @return	OperationModel
	 */
	public function setAccessPolicy($level)
	{
		if (! $this->isNonEmptyString($level)) {
			throw new Exception("Access policy must be a non empty string");
		}

		$level = strtolower($level);
		if (! in_array($level, array('public', 'private'))) {
			throw new Exception("Access policy must be either public|private");
		}

		$this->accessPolicy = $level;
		$this->_markDirty('accessPolicy');
		return $this;
	}

	/**
	 * @param	string
	 */
	public function getRouteString()
	{
		return	$this->routeString;
	}

	/**
	 * @param	string	$route
	 * @return	OperationModel
	 */
	public function setRouteString($route)
	{
		if (! $this->isNonEmptyString($route)) {
			throw new Exception("route must be a non empty string");
		}

		$this->routeString = $route;
		$this->_markDirty('routeString');
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getDefaultFormat()
	{
		return $this->defaultFormat;
	}

	/**
	 * @param	string	$format
	 * @return	OperationModel
	 */
	public function setDefaultFormat($format)
	{
		if (! $this->isNonEmptyString($format)) {
			throw new Exception("Default format must be a non empty string");
		}

		$this->defaultFormat = $format;
		$this->_markDirty('defaultFormat');
		return $this;
	}

	/**
	 * Add a single filter to the filter list and mark the filters as dirty
	 * 
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$filter
	 * @return	null
	 */
	public function addFilter($filter, $type)
	{
		if (! $this->isNonEmptyString($filter)) {
			throw new Exception("filter must be a non empty string");
		}

		if (! $this->isNonEmptyString($type)) {
			throw new Exception("filter type must be a non empty string");
		}

		$type = strtolower($type);
		if (! in_array($type, array('pre', 'post'))) {
			$err = "filter type values must be pre|post -($type) ";
			throw new Exception($err);
		}

		if (in_array($filter, $this->filters[$type])) {
			return $this;
		}

		$this->filters[$type][] = $filter;
		$this->_markDirty('filters');
		return $this;
	}

	/**
	 * Add many filters from a single datastructure with the following shape:
	 * array (
	 *	'pre'	=> array('filter1', 'filter2', 'filter3'),
	 *  'post'	=> array('filter4', 'filter5', 'filter6')
	 * );
	 * 
	 * or 
	 * array (
	 *	'pre'  => 'filter1',
	 *	'post' => 'filter2'
	 * );
	 * 
	 * @throws	Appfuel\Framework\Exception
	 * @param	array	$filters
	 * @return	OperationModel
	 */
	public function setFilters(array $filters)
	{
		if (empty($filters)) {
			return $this;
		}

		/* 
		 * clear out any existing filters
		 */
		$this->filters = array(
			'pre'	=> array(),
			'post'	=> array()
		);
		if (array_key_exists('pre', $filters)) {
			$list = $filters['pre'];
			if (! empty($list) && is_string($list)) {
				$list = array($list);
			}

			if (! empty($list) && is_array($list)) {
				foreach ($list as $filter) {
					$this->addFilter($filter, 'pre');
				}
			}
		}

		if (array_key_exists('post', $filters)) {
			$list = $filters['post'];
			if (! empty($list) && is_string($list)) {
				$list = array($list);
			}

			if (! empty($list) && is_array($list)) {
				foreach ($list as $filter) {
					$this->addFilter($filter, 'post');
				}
			}
		}

		return $this;
	}

	/**
	 * @return	array
	 */
	public function getPreFilters()
	{
		return $this->filters['pre'];
	}

	/**
	 * @return	array
	 */
	public function getPostFilters()
	{
		return $this->filters['post'];
	}

	/**
	 * @return	string
	 */
	public function getRequestType()
	{
		return $this->requestType;
	}
	
	/**
	 * @param	string	$type
	 * @return	OperationModel
	 */
	public function setRequestType($type)
	{
		if (! $this->isNonEmptyString($type)) {
			throw new Exception("Request type  must be a non empty string");
		}

		$type = strtolower($type);
		if (! in_array($type, array('http','ajax', 'cli'))) {
			throw new Exception("Request type must be http|ajax|cli -($type)");
		}

		$this->requestType = $type;
		$this->_markDirty('requestType');
		return $this;
	}
}
