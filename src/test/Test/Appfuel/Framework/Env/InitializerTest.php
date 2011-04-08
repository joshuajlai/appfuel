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

use Test\AfTestCase					  as ParentTestCase,
	Appfuel\Stdlib\Filesystem\Manager as FileManager,
	Appfuel\Registry,
	Appfuel\Framework\Env\Initializer;

/**
 * The initializer is responsible for configuration of errors: if they show,
 * and at what level, php include_path, enabling the autoloader, and
 * default timezone. As more configurations are needed they will be added.
 */
class InitializerTest extends ParentTestCase
{
	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = NULL;

	/**
	 * Save the include path and registry settings
	 * @return null
	 */
	public function setUp()
	{
		$this->backupIncludePath();
		$this->basePath = $this->getBasePath();
	}

	/**
	 * Restore the include path and registry settings
	 * @return null
	 */
	public function tearDown()
	{
		$this->restoreIncludePath();
		$this->restoreAppfuelSettings();
	}

	/**
	 * This method will try to append the base path onto to path you give
	 * it to get the full path to the config. Reason for this is to allow 
	 * developers to never care where the base path is and how to get it.
	 *
	 * @return null
	 */
	public function testGetConfigData()
	{
		/* relative path to config file */
		$rel  = 'files' . DIRECTORY_SEPARATOR . 'config.ini';
		$path = $this->getCurrentPath($rel);

		$result = Initializer::getConfigData($path);
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('label_1', $result);
		$this->assertArrayHasKey('label_2', $result);
		$this->assertArrayHasKey('label_3', $result);

		$this->assertEquals('value_1', $result['label_1']);
		$this->assertEquals('value_2', $result['label_2']);
		$this->assertEquals('value_3', $result['label_3']);
	}

	/**
	 * @expectedException	\Appfuel\Framework\Exception
	 */
	public function testGetConfigDataFileDoesNotExist()
	{
		/* relative path to config file */
		$path = 'test' . DIRECTORY_SEPARATOR . 'asdasdasd';

		$result = Initializer::getConfigData($path);
	}

	/**
	 * This is a wrapper for Appfuel\Framework\Env\Timezone. We 
	 * will be testing the side effect.
	 *
	 * @return null
	 */
	public function testInitTimezone()
	{
		$this->assertTrue(date_default_timezone_set('Antarctica/Casey'));

		$timezone = 'Arctic/Longyearbyen';
		$this->assertTrue(Initializer::initDefaultTimezone($timezone));
		$this->assertEquals($timezone, date_default_timezone_get());

		$this->assertFalse(Initializer::initDefaultTimezone('blahs'));
		$this->assertEquals($timezone, date_default_timezone_get());
	}

	/**
	 * This is a wrapper for the Appfuel\Framework\Env\Autoloader
	 *
	 * @return	null
	 */	
	public function testInitAutoloader()
	{
		$this->clearAutoloaders();
		$loaders = spl_autoload_functions();
		$this->assertInternalType('array', $loaders);
		$this->assertEmpty($loaders);

		$this->assertNull(Initializer::initAutoloader());
		$loaders = spl_autoload_functions();
		$this->assertInternalType('array', $loaders);
		$this->assertEquals(1, count($loaders));

		$loader = current($loaders);
		$this->assertInternalType('array', $loader);
		$this->assertEquals(2, count($loader));

		$this->assertInstanceof(
			'Appfuel\Framework\Env\Autoloader',
			$loader[0]
		);

		$this->assertEquals('loadClass', $loader[1]);
	}

