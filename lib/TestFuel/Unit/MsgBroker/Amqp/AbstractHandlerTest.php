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
	Appfuel\MsgBroker\Amqp\Dependency,
	Appfuel\MsgBroker\Amqp\AmqpProfile,
	Appfuel\MsgBroker\Amqp\ConsumerTask;

/**
 * Common fucntionality for handling both publisher and consumers
 */
class AbstractHandlerTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpProfile
	 */
	protected $profile = null;

	/**
	 * System Under Test
	 * @var Appfuel\MsgBroker\Amqp\AbstractHandler
	 */
	protected $handler = null;

	/**
	 * Used to create mock abtract objects
	 * @var string
	 */
	protected $handlerClass = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$depend = new Dependency();
		$depend->load();

		$profile  = new AmqpProfile(array('queue' => 'af-test-queue'));
		$this->task  = new ConsumerTask($profile);
		$this->connData  = array(
			'host'		=> 'localhost',
			'user'		=> 'guest',
			'password'	=> 'guest',
			'vhost'		=> '/'
		);
		$this->handlerClass = 'Appfuel\MsgBroker\Amqp\AbstractHandler';
		$params = array($this->connData, $this->task);
		$this->handler = $this->getMockBuilder($this->handlerClass)
						   ->setConstructorArgs($params)
						   ->getMockForAbstractClass();
							
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->task    = null;
		$this->handler = null;
	}

	public function createMockTask()
	{
        $taskInterface = 'Appfuel\Framework\MsgBroker\Amqp\AmqpTaskInterface';
        $task = $this->getMockBuilder($taskInterface)
                     ->setMethods(array(
                        'getProfile',
                        'getExchangeName',
                        'getQueueName',
                        'getExchangeValues',
                        'getQueueValues',
                        'getBindValues'))
                     ->getMock();
		return $task;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\TaskHandlerInterface',
            $this->handler
        );
    }

	/**
	 * The task can only be set through the constructor
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetTask()
	{
		$this->assertSame($this->task, $this->handler->getTask());
	}

	/**
	 * A connector can be an array of key/value pairs of an object that
	 * implements Appfuel\Framework\MsgBroker\AmqpConnectorInterface.
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetConnector()
	{
		$connector = $this->handler->getConnector();
		$this->assertInstanceOf(
			'Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface',
			$connector
		);

		/* happens only when we initialize */
		$this->assertNull($this->handler->getConnection());
		$this->assertNull($this->handler->getChannelAdapter());
		$this->assertFalse($this->handler->isConnection());
		$this->assertFalse($this->handler->isChannelAdapter());
	}

	/**
	 * @depends	testGetConnector
	 * @return	null
	 */
	public function testInitializeShutDown()
	{
		$this->assertNull($this->handler->initialize());
		$this->assertTrue($this->handler->isConnection());
		$this->assertTrue($this->handler->isChannelAdapter());

		$channel = $this->handler->getChannelAdapter();
		$this->assertInstanceOf(
			'AMQPChannel',
			$channel
		);

		$connection = $this->handler->getConnection();
		$this->assertInstanceOf(
			'AMQPConnection',
			$connection
		);

		$this->assertNull($this->handler->shutDown());
		$this->assertNull($this->handler->getConnection());
		$this->assertNull($this->handler->getChannelAdapter());
		$this->assertFalse($this->handler->isConnection());
		$this->assertFalse($this->handler->isChannelAdapter());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDeclareExchange()
	{
		$adapter = $this->getMockBuilder('AMQPChannel')
						->disableOriginalConstructor()
						->setMethods(array('exchange_declare'))
						->getMock();

		$expected = 'success';
		$adapter->expects($this->once())
				->method('exchange_declare')
				->will($this->returnValue($expected));

		$task = $this->createMockTask();
		$task->expects($this->once())
			 ->method('getExchangeValues')
			 ->will($this->returnValue(array(1,2,3,4,5,6,7,8,9)));

		$result = $this->handler->declareExchange($adapter, $task);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDeclareQueue()
	{
		$adapter = $this->getMockBuilder('AMQPChannel')
						->disableOriginalConstructor()
						->setMethods(array('queue_declare'))
						->getMock();

		$expected = 'success';
		$adapter->expects($this->once())
				->method('queue_declare')
				->will($this->returnValue($expected));

		$task = $this->createMockTask();
		$task->expects($this->once())
			 ->method('getQueueValues')
			 ->will($this->returnValue(array(1,2,3,4,5,6,7,8)));

		$result = $this->handler->declareQueue($adapter, $task);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBindQueue()
	{
		$adapter = $this->getMockBuilder('AMQPChannel')
						->disableOriginalConstructor()
						->setMethods(array('queue_bind'))
						->getMock();

		$expected = 'success';
		$adapter->expects($this->once())
				->method('queue_bind')
				->will($this->returnValue($expected));

		$task = $this->createMockTask();
		$task->expects($this->once())
			 ->method('getBindValues')
			 ->will($this->returnValue(array(1,2,3,4,5,6)));

		$result = $this->handler->bindQueue($adapter, $task);
		$this->assertEquals($expected, $result);
	}



}
