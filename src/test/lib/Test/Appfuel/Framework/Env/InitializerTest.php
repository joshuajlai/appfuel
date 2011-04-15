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
	 * Save the include path
	 * @return null
	 */
	public function setUp()
	{
		$this->backupIncludePath();
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
}

