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

use SplFileInfo,
	StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\MsgBroker\Amqp\AmqpProfile,
	Appfuel\MsgBroker\Amqp\ConsumerTask;

/**
 * The consumer was ment hold a profile and manage the setting for the 
 * amqp channel method 'basic_consume'. Tese parameters are declared in 
 * the constructor and made available through the getAdapterData
 */
class ConsumerTaskTest extends BaseTestCase
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
	 * Name of the queue the profile will be mocked with
	 * @var string
	 */
	protected $queueName = null;

	/**
	 * Default adapter data given
	 * @var array
	 */
	protected $adapterData = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->queueName = 'my-queue';
		$this->adapterData = array(
			'queue'         => $this->queueName,
			'consumer-tag'  => '',
			'no-local'      => false,
			'no-ack'        => false,
			'exclusive'     => true,
			'no-wait'       => false,
			'callback'      => null,
			'ticket'        => null
		);
		$pInterface = 'Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface';
		$this->profile = $this->getMockBuilder($pInterface)
							  ->disableOriginalConstructor()
							  ->setMethods(array(
									'getExchangeName',
									'getQueueName',
									'getExchangeData',
									'getQueueData',
									'getBindData'))
								->getMock();

		$this->profile->expects($this->any())
					  ->method('getQueueName')
					  ->will($this->returnValue($this->queueName));


		$this->task = new ConsumerTask($this->profile);
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
	 * The queue name is inserted if no name is given
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetAdapterData()
	{
		/* the default array used when non is given */
		$expected = $this->adapterData;
		$this->assertEquals($expected, $this->task->getAdapterData());

		$expected = array_values($expected);
		$this->assertEquals($expected, $this->task->getAdapterValues());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorQueueName()
	{
		$data = array('queue' => 'other-queue');
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$expected = array_values($expected);
		$this->assertEquals($expected, $task->getAdapterValues());

		/* will be default values */
		$data = array('queue' => array(1,2,3));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('queue' => new StdClass());
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());
	}


	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorConsumerTag()
	{
		$data = array('consumer-tag' => 'mytag');
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$expected = array_values($expected);
		$this->assertEquals($expected, $task->getAdapterValues());

		$data = array('consumer-tag' => '');
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$data = array('consumer-tag' => 0);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$data = array('consumer-tag' => 12345);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$data = array('consumer-tag' => -12345);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('consumer-tag' => array(1,2,3));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('consumer-tag' => new StdClass());
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorNoLocal()
	{
		$data = array('no-local' => true);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$expected = array_values($expected);
		$this->assertEquals($expected, $task->getAdapterValues());

		/* same as default values */
		$data = array('no-local' =>false);
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('no-local' => array(1,2,3));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('no-local' => new StdClass());
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values must be a strict bool*/
		$data = array('no-local' => 1);
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values must be a strict bool*/
		$data = array('no-local' => 'true');
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorNoAck()
	{
		$data = array('no-ack' => true);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$expected = array_values($expected);
		$this->assertEquals($expected, $task->getAdapterValues());

		/* same as default values */
		$data = array('no-ack' =>false);
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('no-ack' => array(1,2,3));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('no-ack' => new StdClass());
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values must be a strict bool*/
		$data = array('no-ack' => 1);
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values must be a strict bool*/
		$data = array('no-ack' => 'true');
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorExclusive()
	{
		$data = array('exclusive' => false);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$expected = array_values($expected);
		$this->assertEquals($expected, $task->getAdapterValues());

		/* same as default values */
		$data = array('exclusive' => true);
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('exclusive' => array(1,2,3));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('exclusive' => new StdClass());
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values must be a strict bool*/
		$data = array('exclusive' => 1);
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values must be a strict bool*/
		$data = array('exclusive' => 'true');
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorNoWait()
	{
		$data = array('no-wait' => true);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		$expected = array_values($expected);
		$this->assertEquals($expected, $task->getAdapterValues());

		/* same as default values */
		$data = array('no-wait' => false);
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('no-wait' => array(1,2,3));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('no-wait' => new StdClass());
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values must be a strict bool*/
		$data = array('no-wait' => 1);
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values must be a strict bool*/
		$data = array('no-wait' => 'true');
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());
	}


    /**
     * ticket can be any integer 
     *
     * @depends testInterface
     * @return  null
     */
    public function testConstructorTicket()
    {  
        $data = array('ticket' => 123);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

        $data = array('ticket' => 0);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('ticket' => 'abc');
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('ticket' => array(1,2,3));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* will be default values */
		$data = array('ticket' => new StdClass());
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());
    }

    /**
     * @depends testInterface
     * @return  null
     */
    public function testConstructorCallback()
    {  
		$file = new SplFileInfo('some/path');
		$callback = array($file, 'isFile');
        $data = array('callback' => $callback);
		$task = new ConsumerTask($this->profile, $data);
		$expected = array_merge($this->adapterData, $data);
		$this->assertEquals($expected, $task->getAdapterData());

		/* default values cause not callable */
	    $data = array('callback' => 'this is not callable');
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());

		/* default values cause not callable */
	    $data = array('callback' => array(1,2,3));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());


		/* default values cause not callable */
	    $data = array('callback' => array($file, 'method-not-there'));
		$task = new ConsumerTask($this->profile, $data);
		$expected = $this->adapterData;
		$this->assertEquals($expected, $task->getAdapterData());
    }

	/**
	 * @depends	testInterface	
	 * @return	null
	 */
	public function testGetSetConsumerTag()
	{
		$this->assertEquals('', $this->task->getConsumerTag());
		
		$tag = 'abc';
		$this->assertSame($this->task, $this->task->setConsumerTag($tag));
		$this->assertEquals($tag, $this->task->getConsumerTag());
		$data = $this->task->getAdapterData();
		$this->assertEquals($tag, $data['consumer-tag']);

		/* can be an empty string */
		$tag = '';
		$this->assertSame($this->task, $this->task->setConsumerTag($tag));
		$this->assertEquals($tag, $this->task->getConsumerTag());
		$data = $this->task->getAdapterData();
		$this->assertEquals($tag, $data['consumer-tag']);

		/* can be an numeric */
		$tag = 12345;
		$this->assertSame($this->task, $this->task->setConsumerTag($tag));
		$this->assertEquals($tag, $this->task->getConsumerTag());
		$data = $this->task->getAdapterData();
		$this->assertEquals($tag, $data['consumer-tag']);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testEnableDisableManualAck()
	{
		/* current value is false */
		$data = $this->task->getAdapterData();
		$this->assertFalse($data['no-ack']);

		$this->assertSame($this->task, $this->task->enableManualAck());
		$data = $this->task->getAdapterData();
		$this->assertTrue($data['no-ack']);

		$this->assertSame($this->task, $this->task->disableManualAck());
		$data = $this->task->getAdapterData();
		$this->assertFalse($data['no-ack']);	
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetCallback()
	{
		$data = $this->task->getAdapterData();
		$this->assertNull($data['callback']);

		$file = new SplFileInfo('some/path');
		$callback = array($file, 'isFile');
		$this->assertSame($this->task, $this->task->setCallback($callback));

		
		$data = $this->task->getAdapterData();
		$this->assertEquals($callback, $data['callback']);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetCallback_NotCallableObjectFailure()
	{
		$obj = new StdClass();
		$callback = array($obj, 'isFile');
		$this->task->setCallback($callback);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetCallback_StringFailure()
	{
		$this->task->setCallback('i am not callable');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetCallback_ArrayFailure()
	{
		$this->task->setCallback(array('i am ', 'not callable'));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetConsumerTag_ArrayFailure()
	{
		$this->task->setConsumerTag(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetConsumerTag_ObjectFailure()
	{
		$this->task->setConsumerTag(new StdClass());
	}


}
