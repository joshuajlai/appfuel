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
	AMQPMessage,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\MsgBroker\Amqp\Dependency,
	Appfuel\MsgBroker\Amqp\AmqpProfile,
	Appfuel\MsgBroker\Amqp\PublisherTask;

/**
 * The publisher was ment hold a profile and manage the setting for the 
 * amqp channel method 'basic_publish'. Tese parameters are declared in 
 * the constructor and made available through the getAdapterData
 */
class PublisherTaskTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpProfile
	 */
	protected $profile = null;

	/**
	 * System Under Test
	 * @var PublisherTask
	 */
	protected $task = null;

	/**
	 * Name of the queue the profile will be mocked with
	 * @var string
	 */
	protected $exchangeName = null;

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
		$depend = new Dependency();
		$depend->load();
		$this->exchangeName = 'my-exchange';
		$this->adapterData = array(
			'message'		=> '',
			'exchange'      => $this->exchangeName,
			'route-key'		=> '',
			'mandatory'     => false,
			'immediate'     => false,
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
					  ->method('getExchangeName')
					  ->will($this->returnValue($this->exchangeName));


		$this->task = new PublisherTask($this->profile);
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
	        
		$this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\PublisherTaskInterface',
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
     * @depends testInterface
     * @return  null
     */
    public function testConstructorExchangeName()
    {  
        $data = array('exchange' => 'other-exchange');
        $task = new PublisherTask($this->profile, $data);
        $expected = array_merge($this->adapterData, $data);
        $this->assertEquals($expected, $task->getAdapterData());

        $expected = array_values($expected);
        $this->assertEquals($expected, $task->getAdapterValues());

        /* will be default values */
        $data = array('exchange' => array(1,2,3));
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

        /* will be default values */
        $data = array('exchange' => new StdClass());
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());
    }

    /**
     * @depends testInterface
     * @return  null
     */
    public function testConstructorRouteKey()
    {  
        $data = array('route-key' => 'some key');
        $task = new PublisherTask($this->profile, $data);
        $expected = array_merge($this->adapterData, $data);
        $this->assertEquals($expected, $task->getAdapterData());

        $expected = array_values($expected);
        $this->assertEquals($expected, $task->getAdapterValues());
	
		/* empty string is valid and the same as not including it */
        $data = array('route-key' => '');
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

        $expected = array_values($expected);
        $this->assertEquals($expected, $task->getAdapterValues());

        /* will be default values */
        $data = array('route-key' => array(1,2,3));
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

        /* will be default values */
        $data = array('route-key' => new StdClass());
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());
    }

    /**
     * @depends testInterface
     * @return  null
     */
    public function testConstructorMessage()
    {
		$msg  = new AMQPMessage('i am a message');
        $data = array('message' => $msg);
        $task = new PublisherTask($this->profile, $data);
        $expected = array_merge($this->adapterData, $data);
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values */
	    $data = array('message' => 'i am a string');
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values */
	    $data = array('message' => array(1,2,2));
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values */
	    $data = array('message' => new StdClass());
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());
    }

    /**
     * @depends testInterface
     * @return  null
     */
    public function testConstructorMandatory()
    {
        $data = array('mandatory' => true);
        $task = new PublisherTask($this->profile, $data);
        $expected = array_merge($this->adapterData, $data);
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values same as default */
        $data = array('mandatory' => false);
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values */
	    $data = array('mandatory' => array(1,2,2));
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values */
	    $data = array('mandatory' => new StdClass());
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values not a strict bool*/
	    $data = array('mandatory' => 1);
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());
    }

    /**
     * @depends testInterface
     * @return  null
     */
    public function testConstructorImmediate()
    {
        $data = array('immediate' => true);
        $task = new PublisherTask($this->profile, $data);
        $expected = array_merge($this->adapterData, $data);
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values same as default */
        $data = array('immediate' => false);
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values */
	    $data = array('immediate' => array(1,2,2));
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values */
	    $data = array('immediate' => new StdClass());
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

		/* defaults values not a strict bool*/
	    $data = array('immediate' => 1);
        $task = new PublisherTask($this->profile, $data);
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
        $task = new PublisherTask($this->profile, $data);
        $expected = array_merge($this->adapterData, $data);
        $this->assertEquals($expected, $task->getAdapterData());

        $data = array('ticket' => 0);
        $task = new PublisherTask($this->profile, $data);
        $expected = array_merge($this->adapterData, $data);
        $this->assertEquals($expected, $task->getAdapterData());

        /* will be default values */
        $data = array('ticket' => 'abc');
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

        /* will be default values */
        $data = array('ticket' => array(1,2,3));
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());

        /* will be default values */
        $data = array('ticket' => new StdClass());
        $task = new PublisherTask($this->profile, $data);
        $expected = $this->adapterData;
        $this->assertEquals($expected, $task->getAdapterData());
    }

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetMessage()
	{
		$data = $this->task->getAdapterData();
		$this->assertEquals('', $data['message']);

		$this->assertSame($this->task, $this->task->setMessage('my Msg'));
		
		$data = $this->task->getAdapterData();
		$this->assertInstanceOf('AMQPMessage', $data['message']);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRouteKey()
	{
		$data = $this->task->getAdapterData();
		$this->assertEquals('', $data['route-key']);

		$this->assertSame($this->task, $this->task->setRouteKey('my-key'));
		
		$data = $this->task->getAdapterData();
		$this->assertEquals('my-key', $data['route-key']);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRouteKey_NumberFailure()
	{
		$this->task->setRouteKey(1233445);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRouteKey_ArrayFailure()
	{
		$this->task->setRouteKey(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRouteKey_ObjectFailure()
	{
		$this->task->setRouteKey(new StdClass());
	}




}
