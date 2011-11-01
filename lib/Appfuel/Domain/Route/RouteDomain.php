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
	Appfuel\Orm\Domain\DomainModel,
	Appfuel\Framework\Domain\Route\RouteDomainInterface,
	Appfuel\Framework\Domain\Action\ActionDomainInterface,
	Appfuel\Domain\InterceptFilter\InterceptFilterCollection;

/**
 * A route binds an action to the route key which is the first parameter
 * in the uri string. Routes have a public flag used by pass acl validation
 * for any public actions. Routes also hold a collection of intercepting 
 * filters which get applied by the front controller either before the or 
 * after the action is executed.
 */
class RouteDomain extends DomainModel implements RouteDomainInterface
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
		$this->action = $action;
		$this->_markDirty('action');
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPublic()
	{
		return $this->isPublic;
	}

	/**
	 * @param	bool	$flag
	 * @return	RouteDomain
	 */
	public function setIsPublic($flag)
	{
		if (! is_bool($flag)) {
			throw new Exception("isPublic flag must be a bool value");
		}

		$this->isPublic = $flag;
		$this->_markDirty('isPublic');
		return $this;
	}

	/**
	 * @return	InterceptingFilterCollectionInterface | null when not set
	 */
	public function getFilters()
	{
		return $this->filters;
	}

	/**
	 * @param	InterceptFilterCollection$filters
	 * @return	RouteDomain
	 */
	public function setFilters(InterceptFilterCollection $filters)
	{
		$this->filters = $filters;
		$this->_markDirty('filters');
		return $this;
	}
}
