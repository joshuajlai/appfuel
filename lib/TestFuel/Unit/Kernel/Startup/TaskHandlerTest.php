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
namespace TestFuel\Unit\Kernel\Startup;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Startup\TaskHandler;

class TaskHandlerTest extends BaseTestCase
{
	/**
	 * Backup the existing status list
	 * @var array
	 */
	protected $backup = array();

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->backup = TaskHandler::getStatusList();
		TaskHandler::clearStatusList();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		TaskHandler::clearStatusList();
		foreach ($this->backup as $key => $msg) {
			TaskHandler::addStatus($key, $msg);
		}
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



}
