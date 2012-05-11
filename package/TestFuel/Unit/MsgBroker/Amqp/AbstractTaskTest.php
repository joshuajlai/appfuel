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
namespace TestFuel\Test\MsgBroker\Amqp;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\MsgBroker\Amqp\AmqpProfile;

/**
 * Common fucntionality for handling both publisher and consumers
 */
class AbstractTaskTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpProfile
	 */
	protected $profile = null;

	/**
	 * System Under Test
	 * @var Appfuel\MsgBroker\Amqp\AbstractTask
	 */
	protected $task = null;

	/**
	 * Used to create mock abtract objects
	 * @var string
	 */
	protected $taskClass = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$pInterface = 'Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface';
		$this->taskClass = 'Appfuel\MsgBroker\Amqp\AbstractTask';
		$this->profile = $this->getMockBuilder($pInterface)
							  ->disableOriginalConstructor()
							  ->setMethods(array(
									'getExchangeName',
									'getQueueName',
									'getExchangeData',
									'getQueueData',
									'getBindData'))
								->getMock();

		$this->task = $this->getMockBuilder($this->taskClass)
						   ->setConstructorArgs(array($this->profile))
						   ->getMockForAbstractClass();
							
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->profile = null;
		$this->task    = null;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\AmqpTaskInterface',
            $this->task
        );
    }

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetProfile()
	{
		$this->assertSame($this->profile, $this->task->getProfile());
	}

	/**
	 * Wrapper for the same method in the profile object
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetExchangeName()
	{
		$name = 'my-exchange';
		$this->profile->expects($this->once())
					  ->method('getExchangeName')
					  ->will($this->returnValue($name));

		$this->assertEquals($name, $this->task->getExchangeName());
	}

	/**
	 * Wrapper for the same method in the profile object
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetQueueName()
	{
		$name = 'my-queue';
		$this->profile->expects($this->once())
					  ->method('getQueueName')
					  ->will($this->returnValue($name));

		$this->assertEquals($name, $this->task->getQueueName());
	}

	/**
	 * The abstract class makes no attempt to validate or modify the adapter
	 * data that is put in. The concrete classes extend the setAdapterData for
	 * that purpose. So here we will show whatever array you put in is what
	 * you get out.
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetAdapterDataGetAdapterValues()
	{
		$this->assertEquals(array(), $this->task->getAdapterData());
		$this->assertEquals(array(), $this->task->getAdapterValues());

		$data = array('key'=>'value');
		$task = $this->getMockBuilder($this->taskClass)
                           ->setConstructorArgs(array($this->profile, $data))
                           ->getMockForAbstractClass();


		$this->assertEquals($data, $task->getAdapterData());
		$this->assertEquals(array('value'), $task->getAdapterValues());

		$data = array();
		$task = $this->getMockBuilder($this->taskClass)
                           ->setConstructorArgs(array($this->profile, $data))
                           ->getMockForAbstractClass();

		$this->assertEquals($data, $task->getAdapterData());
		$this->assertEquals(array(), $task->getAdapterValues());

		$data = array(1,2,3);
		$task = $this->getMockBuilder($this->taskClass)
                           ->setConstructorArgs(array($this->profile, $data))
                           ->getMockForAbstractClass();

		$this->assertEquals($data, $task->getAdapterData());
		$this->assertEquals($data, $task->getAdapterData());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetExchangeValues()
	{
		$data = array('a' => 'value-a', 'b' => 'value-b', 'c' => 'value-c');
		$this->profile->expects($this->once())
					  ->method('getExchangeData')
					  ->will($this->returnValue($data));


		$expected = array_values($data);
		$this->assertEquals($expected, $this->task->getExchangeValues());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetQueueValues()
	{
		$data = array('a' => 'value-a', 'b' => 'value-b', 'c' => 'value-c');
		$this->profile->expects($this->once())
					  ->method('getQueueData')
					  ->will($this->returnValue($data));


		$expected = array_values($data);
		$this->assertEquals($expected, $this->task->getQueueValues());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetBindValues()
	{
		$data = array('a' => 'value-a', 'b' => 'value-b', 'c' => 'value-c');
		$this->profile->expects($this->once())
					  ->method('getBindData')
					  ->will($this->returnValue($data));


		$expected = array_values($data);
		$this->assertEquals($expected, $this->task->getBindValues());
	}
}
