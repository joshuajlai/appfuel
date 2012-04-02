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
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 */
class PkgName
{
	/**
	 * @var string
	 */
	protected $vendor = null;

	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @param	array	$files
	 * @return	FileStack
	 */
	public function __construct($str, $default = null)
	{
		if (! is_string($str) || empty($str)) {
			$err = "package name must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		if (null !== $default && ! is_string($default)) {
			$err = "default vendor name must be a none empty string";
			throw new InvalidArgumentException($err);
		}

		$parts = explode(':', $str);
		$vendor = $default;
		if (1 === count($parts)) {
			$name = current($parts);
		}
		else {
			$vendor = current($parts);
			$name   = next($parts);
		}

		if (empty($vendor) || empty($name)) {
			$err = "vendor and package names can not be empty";	
			throw new InvalidArgumentException($err);
		}
		$this->vendor = $vendor;
		$this->name   = $name;
	}

	/**
	 * @return	string
	 */
	public function getVendor()
	{
		return $this->vendor;
	}

	/**
	 * @return	array
	 */
	public function getName()
	{
		return $this->name;
	}
}
