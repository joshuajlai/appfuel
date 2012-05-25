<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Kernel;

use StdClass,
	Appfuel\Kernel\StartupTask,
	Testfuel\TestCase\BaseTestCase;

/**
 */
class StartupTaskTest extends BaseTestCase
{
	/**
	 * Set of registry keys used in many test cases
	 * @var array
	 */
	protected $taskKeys = array(
		'my-key'   => 'my-default',
		'your-key' => null,
		'our-key'  => true
	);

	/**
	 * @return	array
	 */
	public function getTaskKeys()
	{
		return $this->taskKeys;
	}

	/**
	 * @test
	 * @return StartupTask
	 */
	public function createTaskNoArgs()
	{
		$task = new StartupTask();
		$this->assertInstanceOf(
			'Appfuel\Kernel\StartupTaskInterface',
			$task
		);
		return $task;
	}

	/**
	 * @test
	 * @return StartupTask
	 */
	public function createTaskWithKeys()
	{
		$task = new StartupTask($this->getTaskKeys());
		$this->assertInstanceOf(
			'Appfuel\Kernel\StartupTaskInterface',
			$task
		);

		return $task;	
	}

	/**
	 * @test
	 * @depends	createTaskNoArgs
	 * @return	StartupTask
	 */
	public function getStatusBeforeExecute(StartupTask $task)
	{
		$this->assertNull($task->getStatus());
	}

	/**
	 * @test
	 * @depends	createTaskNoArgs
	 * @return	StartupTask
	 */
	public function keysAreEmptyWhenConstructorIsEmpty(StartupTask $task)
	{
		$this->assertEquals(array(), $task->getRegistryKeys());
		return $task;
	}

	/**
	 * @test
	 * @depends	createTaskWithKeys
	 * @return	null
	 */
	public function keysWhenConstructHasArgs(StartupTask $task)
	{
		$keys = $this->getTaskKeys();
		$this->assertEquals($keys, $task->getRegistryKeys());
	}

	/**
	 * @test
	 * @depends	keysAreEmptyWhenConstructorIsEmpty
	 * @return	StartupTask
	 */
	public function AddRegistryKeyNoDefault(StartupTask $task)
	{
		$key = 'my-key';
		$this->assertSame($task, $task->addRegistryKey($key));

		$expected = array($key => null);
		$this->assertEquals($expected, $task->getRegistryKeys());

		$key = 'other-key';
		$this->assertSame($task, $task->addRegistryKey($key));

		$expected[$key] = null;
		$this->assertEquals($expected, $task->getRegistryKeys());

		return $task;
	}

	/**
	 * @test
	 * @depends	AddRegistryKeyNoDefault
	 * @return	null
	 */
	public function clearKeyRegistry(StartupTask $task)
	{
		$this->assertSame($task, $task->clearRegistryKeys());
		$this->assertEquals(array(), $task->getRegistryKeys());

		return $task;
	}


