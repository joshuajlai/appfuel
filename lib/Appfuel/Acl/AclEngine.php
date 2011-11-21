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
namespace Appfuel\Acl;

use InvalidArgumentException;

/**
 * The acl role is an immutable value object that defines an authority level
 */
class AclEngine implements AclEngineInterface
{
	/**
	 * The while holds a mapping of role codes to resources that code has
	 * access to.
	 * @var array
	 */
	protected $map = array();

	/**
	 * @param	array $map	
	 * @return	AclEngine
	 */
	public function __construct(array $map)
	{	
		if ($map === array_values($map)) {
			$err  = 'the acl map must be an associative array of role codes ';
			$err .= 'that hold a list of resources they are allowed access to';
			throw new InvalidArgumentException($err);
		}

		foreach ($map as $code => $whitelist) {
			if (empty($code) || !is_string($code)) {
				$err = 'acl mapp error: role codes must be non empty string';
				throw new InvalidArgumentException($err);
			}
			
			if (! is_array($whitelist)) {
				$err = 'acl mapp error: resource whitelist must be an array';
				throw new InvalidArgumentException($err);

			}
		}

		$this->map = $map;
	}

	/**
	 * @param	string	$code	the aclRoleCode
	 * @return	bool
	 */
	public function isAllowed($code, $resource)
	{
		if (empty($code) || !is_string($code) || ! isset($this->map[$code])) {
			return false;
		}

		$map = $this->map[$code];
		if (empty($map) || !is_array($map)) {
			return false;
		}

		if (!is_string($resource) || !in_array($resource, $map, true)) {
			return false;
		}

		return true;
	}
}
