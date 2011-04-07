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
 * 
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
		$this->basePath    = $this->getBasePath();
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
	 * Test to ensure we can initialize data into the registery
	 *
	 * @return null
	 */
	public function xtestInitRegistry()
	{
		Registry::init();
		$this->assertEquals(0, Registry::count());

		Initializer::initRegistry(array());	
		$this->assertEquals(0, Registry::count());

		$data = array(
			'label_1' => 'value_1',
			'label_2' => 'value_2'
		);

		Initializer::initRegistry($data);	
		$this->assertEquals(2, Registry::count());

		$this->assertEquals($data['label_1'], Registry::get('label_1'));
		$this->assertEquals($data['label_2'], Registry::get('label_2'));
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

	/**
	 * Test initializing with the registry. We create a new intializer
	 * because new factory will be create from those registry settings
	 *
	 */
	public function xtestInitFromRegistry()
	{
		/* clear out any objects set during initialization */
		$initializer = new Initializer($this->basePath);
		Registry::add('app_factory', '\Appfuel\Framework\AppFactory');
		
		$paths = array('path_1', 'path_2');
		Registry::add('include_path', $paths);
		Registry::add('include_path_action', 'replace');

		Registry::add('display_error', 1);
		Registry::add('error_reporting', 'all');

		
		$result = $initializer->initFromRegistry();

		$includePath    = get_include_path();
		$displayError   = ini_get('display_errors');
		$errorReporting = error_reporting();

		/* 
		 * must restore the settings so we can test the results 
		 * we backed up the actual registry so we can restore 
		 */
		$this->restoreAppfuelSettings();	
		$expectedInclude = 'path_1' . PATH_SEPARATOR . 'path_2';
		$this->assertEquals($expectedInclude, $includePath);	
		$this->assertEquals(1, $displayError);
		$this->assertEquals(E_ALL, $errorReporting);
	
		$this->assertInstanceOf(
			'\\Appfuel\\Framework\\AppFactoryInterface',
			$initializer->getFactory()
		);
	}
}

