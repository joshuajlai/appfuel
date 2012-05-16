<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Unit\Kernel;

use StdClass,
	Appfuel\Kernel\TaskHandler,
	Appfuel\Kernel\Mvc\MvcContext,
	Appfuel\Config\ConfigRegistry,
	Testfuel\TestCase\BaseTestCase,
	Testfuel\Functional\Kernel\Startup\TestTaskA,
	Testfuel\Functional\Kernel\Startup\TestTaskB;
	

class TaskHandlerTest extends BaseTestCase
{
	/**
	 * Backup the existing status list
	 * @var array
	 */
	protected $statusBackup = array();

	/**
	 * Backup the existing config
	 * @var array
	 */
	protected $configBackup = array();

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->statusBackup = TaskHandler::getStatusList();
		TaskHandler::clearStatusList();
		
		$this->configBackup = ConfigRegistry::getAll();
		ConfigRegistry::clear();
	}


	/**
	 * @return	null
	 */
	public function tearDown()
	{
		TaskHandler::clearStatusList();
		foreach ($this->statusBackup as $key => $msg) {
			TaskHandler::addStatus($key, $msg);
		}

		ConfigRegistry::clear();
		ConfigRegistry::setAll($this->configBackup);
	}

    /**
     * @return  array
     */
    public function provideInvalidStrings()
    {
        return array(
            array(0),
            array(1),
            array(100),
            array(-1),
            array(-100),
            array(1.2),
            array(array()),
            array(array(1,2,3)),
            array(new StdClass())
        );
    }

	/**
	 * @test
	 * @return	null
	 */
	public function addGetStausGetClearStatusList()
	{
		$this->assertEquals(array(), TaskHandler::getStatusList());

		$key1 = 'my-class';
		$msg  = 'my status message';
		$this->assertNull(TaskHandler::addStatus($key1, $msg));
		$this->assertEquals($msg, TaskHandler::getStatus($key1));

		$expected = array($key1 => $msg);
		$this->assertEquals($expected, TaskHandler::getStatusList());

		$key2 = 'my-other-class';
		$msg2  = 'my other status message';
		$this->assertNull(TaskHandler::addStatus($key2, $msg2));
		$this->assertEquals($msg2, TaskHandler::getStatus($key2));

		$expected[$key2] = $msg2;
		$this->assertEquals($expected, TaskHandler::getStatusList());

		$this->assertNull(TaskHandler::clearStatusList());
		$this->assertEquals(array(), TaskHandler::getStatusList());

	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @param			mixed	$key
	 * @return	null
	 */
	public function addStatusInvalidKeyFailure($key)
	{
		$this->setExpectedException('InvalidArgumentException');
		TaskHandler::addStatus($key, 'my message');
	}

	/**
	 * @test
	 * @return	null
	 */
	public function addStatusEmptyKeyFailure()
	{
		$this->setExpectedException('InvalidArgumentException');
		TaskHandler::addStatus('', 'my message');
	}

	/**
	 * @test
	 * @return	TaskHandler
	 */
	public function createTaskHandler()
	{
		$handler = new TaskHandler();
		$this->assertInstanceOf(
			'Appfuel\Kernel\TaskHandlerInterface',
			$handler
		);

		return $handler;
	}

	/**
	 * @test
	 * @depends	createTaskHandler
	 * @return	null
	 */
	public function getTasksFromRegistryWhenEmpty(TaskHandler $handler)
	{
		$tasks = ConfigRegistry::get('startup-tasks', array());
		$this->assertEquals(array(), $tasks);

		$result = $handler->getTasksFromRegistry();
		$this->assertEquals(array(), $result);
	}

	/**
	 * @test
	 * @depends	createTaskHandler
	 * @return	null
	 */
	public function getTasksFromRegistry(TaskHandler $handler)
	{
		$tasks = array('my-class', 'your-class', 'our-class');
		ConfigRegistry::add('startup-tasks', $tasks);
		$result = $handler->getTasksFromRegistry();
		$this->assertEquals($tasks, $result);
	}

	/**
	 * @test
	 * @depends	createTaskHandler
	 * @return	null
	 */
	public function collectFromRegistry(TaskHandler $handler)
	{
		$data = array(
			'param-a' => 'value-a',
			'param-b' => 'value-b',
			'param-c' => 'value-c',
		);
		ConfigRegistry::setAll($data);
		
		$collect = array(
			'param-a' => null,
			'param-c' => null,
			'param-x' => 12345,
			'param-y' => 'af-exclude-not-found'
		);

		$result = $handler->collectFromRegistry($collect);
		$expected = array(
			'param-a' => 'value-a',
			'param-c' => 'value-c',
			'param-x' => 12345
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @depends	createTaskHandler
	 * @return	null
	 */
	public function kernelRunTasks(TaskHandler $handler)
	{
		$taskA = 'TestFuel\Functional\Kernel\Startup\TestTaskA';
		$taskB =  'TestFuel\Functional\Kernel\Startup\TestTaskB';
		$data = array(
			'startup-tasks' => array($taskA, $taskB),
			'test-a' => 'value-a',
			'test-b' => 'value-b'
		);
		ConfigRegistry::setAll($data);

		$ns = 'Appfuel\Kernel\Mvc';
		$route = $this->getMock("$ns\MvcRouteDetailInterface");
		$input = $this->getMock("$ns\AppInputInterface");
		$context = new MvcContext('my-route', $input);

		$this->assertNull($handler->kernelRunTasks($route, $context));
	
		$this->assertEquals('value-a', $context->get('test-a'));	
		$this->assertEquals('value-b', $context->get('test-b'));

		$msg = TaskHandler::getStatus($taskA);
		$this->assertEquals('test-a has executed', $msg);

		$msg = TaskHandler::getStatus($taskB);
		$this->assertEquals('test-b has executed', $msg);
	}

	/**
	 * @test
	 * @depends	createTaskHandler
	 * @return	null
	 */
	public function kernelRunTaskListNotArrayFailure(TaskHandler $handler)
	{
		$taskA = 'TestFuel\Functional\Kernel\Startup\TestTaskA';
		$taskB =  'TestFuel\Functional\Kernel\Startup\TestTaskB';
		$data = array(
			'startup-tasks' => 'this needs to be an array',
			'test-a' => 'value-a',
			'test-b' => 'value-b'
		);
		ConfigRegistry::setAll($data);

		$ns = 'Appfuel\Kernel\Mvc';
		$route = $this->getMock("$ns\MvcRouteDetailInterface");
		$input = $this->getMock("$ns\AppInputInterface");
		$context = new MvcContext('my-route', $input);

		$msg = 'tasks defined in the config registry must be in an array';
		$this->setExpectedException('RunTimeException', $msg);
		$this->assertNull($handler->kernelRunTasks($route, $context));
	}

	/**
	 * @test
	 * @depends	createTaskHandler
	 * @return	null
	 */
	public function kernelRunTasksClassNotStringFailure(TaskHandler $handler)
	{
		$taskA = 'TestFuel\Functional\Kernel\Startup\TestTaskA';
		$taskB = 'TestFuel\Functional\Kernel\Startup\TestTaskB';
		$taskC = array(1,2,3);
		$data = array(
			'startup-tasks' => array($taskA, $taskB, $taskC),
			'test-a' => 'value-a',
			'test-b' => 'value-b'
		);
		ConfigRegistry::setAll($data);

		$ns = 'Appfuel\Kernel\Mvc';
		$route = $this->getMock("$ns\MvcRouteDetailInterface");
		$input = $this->getMock("$ns\AppInputInterface");
		$context = new MvcContext('my-route', $input);

		$msg  = 'startup task class name must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$this->assertNull($handler->kernelRunTasks($route, $context));
	}

	/**
	 * @test
	 * @depends	createTaskHandler
	 * @return	null
	 */
	public function kernelRunTasksClassInterfaceFailure(TaskHandler $handler)
	{
		$taskA = 'TestFuel\Functional\Kernel\Startup\TestTaskA';
		$taskB = 'TestFuel\Functional\Kernel\Startup\TestTaskB';
		$taskC = 'StdClass';
		$data = array(
			'startup-tasks' => array($taskA, $taskB, $taskC),
			'test-a' => 'value-a',
			'test-b' => 'value-b'
		);
		ConfigRegistry::setAll($data);

		$ns = 'Appfuel\Kernel\Mvc';
		$route = $this->getMock("$ns\MvcRouteDetailInterface");
		$input = $this->getMock("$ns\AppInputInterface");
		$context = new MvcContext('my-route', $input);

		$ns  = 'Appfuel\Kernel\StartupTaskInterface'; 
		$msg = "-(StdClass) must implement $ns";
		$this->setExpectedException('RunTimeException', $msg);
		$this->assertNull($handler->kernelRunTasks($route, $context));
	}
	/**
	 * @test
	 * @depends	createTaskHandler
	 * @return	null
	 */
	public function runTask(TaskHandler $handler)
	{
		$taskA = new TestTaskA();
		$taskB = new TestTaskB();

		$data = array(
			'test-a' => 'value-a',
			'test-b' => 'value-b'
		);
		ConfigRegistry::setAll($data);

		$this->assertNull($handler->runTask($taskA));
			
		$msg = TaskHandler::getStatus(get_class($taskA));
		$this->assertEquals('test-a has executed', $msg);

		$this->assertNull($handler->runTask($taskB));
		$msg = TaskHandler::getStatus(get_class($taskB));
		$this->assertEquals('test-b has executed', $msg);
	}



}
