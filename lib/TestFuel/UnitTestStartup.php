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
namespace TestFuel;

use TestFuel\TestCase\TestRegistry,
	Appfuel\Kernel\KernelState,
	Appfuel\Kernel\KernelRegistry,
	Appfuel\Kernel\Startup\StartupTaskAbstract;

/**
 * This startup startegy ensures the most strict error settrings, 
 * backs up the kernel registry and sets the path finder
 */
class UnitTestStartup extends StartupTaskAbstract 
{
	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		/* regardless of the kernel settings unit tests
		 * should have full error reporting and they should be displayed
		 */
	    error_reporting(E_ALL | E_STRICT);
        ini_set('error_diplay', 'on');
		
		$params  = KernelRegistry::getParams(); 
		$routes  = KernelRegistry::getRouteMap();
		$domains = KernelRegistry::getDomainMap();
		$state  = new KernelState();
		TestRegistry::setKernelState($state);
		TestRegistry::setKernelParams($params);
		TestRegistry::setKernelRouteMap($routes);
		TestRegistry::setKernelDomainMap($domains);	

		$this->setStatus('appfuel unittest: initialized');
	}
}	