	/**
	 * This is a wrapper for Appfuel\Framework\Env\PHPError
	 *
	 * @return null
	 */
	public function testInitPHPError()
	{
		ini_set('display_errors', 0);
		$this->assertEquals(0, ini_get('display_errors'));
		
		error_reporting(0);
		$this->assertEquals(0, error_reporting());

		$this->assertNull(Initializer::initPHPError(1, 'all'));
		$this->assertEquals(1, ini_get('display_errors'));
		$this->assertEquals(E_ALL, error_reporting());

		/* 
		 * the display_errors wont change when null is given
		 * as a parameter
		 */
		Initializer::initPHPError(1, 'parse');
		$this->assertEquals(1, ini_get('display_errors'));
		$this->assertEquals(E_PARSE, error_reporting());

		/*
		 * the error_reporting wont change when null is given
		 */
		Initializer::initPHPError(0, NULL);
		$this->assertEquals(0, ini_get('display_errors'));
		$this->assertEquals(E_PARSE, error_reporting());

		/*
		 * when both are not changes nothing happens
		 */
		Initializer::initPHPError(0, NULL);
		$this->assertEquals(0, ini_get('display_errors'));
		$this->assertEquals(E_PARSE, error_reporting());
	
	}

	/**
	 * This is a wrapper arround Appfuel\Framework\Env\IncludePath
	 *
	 * @return null
	 */
	public function testInitIncludePathDefaultAction()
	{
		$oldInclude = get_include_path();
		$paths = array(
			'my_path',
			'your_path'
		);

		/* 
		 * when the second parameter is ommited the action performed with
		 * the paths is to replace the old include path with the paths given
		 */
		$result     = Initializer::initIncludePath($paths);
		$expected   = $paths[0] . PATH_SEPARATOR . $paths[1];
		$newInclude = get_include_path();

		/* 
		 * we always need the include path for phpunit be for we test 
		 */
		$this->restoreIncludePath();

		/* init returns the old include path or false on failure */
		$this->assertEquals($oldInclude, $result);
		$this->assertEquals($expected, $newInclude);
	}

	/**
	 * Test the wrapper with an append action
	 *
	 * @return null
	 */
	public function testInitIncludePathAppendAction()
	{
		$oldInclude = get_include_path();
		$paths = array(
			'my_path',
			'your_path'
		);

		$result   = Initializer::initIncludePath($paths, 'append');
		$expected = $oldInclude . PATH_SEPARATOR .
				    $paths[0]	. PATH_SEPARATOR . 
				    $paths[1];
		$newInclude = get_include_path();

		/* 
		 * we always need the include path for phpunit be for we test 
		 */
		$this->restoreIncludePath();

		/* init returns the old include path or false on failure */
		$this->assertEquals($oldInclude, $result);
		$this->assertEquals($expected, $newInclude);
	}

	/**
	 * Test the wrapper with an prepend action
	 *
	 * @return null
	 */
	public function testInitIncludePathPrependAction()
	{
		$oldInclude = get_include_path();
		$paths = array(
			'my_path',
			'your_path'
		);

		$result   = Initializer::initIncludePath($paths, 'prepend');
		$expected = $paths[0]   . PATH_SEPARATOR . 
				    $paths[1]   . PATH_SEPARATOR .
					$oldInclude;

		$newInclude = get_include_path();

		/* 
		 * we always need the include path for phpunit be for we test 
		 */
		$this->restoreIncludePath();

		/* init returns the old include path or false on failure */
		$this->assertEquals($oldInclude, $result);
		$this->assertEquals($expected, $newInclude);
	}



	/**
	 * This method will try to append the base path onto to path you give
	 * it to get the full path to the config. Reason for this is to allow 
	 * developers to never care where the base path is and how to get it.
	 *
	 * @return null
	 */
	public function xtestInitRegistryWithConfig()
	{
		/* clear out the registry */
		Registry::init();

		/* relative path to config file */
		$path = 'test' . DIRECTORY_SEPARATOR . 
				FileManager::classNameToDir(get_class($this)) 
				. DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR 
				. 'config.ini';

		$result = $this->initializer->initRegistryWithConfig($path);
		$this->assertNull($result);
		$this->assertEquals('value_1', Registry::get('label_1'));
		$this->assertEquals('value_2', Registry::get('label_2'));
		$this->assertEquals('value_3', Registry::get('label_3'));
	}
}

