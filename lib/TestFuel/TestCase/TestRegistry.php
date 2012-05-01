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
namespace TestFuel\TestCase;

use InvalidArgumentException,
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\Kernel\KernelStateInterface;

/**
 * Holds global state for unittest.
 */
class TestRegistry
{
	/**
	 * Used to restore include paths, error settings, timezone etc..
	 * @var array
	 */
	static protected $kernelState = null;

	/**
	 * Used to backup kernel registry parameters
	 * @var var
	 */
	static protected $params = array();

	/**
	 * Used to backup domain map initialized during framework configuration
	 * @var array
	 */
	static protected $domains = array();

	/**
	 * Used to backup route map initialized during framework configuration
	 * @var array
	 */
	static protected $routes = array();

	/**
	 * @return	KernelStateInterface
	 */
	static public function getKernelState()
	{
		return self::$kernelState;
	}

	/**
	 * @param	KernelStateInterface $state
	 * @return	null
	 */
	static public function setKernelState(KernelStateInterface $state)
	{
		self::$kernelState = $state;
	}

	/**
	 * @return	array
	 */
	static public function getKernelParams()
	{
		return	self::$params;
	}

	/**
	 * @param	array	$params	
	 * @return	null
	 */
	static public function setKernelParams(array $params)
	{
		self::$params = $params;
	}

	/**
	 * @return null
	 */
	static public function clearKernelParams()
	{
		self::$params = array();
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setKernelDomainMap(array $map)
	{
		self::$domains = $map;
	}
	
	/**
	 * @return	array
	 */
	static public function getKernelDomainMap()
	{
		return	self::$domains;
	}

	/**
	 * @return null
	 */
	static public function clearKernelDomainMap()
	{
		self::$domains = array();
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setKernelRouteMap(array $map)
	{
		self::$routes = $map;
	}
	
	/**
	 * @return	array
	 */
	static public function getKernelRouteMap()
	{
		return	self::$routes;
	}

	/**
	 * @return null
	 */
	static public function clearKernelRouteMap()
	{
		self::$routes = array();
	}

	/**
	 * @return	null
	 */
	static public function clear()
	{
		self::clearKernelParams();
		self::clearKernelDomainMap();
		self::clearKernelRouteMap();
	}

}
