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
namespace Appfuel\Kernel;

use DomainException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Kernel\Mvc\MvcRouteManager;

/**
 * 
 */
class RouteListTask extends StartupTask 
{
	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$reader = new FileReader(new FileFinder('app'));
		$map = $reader->import('routes.php', true);
		MvcRouteManager::setRouteMap($map);

		$total = count($map);
		$this->setStatus("route map set with -($total) items");
	}
}
