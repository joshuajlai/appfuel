<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use DomainException;

/**
 * As a sub system to the route detail, route access provides acl control
 * to route. It determines if a route is public (no acl required) or internal
 * (can only be called by an action and not by the front controller). Acl codes
 * may also be directly mapped to input method used: appfuel supports 5 methods
 * get, post, put, delete, cli. 
 */
class RouteAccess implements RouteAccessInterface
{
	/**
	 * Flag used to determine if this route is public and reqiures no acl check
	 * @var	 bool
	 */
	protected $isPublic = false;

	/**
	 * Flag used to detemine if the controller used by this route is internal.
	 * Internal routes can not be executed by the front controller and thus
	 * inaccessible from the outside
	 * @var bool
	 */
	protected $isInternal = false;

	/**
	 * Used when isPublic is false but you want anyone who as acl access to
	 * pass through
	 * @var bool
	 */
	protected $isIgnoreAcl = false;

	/**
	 * Flag used to determine if acl access is mapped a method. Used in 
	 * restful calls, to different acl codes to get, post, delete, and put
	 * @var bool
	 */
	protected $isAclForEachMethod = false;

	/**
	 * Can be a list of acl codes or a map of http method to acl codes.
	 * @var	array
	 */
	protected $map = array();

	/**
	 * @return RouteAccess
	 */
	public function enablePublicAccess()
	{
		$this->isPublic = true;
		return $this;
	}

	/**
	 * @return RouteAccess
	 */
	public function disablePublicAccess()
	{
		$this->isPublic = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPublicAccess()
	{
		return $this->isPublic;
	}

	/**
	 * @return	RouteAccess
	 */
	public function enableInternalOnlyAccess()
	{
		$this->isInternal = true;
		return $this;
	}

	/**
	 * @return	RouteAccess
	 */
	public function disableInternalOnlyAccess()
	{
		$this->isInternal = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isInternalOnlyAccess()
	{
		return $this->isInternal;
	}

	/**
	 * @return	RouteAccess
	 */
	public function ignoreAclAccess()
	{
		$this->isIgnoreAcl = true;
		return $this;
	}

	/**
	 * @return	RouteAccess
	 */
	public function useAclAccess()
	{
		$this->isIgnoreAcl = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAclAccessIgnored()
	{
		return $this->isIgnoreAcl;
	}

	/**
	 * @return	RouteAccess
	 */
	public function useAclForAllMethods()
	{
		$this->isAclForEachMethod = false;
		return $this;
	}

	/**
	 * @return	RouteAccess
	 */
	public function useAclForEachMethod()
	{
		$this->isAclForEachMethod = true;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isAclForEachMethod()
	{
		return $this->isAclForEachMethod;
	}

	/**
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAccessAllowed($codes, $method = null)
	{
		if ($this->isPublicAccess() || $this->isAclAccessIgnored()) {
			return true;
		}
		
		if (is_string($codes)) {
			$codes = array($codes);
		}
		else if (! is_array($codes)) {
			return false;
		}

		$compare = array();
		foreach ($codes as $code) {
			if (is_string($code) && ! empty($code)) {
				$compare[] = $code;
			}
		}
		
		if ($this->isAclForEachMethod()) {
			if (! is_string($method) || 
				! isset($this->map[$method]) ||
				! is_array($this->map[$method])) {
				return false;
			}
			$map = $this->map[$method];
		}
		else {
			$map = $this->map;
		}

		$result = array_intersect($map, $compare);

		if (empty($result)) {
			return false;
		}

		return true;
	}

	/**
	 * @param	mixed string | array
	 * @return	RouteAccess
	 */
	public function setAclMap(array $map)
	{
		if ($this->isAclForEachMethod()) {
			if ($map === array_values($map)) {
				$err  = "map must be an associative array ";
				throw new DomainException($err);
			}
			foreach ($map as $method => $codes) {
				if (! is_string($method) || empty($method)) {
					$err  = "the method acl codes are mapped to must be a ";
					$err .= "non empty string";
					throw new DomainException($err);
				}
				
				if (! is_array($codes)) {
					$err = "list of codes for -($method) must be an array";
					throw new DomainException($err);
				}
				foreach ($codes as $code) {
					if (! is_string($code) || empty($code)) {
						$err  = "acl code for -($method) must be a non empty ";
						$err .= "string";
						throw new DomainException($err);
					}
				}
			}
		}
		else {
			if ($map !== array_values($map)) {
				$err = "when the acl map applies to all methods it must not ";
				$err = "be an associative array";
				throw new DomainException($err);
			}

			foreach ($map as $code) {
				if (! is_string($code) || empty($code)) {
					$err = "all acl codes must be non empty strings";
					throw new DomainException($err);
				}
			}

		}

		$this->map = $map;
		return $this;
	}

	/**
	 * @return	RouteAccess
	 */
	public function getAclMap()
	{
		return $this->map;
	}
}