	/**
	 * @test
	 * @depends	clearKeyRegistry
	 * @return	StartupTask
	 */
	public function AddRegistryKeyWithDefault(StartupTask $task)
	{
		$key = 'my-key';
		$default = 12345;
		$this->assertSame($task, $task->addRegistryKey($key, $default));

		$expected = array($key => $default);
		$this->assertEquals($expected, $task->getRegistryKeys());

		$key = 'other-key';
		$default = 'my custom default';
		$this->assertSame($task, $task->addRegistryKey($key, $default));

		$expected[$key] = $default;
		$this->assertEquals($expected, $task->getRegistryKeys());

		$key = 'another-key';
		$default = null;
		$this->assertSame($task, $task->addRegistryKey($key, $default));

		$expected[$key] = $default;
		$this->assertEquals($expected, $task->getRegistryKeys());


		return $task;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return			null
	 */
	public function addRegistryKeyFailures($key)
	{
		$msg = 'label must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$task = new StartupTask();
		$task->addRegistrykey($key);
	}

	/**
	 * Load always appends onto the existing list
	 * @test
	 * @depends	clearKeyRegistry
	 * @return	StartupTask
	 */
	public function LoadRegistryKeysNoDefault(StartupTask $task)
	{
		$task->clearRegistryKeys();
		$keys = array('my-key', 'your-key', 'our-key');
		$this->assertSame($task, $task->loadRegistryKeys($keys));
		
		$expected = array(
			'my-key'  => null,
			'your-key' => null,
			'our-key'  => null
		);
		$this->assertEquals($expected, $task->getRegistryKeys());

		$more = array('key-a', 'key-b');
		$this->assertSame($task, $task->loadRegistryKeys($more));
		
		$expected['key-a'] = null;
		$expected['key-b'] = null;
		$this->assertEquals($expected, $task->getRegistryKeys());
		

		$this->assertSame($task, $task->loadRegistryKeys(array()));
		$this->assertEquals($expected, $task->getRegistryKeys());
		return $task;
	}

	/**
	 * @test
	 * @depends	clearKeyRegistry
	 * @return	StartupTask
	 */
	public function LoadRegistryKeysWithDefault(StartupTask $task)
	{
		$task->clearRegistryKeys();
		$keys = array(
			'my-key'   => 'value-a', 
			'your-key' => 12345, 
			'our-key'  => false
		);
		$this->assertSame($task, $task->loadRegistryKeys($keys));
		$this->assertEquals($keys, $task->getRegistryKeys());

		$more = array('key-a' => 1.23, 'key-b' => new StdClass());
		$this->assertSame($task, $task->loadRegistryKeys($more));
		
		$keys['key-a'] = 1.23;
		$keys['key-b'] = new StdClass();
		$this->assertEquals($keys, $task->getRegistryKeys());

		return $task;
	}

	/**
	 * @test
	 * @depends	createTaskNoArgs
	 * @return	StartupTask
	 */
	public function setRegistryKeys(StartupTask $task)
	{
		$task->clearRegistryKeys();
		$keys = array(
			'my-key'   => 'value-a', 
			'your-key' => 12345, 
			'our-key'  => false
		);
		$task->loadRegistryKeys($keys);
		
		$new = array('key-a' => 'value-a', 'key-b' => 'value-b');
		$this->assertSame($task, $task->setRegistryKeys($new));
		$this->assertEquals($new, $task->getRegistryKeys());

		return $task;
	}

	/**
	 * StartupTask is treated like an abstract class. The reason I did not use
	 * an abstract class is because I wanted the power of an interface and in
	 * php you can not have an abstract method and interface method declared.
	 *
	 * @test
	 * @depends	createTaskNoArgs
	 * @return	null
	 */
	public function executeShouldFail(StartupTask $task)
	{
		$msg = 'execute method must be extended';
		$this->setExpectedException('LogicException', $msg);
		$task->execute();
	}

	/**
	 * @test
	 * @depends	createTaskNoArgs
	 * @return	null
	 */
	public function kernelExecuteShouldFail(StartupTask $task)
	{
		$msg = 'execute method must be extended';
		$this->setExpectedException('LogicException', $msg);
		
		$ns = 'Appfuel\Kernel\Mvc';
		$route = $this->getMock("$ns\MvcRouteDetailInterface");
		$context = $this->getMock("$ns\MvcContextInterface");
		$task->kernelExecute(array(), $route, $context);
	}



	/**
	 * Allows only string even empty ones but
	 * @return	null
	 */
	public function Status()
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
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function Satus_Failures($status)
	{
		$this->task->setStatus($status);
	}


	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function addRegistryKeyWithDefaults()
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
	 * @return				null
	 */
	public function AddRegistryKey_EmptyStringFailures()
	{
		$this->task->addRegistrykey('');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @return				null
	 */
	public function AddRegistryKey_EmptyStringWhiteSpacesFailures()
	{
		$this->task->addRegistrykey("   \t\n   ");
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function SetRegistryKeysNoDefaults()
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
	public function SetRegistryWithDefaults()
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
