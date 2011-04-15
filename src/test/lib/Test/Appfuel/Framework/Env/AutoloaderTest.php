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
namespace Test\Appfuel\Framework\Env;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\Framework\Env\Autoloader;

/**
 * 
 */
class AutoloaderTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var Autoloader
	 */
	protected $loader = NULL;

	/**
	 * Previous state of registered spl autoload functions
	 * @var array
	 */
	protected $splFunctions = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->backupAutoloaders();
		$this->loader = new Autoloader();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->restoreAutoloaders();
		unset($this->loader);
	}

	/**
	 * Remove all existing loaders including phpunit and then call register,
	 * unregister and cache the results and status of the autoloaders. Restore
	 * the previous autoloaders and test the cached results for correctness
	 *
	 * @return NULL
	 */
	public function testRegisterUnregister()
	{
		$this->clearAutoloaders();
		$registerResult  = $this->loader->register();
		$registerLoaders = spl_autoload_functions();
		$unregisterResult  = $this->loader->unregister();
		$unregisterLoaders = spl_autoload_functions();

		/* so phpunit can work again */
		$this->restoreAutoloaders();

		$this->assertTrue(
			$registerResult, 
			'Should have registered successfully'
		);

		$this->assertInternalType('array', $registerLoaders);
		$this->assertArrayHasKey(0, $registerLoaders);

		/* only one registered function */
		$this->assertEquals(1, count($registerLoaders));

		/* because the loader method is part of a class the registered
		 * method is in the form of an array not a string
		 */
		$this->assertInternalType('array', $registerLoaders[0]);
		$this->assertArrayHasKey(0, $registerLoaders[0]);
		$this->assertArrayHasKey(1, $registerLoaders[0]);
		$this->assertEquals(2, count($registerLoaders[0]));

		$this->assertInstanceof(
			'\Appfuel\Framework\Env\Autoloader',
			$registerLoaders[0][0],
			'The registered loader should be an appfuel loader'
		);

		$this->assertEquals(
			'loadClass', 
			$registerLoaders[0][1],
			'the registered method should be loadClass'
		);

		
		$this->assertTrue(
			$unregisterResult,
			'Should have unregistered successfully'
		);

		$this->assertInternalType('array', $unregisterLoaders);
		$this->assertEmpty($unregisterLoaders);
	}

	/**
	 * @return NULL
	 */
	public function testIsLoaded()
	{
		$class = get_class($this);
		$this->assertTrue($this->loader->isLoaded($class));
		
		/* This class was loaded during when the dependencies were loaded */
		$class = '\Appfuel\Dependency';
		$this->assertTrue($this->loader->isLoaded($class));

		/* class known not to exist */
		$class = '\Known\Not\To\Exist';
		$this->assertFalse($this->loader->isLoaded($class));
	}

	/**
	 * In order to test the class loader we have a directory located in the 
	 * same directory as this test class called files. In that directory 
	 * includes the namespace we will try to load. First we clear any 
	 * autoloaders to prevent loading of this file. then we set the include
	 * path to only the directory with the namespace. We prove the file is not
	 * loaded. We then load it and prove with isLoaded that it worked and 
	 * finally restore the include path
	 *
	 * @return NULL
	 */
	public function testLoadClass()
	{
		/* clear the autoloaders so nothing else tries to load this class */
		$this->clearAutoloaders();
		$this->backupIncludePath();
		
		/* prove class does not exist */
		$class = '\AutoloadExample\Instance';
		$this->assertFalse($this->loader->isLoaded($class));

		/* location of example class to find */
		$path = $this->getCurrentPath('files');
		set_include_path($path);

		$this->loader->loadClass($class);
	
		$this->restoreIncludePath();
		$this->restoreAutoloaders();
		$this->assertTrue($this->loader->isLoaded($class));
	}
}
