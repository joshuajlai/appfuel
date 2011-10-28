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
	Appfuel\MsgBroker\Amqp\AmqpQueue;

/**
 * The queue models the amqp queue entity and does not perform any
 * operations. It is an immutable value object that can not be modified once
 * instantiated.
 */
class AmqpQueueTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpQueue
	 */
	protected $queue = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->queue = new AmqpQueue();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->queue = null;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\AmqpQueueInterface',
            $this->queue
        );
    }

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultValues()
	{
		$this->assertEquals('', $this->queue->getQueueName());
		$this->assertFalse($this->queue->isPassive());
		$this->assertFalse($this->queue->isDurable());
		$this->assertFalse($this->queue->isExclusive());
		$this->assertTrue($this->queue->isAutoDelete());
		$this->assertFalse($this->queue->isNoWait());
		$this->assertNull($this->queue->getArguments());
	}

	/**
	 * @return	array
	 */
	public function provideValidNames()
	{
		return	array(
			array('my-queue', 'my-queue'),
			array('', ''),
			array(null, '')
		);
	}

	/**
	 * @dataProvider	provideValidNames
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_QueueName($input, $expected)
	{
		$queue = new AmqpQueue($input);
		$this->assertEquals($expected, $queue->getQueueName());
	}

	/**
	 * @return	array
	 */
	public function provideInvalidNames()
	{
		return	array(
			array(array(1,2,3)),
			array(array()),
			array(new StdClass()),
			array(123454),
			array(1.2345)
		);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInValidNames
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_QueueNameFailures($input)
	{
		$queue = new AmqpQueue($input);
	}

	/**
	 * @return	array	
	 */
	public function provideBoolValues()
	{
		return array(
			array(true),
			array(false),
		);
	}

	/**
	 * @return	array	
	 */
	public function provideInvalidBoolValues()
	{
		return array(
			array('string-is-not-bool'),
			array('true'),
			array('false'),
			array(''),
			array(array()),
			array(array(1,2,3)),
			array(1),
			array(0),
			array(new StdClass())
		);
	}


	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Passive($input)
	{
		$queue = new AmqpQueue(null, $input);
		$this->assertEquals($input, $queue->isPassive());
		
		$queue = new AmqpQueue(null, null, null);
		$this->assertFalse($queue->isPassive());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_PassiveFailures($input)
	{
		$queue = new AmqpQueue(null, null, $input);
	}

	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Durable($input)
	{
		$queue = new AmqpQueue(null, null, $input);
		$this->assertEquals($input, $queue->isDurable());
		
		$queue = new AmqpQueue(null, null, null);
		$this->assertFalse($queue->isDurable());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_DurableFailures($input)
	{
		$queue = new AmqpQueue(null, null, $input);
	}

	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Exclusive($input)
	{
		$queue = new AmqpQueue(null, null, null, $input);
		$this->assertEquals($input, $queue->isExclusive());
		
		$queue = new AmqpQueue(null, null, null, null);
		$this->assertFalse($queue->isExclusive());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_ExclusiveFailures($input)
	{
		$queue = new AmqpQueue(null, null, null, $input);
	}

	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_AutoDelete($input)
	{
		$queue = new AmqpQueue(null, null, null, null, $input);
		$this->assertEquals($input, $queue->isAutoDelete());
		
		$queue = new AmqpQueue(null, null, null, null, null);
		$this->assertTrue($queue->isAutoDelete());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_AutoDeleteFailures($input)
	{
		$queue = new AmqpQueue(null, null, null, null, $input);
	}

	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_NoWait($input)
	{
		$queue = new AmqpQueue(null, null, null, null, null, $input);
		$this->assertEquals($input, $queue->isNoWait());
		
		$queue = new AmqpQueue(null, null, null, null, null, null);
		$this->assertFalse($queue->isNoWait());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_NoWaitFailures($input)
	{
		$queue = new AmqpQueue(null, null, null, null, null, $input);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Args()
	{
		$args = array('a', 'b', 'c');
		$queue = new AmqpQueue(null, null, null, null, null, null, $args);
		$this->assertEquals($args, $queue->getArguments()); 

		/* array kind does not matter */
		$args = array('a'=> 'x', 'b'=>'y', 'c'=>'ww');
		$queue = new AmqpQueue(null, null, null, null, null, null, $args);
		$this->assertEquals($args, $queue->getArguments()); 

		/* empty arrays are not excepted */
		$args = array();
		$queue = new AmqpQueue(null, null, null, null, null, null, $args);
		$this->assertNull($queue->getArguments()); 
	
		$args  = '';
		$queue = new AmqpQueue(null, null, null, null, null, null, $args);
		$this->assertNull($queue->getArguments()); 

		/* anything not an array is ignored */
		$args = 12345;
		$queue = new AmqpQueue(null, null, null, null, null, null, $args);
		$this->assertNull($queue->getArguments()); 

		$args = 'i am a string';
		$queue = new AmqpQueue(null, null, null, null, null, null, $args);
		$this->assertNull($queue->getArguments()); 

		$args = new StdClass();
		$queue = new AmqpQueue(null, null, null, null, null, null, $args);
		$this->assertNull($queue->getArguments()); 
	}
}
