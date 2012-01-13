<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use InvalidArgumentException;

/**
 * The route detail provides framework specific detail about the action
 * route being dispatched. It will determine if the route is public also 
 * provide basic acl checks and can hold intercepting filters specific to 
 * it
 */
class MvcRouteDetail implements MvcRouteDetailInterface
{
	/**
	 * The route associated to the this context
	 * @var string
	 */
	protected $routeKeys = array();
	
	/**
	 * Flag used to determine if this route is public and reqiures no acl check
	 * @var	 bool
	 */
	protected $isPublic = true;

	/**
	 * Flag used to detemine if the controller used by this route is internal.
	 * Internal routes can not be executed by the front controller and thus
	 * inaccessible from the outside
	 * @var bool
	 */
	protected $isInternal = false;

	/**
	 * List of acl codes allowed to access this route
	 * @var	array
	 */
	protected $aclCodes = array();

	/**
	 * List of intercepting filters used by the front controller for 
	 * this route
	 * @var array
	 */
	protected $filters = array();

	/**
	 * @param	string	$route
	 * @param	bool	$isPublic
	 * @param	array	$codes
	 * @param	array	$filters
	 * @return	MvcRouteDetail
	 */
	public function __construct($route, 
								$isPublic = true, 
								$isInternal = false,
								array $codes = null,
								array $filters = null)
	{
		if (is_array($route) && ! empty($route)) {
			$this->addRouteKeys($route);
		}
		if (is_string($route)) {
			$this->addRouteKey($route);
		}
		else {
			$err = 'route key must be a string or an array of strings';
			throw new InvalidArgumentException($err);
		}
		
		$this->isPublic   = ($isPublic === false) ? false : true;
		$this->isInternal = ($isInternal === true) ? true : false;

		if (null !== $codes) {
			$err = 'acl codes in the array must strings';
			foreach ($codes as $code) {
				if (! is_string($code)) {
					throw new InvalidArgumentException($err);
				}
			}
			$this->aclCodes = $codes;
		}

		if (null !== $filters) {
			foreach ($filters as $filter) {
				if (! is_string($filter) || empty($filter)) {
					$err = 'intercepting filter must be a non empty string';
					throw new InvalidArgumentException($err);
				}
			}
			$this->filters = $filters;
		}
	}

	/**
	 * @return	string
	 */
	public function isRouteKey($key)
	{
		$result = false;
		if (is_string($key) && in_array($key, $this->routeKeys, true)) {
			$result = true;
		}

		return $result;
	}

	/**
	 * @return	bool
	 */
	public function isPublic()
	{
		return $this->isPublic;
	}

	/**
	 * @return	bool
	 */
	public function isInternal()
	{
		return $this->isInternal;
	}

	/**
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAllowed($code)
	{
		if (empty($code) || 
			! is_string($code) || ! in_array($code, $this->aclCodes, true)) {
			return false;
		}

		return true;
	}

	/**
	 * @return	array
	 */
	public function getInterceptingFilters()
	{
		return $this->filters;	
	}

	/**
	 * @param	string	$key
	 * @return	MvcRouteDetail
	 */
	protected function addRouteKey($key)
	{
		if (! is_string($key)) {
			$err = 'route key must be a string';
		}

		if (! in_array($key, $this->routeKeys, true)) {
			$this->routeKeys[] = $key;
		}

		return $this;
	}

	/**
	 * @param	string	$key
	 * @return	MvcRouteDetail
	 */
	protected function addRouteKeys(array $keys)
	{
		foreach ($keys as $key) {
			$this->addRouteKey($key);
		}

		return $this;
	}
}
