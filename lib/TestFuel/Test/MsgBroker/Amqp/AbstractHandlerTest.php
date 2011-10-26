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
class AbstractHandlerTest extends BaseTestCase
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
	 * @return	array
	 */
	public function provideExchangeName()
	{
		return	array(
			array(null,		''),
			array('',		''),
			array(' ',		''),
			array("\t",		''),
			array(12345,	''),
			array(1.234,	''),
			array(array(),	''),
			array(array(1,2), ''),
			array(new StdClass(), ''),
			array('my-exchange',	'my-exchange'),
		);
	}

	/**
	 * @return	array
	 */
	public function provideExchangeType()
	{
		return	array(
			array(null,		'direct'),
			array('',		'direct'),
			array(' ',		'direct'),
			array("\t",		'direct'),
			array(12345,	'direct'),
			array(1.234,	'direct'),
			array(array(),	'direct'),
			array(array(1,2), 'direct'),
			array(new StdClass(), 'direct'),
			array('direct',	'direct'),
			array('Direct',	'direct'),
			array('DIRECT', 'direct'),
			array('topic',  'topic'),
			array('Topic',	'topic'),
			array('TOPIC',	'topic'),
			array('fanout',	'fanout'),
			array('Fanout',	'fanout'),
			array('FANOUT',	'fanout'),
			array('header',	'header'),
			array('Header',	'header'),
			array('HEADER',	'header')
		);
	}

	/**
	 * @return	array
	 */
	public function provideBindRouteKey()
	{
		return array(
            array(null,     ''),
            array('',       ''),
            array(' ',      ''),
            array("\t",     ''),
            array(12345,    ''),
            array(1.234,    ''),
            array(array(),  ''),
            array(array(1,2), ''),
            array(new StdClass(), ''),
            array('my-exchange',    'my-exchange'),
		);
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
    public function testQueueName_NotSetFailure()
    {
        $profile = new AmqpProfile(array());
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testQueueName_EmptyStringFailure()
    {
        $profile = new AmqpProfile(array('queue'=> ''));
    }

   /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testQueueName_IntStringFailure()
    {
        $profile = new AmqpProfile(array('queue'=> 12345));
    }

   /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testQueueName_ArrayFailure()
    {
        $profile = new AmqpProfile(array('queue'=> array(1,2,3)));
    }

   /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testQueueName_ObjectFailure()
    {
        $profile = new AmqpProfile(array('queue'=> new StdClass()));
    }

	/**
	 * The name of the exchange is optional. when none is given a default 
	 * of empty is assigned. 
	 *
	 * @dataProvider	 provideExchangeName
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_Exchange($name, $expected)
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('exchange' => $name);
		
		/* will default to an empty string */
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertEquals($expected, $result['exchange']);			
	}

	/**
	 * Exchange type has only a simple set of valid exchanges that can be
	 * set. If the wrong exchange is set the default will be used. The
	 * correct exchanges are 'direct', 'fanout', 'topic', 'header' the 
	 * default is 'direct'
	 *
	 * @dataProvider	provideExchangeType
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_Type($input, $expected)
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('type' => $input);
		
		/* will default to an empty string */
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertEquals($expected, $result['type']);
	}

	/**
	 * passive is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_Passive()
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('passive' => true);
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertTrue($result['passive']); 

		/* not a bool true */
		$exchange['passive'] = 1;
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['passive']); 

		$exchange['passive'] = 'true';
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['passive']); 
	}

	/**
	 * durable is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_Durable()
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('durable' => true);
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertTrue($result['durable']); 

		/* not a bool true */
		$exchange['durable'] = 1;
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['durable']); 

		$exchange['durable'] = 'true';
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['durable']); 
	}

	/**
	 * auto-delete is flag that can only be toggled with a bool false value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_AutoDelete()
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('auto-delete' => false);
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['auto-delete']); 

		/* not a bool false */
		$exchange['auto-delete'] = 0;
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertTrue($result['auto-delete']); 

		$exchange['auto-delete'] = 'false';
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertTrue($result['auto-delete']); 
	}

	/**
	 * internal is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_Internal()
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('internal' => true);
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertTrue($result['internal']); 

		/* not a bool true */
		$exchange['internal'] = 1;
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['internal']); 

		$exchange['internal'] = 'true';
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['internal']); 
	}

	/**
	 * no-wait is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_NoWait()
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('no-wait' => true);
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertTrue($result['no-wait']); 

		/* not a bool true */
		$exchange['no-wait'] = 1;
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['no-wait']); 

		$exchange['no-wait'] = 'true';
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['no-wait']); 
	}

	/**
	 * args can be any non empty array 
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_Args()
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('args' => array(1,2,3));
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertEquals(array(1,2,3), $result['args']); 

		$exchange['args'] = array();
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertNull($result['args']); 

		$exchange['args'] = 'some string';
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertNull($result['args']); 

		$exchange['args'] = 12345;
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertNull($result['args']); 

		$exchange['args'] = new StdClass();
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertNull($result['args']); 
	}

	/**
	 * ticket can be any integer 
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testExchangeSetting_Ticket()
	{
		$queue = array('queue' => 'my-queue');
		$exchange = array('ticket' => 123);
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertEquals(123, $result['ticket']); 

		$exchange['ticket'] = 0;
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertEquals(0, $result['ticket']); 

		$exchange['ticket'] = array();
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertNull($result['ticket']); 

		$exchange['ticket'] = 'some string';
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertNull($result['ticket']); 

		$exchange['ticket'] = new StdClass();
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertNull($result['ticket']); 

		$exchange['ticket'] = 1.2345;
		$profile = new AmqpProfile($queue, $exchange);
		$result  = $profile->getExchangeData();
		$this->assertNull($result['ticket']); 
	}

	/**
	 * no-wait is flag that can only be toggled with a bool true value
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testBindSetting_NoWait()
	{
		$queue	  = array('queue' => 'my-queue');
		$exchange = array();
		$bind	  = array('no-wait' => true);
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertTrue($result['no-wait']); 

		/* not a bool true */
		$bind['no-wait'] = 1;
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertFalse($result['no-wait']); 

		$bind['no-wait'] = 'true';
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getExchangeData();
		$this->assertFalse($result['no-wait']); 
	}
	
	/**
	 * args can be any non empty array 
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testBindSetting_Args()
	{
		$queue	  = array('queue' => 'my-queue');
		$exchange = array();
		$bind	  = array('args' => array(1,2,3));
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertEquals(array(1,2,3), $result['args']); 

		$bind['args'] = array();
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertNull($result['args']); 

		$bind['args'] = 'some string';
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertNull($result['args']); 

		$bind['args'] = 12345;
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertNull($result['args']); 

		$bind['args'] = new StdClass();
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertNull($result['args']); 
	}

	/**
	 * ticket can be any integer 
	 *
	 * @depends	testDefaultValues
	 * @return	null
	 */
	public function testBindSetting_Ticket()
	{
		$queue		= array('queue' => 'my-queue');
		$exchange	= array();
		$bind		= array('ticket' => 123);
		$profile	= new AmqpProfile($queue, $exchange, $bind);
		$result		= $profile->getBindData();
		$this->assertEquals(123, $result['ticket']); 

		$bind['ticket'] = 0;
		$profile = new AmqpProfile($queue, $exchange,$bind);
		$result  = $profile->getBindData();
		$this->assertEquals(0, $result['ticket']); 

		$bind['ticket'] = array();
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertNull($result['ticket']); 

		$bind['ticket'] = 'some string';
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertNull($result['ticket']); 

		$bind['ticket'] = new StdClass();
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertNull($result['ticket']); 

		$bind['ticket'] = 1.2345;
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertNull($result['ticket']); 
	}

	/**
	 * @dataProvider	 provideBindRouteKey
	 * @depends			testDefaultValues
	 * @return			null
	 */
	public function testBindSetting_RouteKey($name, $expected)
	{
		$queue		= array('queue' => 'my-queue');
		$exchange	= array();
		$bind		= array('route-key' => $name);
		
		/* will default to an empty string */
		$profile = new AmqpProfile($queue, $exchange, $bind);
		$result  = $profile->getBindData();
		$this->assertEquals($expected, $result['route-key']);			
	}
}
