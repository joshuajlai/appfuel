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
namespace TestFuel\Unit\Kernel\Startup;

use StdClass,
	Exception,
	TestFuel\TestCase\BaseTestCase;

/**
 * The abstract task encapsulates the common functionality of all start up 
 * tasks. We will test the ability to get registry keys, the ability to 
 * get the status, 
 */
class StartupTaskAbstractTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	AbstractStartupTask
	 */
	protected $task = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$class = 'Appfuel\Kernel\Startup\StartupTaskAbstract';
		$this->task = $this->getMockForAbstractClass($class);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->task = null;
	}

	/**
	 * @return	array
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(true),
			array(false),
			array(12345),
			array(0),
			array(1),
			array(-1),
			array(1.234),
			array(array()),
			array(array(1,2,3)),
			array(new StdClass())
		);
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
	}

	/**
	 * Allows only string even empty ones but
	 * @depends	testInterface
	 * @return	null
	 */
	public function testStatus()
	{
		$this->assertNull($this->task->getStatus());
		
		$status = 'db initialized';
		$this->assertNull($this->task->setStatus($status));
		$this->assertEquals($status, $this->task->getStatus());

		$status = '';
		$this->assertNull($this->task->setStatus($status));
		$this->assertEquals($status, $this->task->getStatus());	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @provides			provideInvalidStrings
	 * @return				null
	 */
	public function testSatus_Failures($status)
	{
		$this->task->setStatus($status);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddRegistryKeyNoDefault()
	{
		$this->assertEquals(array(), $this->task->getRegistryKeys());
		
		$key1 = 'my-key';
		$this->assertNull($this->task->addRegistryKey($key1));
		$expected = array($key1 => null);
		$this->assertEquals($expected, $this->task->getRegistryKeys());
	
		$key2 = 'my-other-key';
		$this->assertNull($this->task->addRegistryKey($key2));
		$expected = array($key1 => null, $key2 => null);
		$this->assertEquals($expected, $this->task->getRegistryKeys());
	
		$key3 = 'my-final-key';
		$this->assertNull($this->task->addRegistryKey($key3));
		$expected = array($key1 => null, $key2 => null, $key3 => null);
		$this->assertEquals($expected, $this->task->getRegistryKeys());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddRegistryKeyWithDefault()
	{
		$this->assertEquals(array(), $this->task->getRegistryKeys());
		
		$key1	  = 'my-key';
		$default1 = 'my-default';
		$this->assertNull($this->task->addRegistryKey($key1, $default1));
		$expected = array($key1 => $default1);
		$this->assertEquals($expected, $this->task->getRegistryKeys());
	
		$key2		= 'my-other-key';
		$default2	= 12345;
		$this->assertNull($this->task->addRegistryKey($key2, $default2));
		$expected[$key2] = $default2;
		$this->assertEquals($expected, $this->task->getRegistryKeys());
	
		$key3	  = 'key3';
		$default3 = 1.2345;
		$this->assertNull($this->task->addRegistryKey($key3, $default3));
		$expected[$key3] = $default3;
		$this->assertEquals($expected, $this->task->getRegistryKeys());

		$key4	  = 'key4';
		$default4 = array(1,2,3);
		$this->assertNull($this->task->addRegistryKey($key4, $default4));
		$expected[$key4] = $default4;
		$this->assertEquals($expected, $this->task->getRegistryKeys());

		$key5	  = 'key4';
		$default5 = array(1,2,3);
		$this->assertNull($this->task->addRegistryKey($key5, $default5));
		$expected[$key5] = $default5;
		$this->assertEquals($expected, $this->task->getRegistryKeys());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @provides			provideInvalidStrings
	 * @return				null
	 */
	public function testAddRegistryKey_Failures($key)
	{
		$this->task->addRegistrykey($key);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testAddRegistryKey_EmptyStringFailures()
	{
		$this->task->addRegistrykey('');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testAddRegistryKey_EmptyStringWhiteSpacesFailures()
	{
		$this->task->addRegistrykey("   \t\n   ");
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRegistryKeysNoDefaults()
	{
		$keys = array('my-key', 'your-key', 'their-key');
		$this->assertNull($this->task->setRegistryKeys($keys));

		$expected = array('my-key'=>null, 'your-key'=>null, 'their-key'=>null);
		$this->assertEquals($expected, $this->task->getRegistryKeys());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRegistryWithDefaults()
	{
		$keys = array(
			'my-key'	=> 12345, 
			'your-key'	=> 'my result', 
			'their-key'	=> new StdClass()
		);
		$this->assertNull($this->task->setRegistryKeys($keys));
		$this->assertEquals($keys, $this->task->getRegistryKeys());
	}
}
