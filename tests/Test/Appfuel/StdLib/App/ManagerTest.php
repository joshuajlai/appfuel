<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @category 	Tests
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Test\Appfuel\StdLib\Filesystem\Manager;

/*
 * Autoloading has not been established so we need to manaully 
 * include this file
 */
require_once 'Appfuel/StdLib/App/Manager.php';

require_once 'Appfuel/StdLib/Autoloader/LoaderInterface.php';
require_once 'Appfuel/StdLib/Autoloader/Classloader.php';

/* import */
use Appfuel\StdLib\App\Manager				as AppManager;
use Appfuel\StdLib\Autoloader\Classloader 	as Autoloader;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test getAutoloader setAutoloader isAutoloader clearAutoloader
	 *
	 * Assert:	initially isAutoloader returns FALSE
	 * Assert:	initially getAutoloader returns NULL
	 * Assert:	setAutoaloader returns NULL AND
	 * 			isAutoloader now returns TRUE AND
	 * 			getAutoloader now returns the autoloader set
	 * Assert: 	clearAutoloader returns NULL AND 
	 * 			isAutoloader returns FALSE AND
	 * 			getAutoloader returns NULL
	 */
	public function testGetSetIsClearAutoloader()
	{
		$this->assertFalse(AppManager::isAutoloader());
		$this->assertNull(AppManager::getAutoloader());

		$loader = $this->getMock('\Appfuel\StdLib\Autoloader\Classloader');
		$this->assertNull(AppManager::setAutoloader($loader));
		$this->assertTrue(AppManager::isAutoloader());
		$this->assertSame($loader, AppManager::getAutoloader());

		$this->assertNull(AppManager::clearAutoloader());
		$this->assertFalse(AppManager::isAutoloader());
		$this->assertNull(AppManager::getAutoloader());
	}

	/**
	 * Test enableAutoloader disableAutoloader
	 *
	 * Assert: 	enableAutoloader returns NULL AND
	 * 			isAutoloader returns TRUE AND
	 * 			getAutoloader returns an object of type 
	 * 			\Appfuel\Autoloader\Classloader AND
	 * 			that autoloaders method has been registered
	 * Assert: 	disableAutoloader returns NULL AND
	 * 			the autoloader method is no longer registered
	 */
	public function testEnableDisableAutoloader()
	{

		$registeredItems = spl_autoload_functions();
		foreach ($registeredItems as $item) {
			if (is_string($item)) {
				spl_autoload_unregister($item);
			} else if (is_array($item)) {
				spl_autoload_unregister(array($item[0],$item[1]));
			}
		}

		/* need this for the test */
		spl_autoload_register('phpunit_autoload');

		$alFunctions = spl_autoload_functions();
		$this->assertTrue(is_array($alFunctions));
		$this->assertEquals(1, count($alFunctions));

		$this->assertNull(AppManager::enableAutoloader());
		$this->assertTrue(AppManager::isAutoloader());
		$loader = AppManager::getAutoloader();
		$this->assertType(
			'\Appfuel\StdLib\Autoloader\Classloader',
			$loader
		);


		/* prove autoloader was added */
		$alFunctions = spl_autoload_functions();
		$this->assertTrue(is_array($alFunctions));
		$this->assertEquals(2, count($alFunctions));


		$newFunctions = end($alFunctions);	
		//echo "\n", print_r($rMethods,1), "\n";exit;	
		$this->assertType(
			'\Appfuel\StdLib\Autoloader\Classloader',
			$newFunctions[0]
		);

		/* prove the default method is registered */
		$this->assertContains($loader->getRegisteredMethod(), $newFunctions[1]);


		$this->assertNull(AppManager::disableAutoloader());
		
		$alFunctions = spl_autoload_functions();
		$this->assertTrue(is_array($alFunctions));
		$this->assertEquals(1, count($alFunctions));

		$this->assertEquals('phpunit_autoload', current($alFunctions));

		/* restore the autoloader for normal operations */
		AppManager::enableAutoloader();
	}
}

