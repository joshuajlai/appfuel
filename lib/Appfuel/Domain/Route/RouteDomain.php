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
namespace Appfuel\Domain\Route;

use Appfuel\Framework\Exception,
	Appfuel\Orm\Domain\DomainModel;

/**
 * A route binds an action to the route key which is the first parameter
 * in the uri string. Routes have a public flag used by pass acl validation
 * for any public actions. Routes also hold a collection of intercepting 
 * filters which get applied by the front controller either before the or 
 * after the action is executed.
 */
class RouteDomain extends DomainModel
{
	/**
	 * This is the first parameter in the uri path. 
	 * @var string
	 */
	protected $routeKey = null;
	
	/**
	 * Action domain holds all the information about the action controller
	 * @var ActionDomain
	 */
	protected $action = null;

	/**
	 * Flag used to determine if this route is publicly accessible. All routes
	 * that are not public are subject to acl validation
	 * @var bool
	 */
	protected $isPublic = false;

	/**
	 * A collection of filters to be applied before or after the action is
	 * executed
	 * @var InterceptingFilterCollectionInterface
	 */
	protected $filters = null;

	
	/**
	 * @param	string
	 */
	public function getRouteKey()
	{
		return $this->routeKey;
	}

	/**
	 * @param	string	$name
	 * @return	OperationModel
	 */
	public function setSetRouteKey($name)
	{
		if (! $this->isNonEmptyString($name)) {
			throw new Exception("Route key must be a non empty string");
		}
		
		$this->routeKey = $name;
		$this->_markDirty('routeKey');
		return $this;
	}

	/**
	 * @return	ActionDomainInterface
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param	ActionDomainInterface	$action
	 * @return	RouteDomain
	 */
	public function setAction(ActionDomainInterface $action)
	{
		$this->action = $class;
		$this->_markDirty('action');
		return $this;
	}

	/**
	 * @return	InterceptingFilterInterface | null when not set
	 */
	public function getPreFilters()
	{
		$filters = $this->getFilters();
		if (! $filters) {
			return null;
		}

		return $filters->getPreFilters();
	}

	/**
	 * @return	InterceptingFilterCollectionInterface | null when not set
	 */
	public function getPostFilters()
	{
		$filters = $this->getFilters();
		if (! $filters) {
			return null;
		}

		return $filters->getPostFilters();
	}

	/**
	 * @return	InterceptingFilterCollectionInterface | null when not set
	 */
	public function getFilters()
	{
		return $this->filters;
	}

	/**
	 * @param	InterceptingFilterCollectionInterface $filters
	 * @return	RouteDomain
	 */
	public function setFilters(InterceptingFilterCollectionInterface $filters)
	{
		$this->filters = $filters;
		$this->_markDirty('filters');
		return $this;
	}
}
