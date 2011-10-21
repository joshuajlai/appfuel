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
 * The profile is a way to group information about exchanges queues and 
 * bindings so we don't have to repeat these in every scripts that uses 
 * rabbitmq. We will be testing these settings in this test case.
 */
class AmqpProfileTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpProfile
	 */
	protected $profile = null;

	/**
	 * Information related to queues. 1st param in constructor 
	 * @var array
	 */
	protected $queue = null;

	/**
	 * Information related to exchanges. 2nd param in constructor
	 * @var array
	 */
	protected $exchange = array();

	/**
	 * Information related to binding exchange to queue. 3rd param
	 * @var array
	 */
	protected $bind = array();

	/**
	 * @return null
	 */
	public function setUp()
	{
        $this->queue = array(
            'queue'         => 'my-queue',
            'passive'       => true,
            'durable'       => true,
            'exclusive'     => true,
            'auto-delete'   => false,
            'no-wait'       => true,
            'args'          => array(1, 'my-setting'),
            'ticket'       => 3,
        );

        $this->exchange = array(
            'exchange'      => 'my-exchange',
            'type'          => 'topic',
            'passive'       => true,
            'durable'       => true,
            'auto-delete'   => false,
            'internal'      => true,
            'no-wait'       => true,
            'args'          => array('S', 'x-something'),
            'ticket'        => 6
        );

        $this->bind = array(
            'route-key' => 'my-key',
            'no-wait'   => true,
            'args'      => array('a', 'b'),
            'ticket'    => 10
        );

		$this->profile = new AmqpProfile(
			$this->queue, 
			$this->exchange,
			$this->bind
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->profile = null;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface',
            $this->profile
        );
    }

    /**
     * @return null
     */
    public function testConstructor()
    {
        $this->assertEquals($this->queue,    $this->profile->getQueueData());
        $this->assertEquals($this->exchange, $this->profile->getExchangeData());

        /* the exhange and queue are automatically copied over to the bind */
        $expected = $this->bind;
        $expected['queue']    = $this->queue['queue'];
        $expected['exchange'] = $this->exchange['exchange'];
        $this->assertEquals($expected, $this->profile->getBindData());
    }

    /**
	 * Test the default values that are used.
	 * 
	 * @depends	testInterface
     * @return null
     */
    public function testDefaultValues()
    {
        $queue = array('queue' => 'my-queue');
        $profile = new AmqpProfile($queue);

        $exchangeDefault = array(
            'exchange'      => '',
            'type'          => 'direct',
            'passive'       => false,
            'durable'       => false,
            'auto-delete'   => true,
            'internal'      => false,
            'no-wait'       => false,
            'args'          => null,
            'ticket'        => null
        );

        /* notice we changed queue to what was passed in since its the only
         * required field
         */
        $queueDefault = array(
            'queue'         => 'my-queue',
            'passive'       => false,
            'durable'       => false,
            'exclusive'     => false,
            'auto-delete'   => true,
            'no-wait'       => false,
            'args'          => null,
            'ticket'        => null,
        );

        $bindDefault = array(
            'queue'     => 'my-queue',
            'exchange'  => '',
            'route-key' => '',
            'no-wait'   => false,
            'args'      => null,
            'ticket'    => null
        );
        $this->assertEquals($exchangeDefault, $profile->getExchangeData());
        $this->assertEquals($queueDefault, $profile->getQueueData());
        $this->assertEquals($bindDefault, $profile->getBindData());
    }

	/**
	 * passive is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testQueueSetting_Passive()
	{
		$data = array('queue' => 'my-queue', 'passive' => true);
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertTrue($result['passive']); 

		/* not a bool true */
		$data['passive'] = 1;
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['passive']); 

		$data['passive'] = 'true';
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['passive']); 
	}

	/**
	 * durable is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testQueueSetting_Durable()
	{
		$data = array('queue' => 'my-queue', 'durable' => true);
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertTrue($result['durable']); 

		/* not a bool true */
		$data['durable'] = 1;
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['durable']); 

		$data['durable'] = 'true';
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['durable']); 
	}

	/**
	 * exclusive is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testQueueSetting_Exclusive()
	{
		$data = array('queue' => 'my-queue', 'exclusive' => true);
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertTrue($result['exclusive']); 

		/* not a bool true */
		$data['exclusive'] = 1;
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['exclusive']); 

		$data['exclusive'] = 'true';
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['exclusive']); 
	}

	/**
	 * auto-delete is flag that can only be toggled with a bool false because
	 * the default value is true
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testQueueSetting_AutoDelete()
	{
		$data = array('queue' => 'my-queue', 'auto-delete' => false);
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['auto-delete']); 

		/* will not toggle to false because not a bool false */
		$data['auto-delete'] = 0;
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertTrue($result['auto-delete']); 

		$data['auto-delete'] = 'false';
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertTrue($result['auto-delete']); 
	}

	/**
	 * no-wait is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testQueueSetting_NoWait()
	{
		$data = array('queue' => 'my-queue', 'no-wait' => true);
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertTrue($result['no-wait']); 

		/* will not be true because setting is not strict bool true */
		$data['no-wait'] = 1;
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['no-wait']); 

		$data['no-wait'] = 'true';
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertFalse($result['no-wait']); 
	}

	/**
	 * args can be any non empty array 
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testQueueSetting_Args()
	{
		$data = array('queue' => 'my-queue', 'args' => array(1,2,3));
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertEquals(array(1,2,3), $result['args']); 

		$data['args'] = array();
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertNull($result['args']); 

		$data['args'] = 'some string';
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertNull($result['args']); 

		$data['args'] = 12345;
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertNull($result['args']); 

		$data['args'] = new StdClass();
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertNull($result['args']); 
	}

	/**
	 * ticket can be any integer 
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testQueueSetting_Ticket()
	{
		$data = array('queue' => 'my-queue', 'ticket' => 123);
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertEquals(123, $result['ticket']); 

		$data['ticket'] = 0;
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertEquals(0, $result['ticket']); 

		$data['ticket'] = array();
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertNull($result['ticket']); 

		$data['ticket'] = 'some string';
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertNull($result['ticket']); 

		$data['ticket'] = new StdClass();
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertNull($result['ticket']); 

		$data['ticket'] = 1.2345;
		$profile = new AmqpProfile($data);
		$result  = $profile->getQueueData();
		$this->assertNull($result['ticket']); 


	}



    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testNoParamsForQueue()
    {
        $profile = new AmqpProfile(array());
    }
}
