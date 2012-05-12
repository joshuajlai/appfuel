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
namespace Appfuel\Orm;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\StartupTask;

/**
 * Assign the domain map which is a listing of domain key => namespace,
 * to the orm manager which is responsible for creating db maps and repo's 
 */
class OrmStartupTask extends StartupTask 
{
	/**
	 * Assign the registry keys to be pulled from the kernel registry
	 * 
	 * @return	OrmStartupTask
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array('orm-domain-map'));
	}

	/**
	 * @param	DbFactoryInterface
	 * @return	DbStartupTask
	 */
	public function setFactory(DbFactoryInterface $factory)
	{
		$this->factory = $factory;
		return $this;
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$mapFile = 'app/domains.php';
		$err  = 'OrmStartupTask failed: ';
		if (isset($params['orm-domain-map'])) {
			$mapFile = $params['orm-domain-map'];
			if (! is_string($mapFile) || empty($mapFile)) {
				$err .= '-(orm-domain-map) must be a non empty string';
				throw new InvalidArgumentException($err);
			}
		}

		if (false !== strpos($mapFile, '../')) {
			$err .= "file path is invalid";
			throw new LogicException($err);
		}

		$file = AF_BASE_PATH . "/$mapFile";
		if (! file_exists($file)) {
			$err .= "could not find -($file)";
			throw new RunTimeException($err);
		}
		$map = require $file;
		if (! is_array($map)) {
			$err = "domain must is not an array";
			throw new LogicException($err);
		}

		OrmManager::setRegisteredDomains($map);
		return "registered " . count($map) . " domains";
	}
}
