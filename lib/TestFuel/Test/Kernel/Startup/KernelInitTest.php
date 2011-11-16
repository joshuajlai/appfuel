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
namespace TestFuel\Test\Kernel\Startup;

use StdClass,
	Exception,
	TestFuel\TestCase\FrameworkTestCase,
	Appfuel\Kernel\Startup\KernelInitTask;

/**
 * The abstract task encapsulates the common functionality of all start up 
 * tasks. We will test the ability to get registry keys, the ability to 
 * get the status, 
 */
class KernelInitTaskTest extends FrameworkTestCase
{
	/**
	 * System under test
	 * @var	KernalInitTask
	 */
	protected $task = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->task = new KernelInitTask();
		parent::setUp();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->task = null;
		parent::tearDown();
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Kernel\Startup\StartupTaskInterface',
			$this->task
		);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Startup\StartupTaskAbstract',
			$this->task
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetRegistryKeys()
	{
		$expected = array(
            'include-path-action' => null,
            'include-path'		  => null,
            'enable-autoloader'	  => null,
            'display-errors'	  => null,
            'error-reporting'	  => null,
            'default-timezone'    => null,
        );

		$this->assertEquals($expected, $this->task->getRegistryKeys());
	}
	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultValues()
	{
		$this->assertNull($this->task->execute());

		$expected  = 'initialize: display-errors,error-reporting,include-path,';
		$expected .= 'timezone,autoloader';
		$this->assertEquals($expected, $this->task->getStatus());

		$expected = AF_BASE_PATH . "/lib";
		$this->assertEquals($expected, get_include_path());

		$splFunctions = spl_autoload_functions();
		$expected = end($splFunctions);
		$this->assertInternalType('array', $expected);
		$this->assertArrayHasKey(0, $expected);
		$this->assertArrayHasKey(1, $expected);
		$this->assertInstanceOf(
			'Appfuel\ClassLoader\StandardAutoLoader',
			$expected[0]
		);
		$this->assertEquals('loadClass', $expected[1]);

		$tz = 'America/Los_Angeles';
		$this->assertEquals($tz, date_default_timezone_get());

		$errorLevel = E_ALL|E_STRICT;
		$this->assertEquals($errorLevel, error_reporting());
	
		$this->assertEquals('on', ini_get('display_errors'));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testTimezone()
	{
		$tz = 'America/Los_Angeles';
		/* put the timezone in a known state */
		$this->assertTrue(date_default_timezone_set($tz));

		$params = array('default-timezone' => 'Africa/Abidjan');
		$this->assertNull($this->task->execute($params));

		$this->assertEquals('Africa/Abidjan', date_default_timezone_get());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIncludePath()
	{
		$params = array('include-path' => 'my/path');
		$executeResult = $this->task->execute($params);
		$path = get_include_path();
		$this->restoreIncludePath();
		
		$this->assertNull($executeResult);
		$this->assertEquals('my/path', $path);
	}
	
	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testErrorReporting()
	{
		$params = array('error-reporting' => 'all');
		$this->assertNull($this->task->execute($params));
		$this->assertEquals(E_ALL, error_reporting());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testErrorDisplay()
	{
		$params = array('display-errors' => 'off');
		$this->assertNull($this->task->execute($params));
		$this->assertEquals('off', ini_get('display_errors'));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAutoloaders()
	{
		$this->clearAutoloaders();
		$params = array('enable-autoloader' => false);
		$executeResult = $this->task->execute($params);
		$loaders = spl_autoload_functions();
		$this->restoreAutoloaders();
		
		$this->assertNull($executeResult);
		$this->assertEquals(array(), $loaders);
	}
	
	
}
