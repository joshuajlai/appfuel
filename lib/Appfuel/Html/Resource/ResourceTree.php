<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use DomainException,
	InvalidArgumentException;

/**
 * Pulls info about vendors, packages, and layers out of the resource tree
 */
class ResourceTree
{
	/**
	 * @var array
	 */
	static protected $tree = array();

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isVendor($key)
	{
		if (is_string($key) && isset(self::$tree[$key])) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$vendor
	 * @return	string | false 
	 */
	static public function getVersion($vendor)
	{
		if (! self::isVendor($vendor) || 
			! isset(self::$tree[$vendor]['version'])) {
			return false;
		}

		return self::$tree[$vendor]['version'];
	}

	/**
	 * @param	string	$vendor
	 * @return	string
	 */
	static public function getPath($vendor)
	{
		if (! self::isVendor($vendor) || !isset(self::$tree[$vendor]['path'])) {
			return false;
		}

		return self::$tree[$vendor]['path'];
	}

	/**
	 * @param	string	$vendor
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isLayer($vendor, $name)
	{
		if (! is_string($name) || 
			! self::isVendor($vendor) || 
			! isset(self::$tree[$vendor]['layers']) ||
			! isset(self::$tree[$vendor]['layers'][$name])) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string	$vendor
	 * @param	string	$name
	 * @return	array | false
	 */
	static public function getLayer($vendor, $name)
	{
		if (! self::isLayer($vendor, $name)) {
			return false;
		}

		$result = self::$tree[$vendor]['layers'][$name];
		
		/* since this array will be used in a value object we will add back
		 * the info used to create heirarchical structure
		 */
		$result['vendor'] = $vendor;
		$result['name']   = $name;
		
		return $result;
	}

	/**
	 * @param	string	$vendor
	 * @param	string	$name
	 * @return	bool
	 */
	static public function isPackage($vendor, $name)
	{
		if (! is_string($name) || 
			! self::isVendor($vendor) || 
			! isset(self::$tree[$vendor]['packages']) ||
			! isset(self::$tree[$vendor]['packages'][$name])) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string	$vendor
	 * @param	string	$name
	 * @return	array | false
	 */
	static public function getPackage($vendor, $name)
	{
		if (! self::isPackage($vendor, $name)) {
			return false;
		}

		$result = self::$tree[$vendor]['packages'][$name];
		if (! isset($result['name'])) {
			$result['name'] = $name;
		}
		
		return $result;
	}

	/**
	 * @param	string	$vendor
	 * @param	string	$type
	 * @return	bool
	 */
	static public function isPackageType($vendor, $type)
	{
		if (! is_string($type) ||
			! self::isVendor($vendor) ||
			! isset(self::$tree[$vendor]['packages'][$type])) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string	$vendor
	 * @param	string	$type
	 * @param	string	$name
	 * @return	array | false
	 */
	static public function getPackageByType($vendor, $type, $name)
	{
		if (! self::isPackageType($vendor, $type) ||
			! isset(self::$tree[$vendor]['packages'][$type][$name])) {
			return false;
		}

		return self::$tree[$vendor]['packages'][$type][$name];
	}

	/**
	 * @return	bool
	 */
	static public function isTree()
	{
		return count(self::$tree) > 0;
	}

	/**
	 * @return	array
	 */
	static public function getTree()
	{
		return self::$tree;
	}

	/**
	 * @param	array	$tree
	 * @return	null
	 */
	static public function setTree(array $tree)
	{
		self::$tree = $tree;
	}

	/**
	 * @return	string
	 */
	static public function getSeparator()
	{
		return self::$sep;
	}

	/**
	 * @param	string	$char
	 * @return	null
	 */
	static public function setSeparator($char)
	{
		if (! is_string($char) || empty($char)) {
			$err = "array separator must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		self::$sep = $char;
	}
}
