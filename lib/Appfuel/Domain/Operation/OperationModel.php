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
	Appfuel\Framework\Action\ActionControllerDetail;

/**
 * An operation represents an action that can be preformed by a user or system.
 * The framework identitifies an operation by its route string while users 
 * refer to it by its name. The route string is a public string used to map an 
 * operation to an action controller namespace, decoupling the action 
 * controller class from the url used to request that action controller. 
 */
class OperationModel extends DomainModel
{
	/**
	 * Textual name 
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var string
	 */
	protected $description = null;
	
	/**
	 * Access policy determines if an operation is public|private
	 * @var string
	 */
	protected $accessPolicy = null;

	/**
	 * Part of the url that describes the location of the controller
	 * @var string
	 */	
	protected $route = null;

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
	 * The class of operation the operation belongs to. Current their are the 
	 * following classes: business|infrastructure|ui
	 *
	 * @var string
	 */
	protected $opClass = null;

	/**
	 * The request type is a key that represents the type of request expected 
	 * for this given operation. Example http, cli, http-ajax,
	 * @var	string
	 */
	protected $requestType = null;

	/**
	 * Value object that provides detail information about the controller that 
	 * executes this operation. The front controller uses the controller detail
	 * @var	ActionControllerDetail 
	 */
	protected $controllerDetail = null;

	/**
	 * @param	string	$actionNs	namespace of the action controller
	 * @return	OperationModel
	 */
	public function setControllerDetail($actionNs)
	{
		if (! $this->isNonEmptyString($actionNs)) {
			throw new Exception("Action namespace must be a non empty string");
		}
	
		$this->controllerDetail = new ActionControllerDetail($actionNs);
		return $this;
	}

	/**
	 * @param	string	$name
	 * @return	OperationModel
	 */
	public function setName($name)
	{
		if (! $this->isNonEmptyString($name)) {
			throw new Exception("Operation name must be a non empty string");
		}
		
		$this->name = $name;
		$this->_markDirty('name');
		return $this;
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
	 * @param	string	$route
	 * @return	OperationModel
	 */
	public function setRoute($route)
	{
		if (! $this->isNonEmptyString($route)) {
			throw new Exception("route must be a non empty string");
		}

		$this->route = $route;
		$this->_markDirty('route');
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
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$class	either business|infra|ui
	 * @return	OperationModel
	 */
	public function setOpClass($class)
	{
		if (! $this->isNonEmptyString($class)) {
			throw new Exception("Operation class must be a non empty string");
		}

		$class = strtolower($class);
		if (! in_array($class, array('business','infra', 'ui'))) {
			$err = "Operation class must be business|infra|ui -($class)";
			throw new Exception($err);
		}

		$this->opClass = $class;
		$this->_markDirty('opClass');
		return $this;
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
