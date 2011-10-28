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
namespace TestFuel\Test\MsgBroker\Amqp\Entity;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\MsgBroker\Amqp\Entity\AmqpBind;

/**
 * The bind models the amqp bind entity and does not perform any
 * operations. It is an immutable value object that can not be modified once
 * instantiated.
 */
class AmqpBindTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpBind
	 */
	protected $bind = null;

	/**
	 * First parameter of the constructor
	 * @var string
	 */
	protected $queue = null;

	/**
	 * Second parameter of the constructor
	 * @var string
	 */
	protected $exchange = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->queue = 'my-queue';
		$this->exchange = 'my-exchange';
		$this->bind = new AmqpBind($this->queue, $this->exchange);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->bind = null;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\Entity\AmqpBindInterface',
            $this->bind
        );
    }

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorValues()
	{
		$this->assertEquals($this->queue, $this->bind->getQueueName());
		$this->assertEquals($this->exchange, $this->bind->getExchangeName());
		$this->assertEquals('', $this->bind->getRouteKey());
		$this->assertFalse($this->bind->isNoWait());
		$this->assertNull($this->bind->getArguments());
	}

	/**
	 * @return	array
	 */
	public function provideValidNames()
	{
		return	array(
			array('my-name', 'my-name'),
			array('', ''),
		);
	}

	/**
	 * @dataProvider	provideValidNames
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_QueueExchangeName($input, $expected)
	{
		$bind = new AmqpBind($input, $input, $input);
		$this->assertEquals($expected, $bind->getQueueName());
		$this->assertEquals($expected, $bind->getExchangeName());
		$this->assertEquals($expected, $bind->getRouteKey());
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
	public function testConstructor_QueueFailures($input)
	{
		$bind = new AmqpBind($input, 'exchange', 'my-key');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInValidNames
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_ExchangeFailures($input)
	{
		$bind = new AmqpBind('queue', $input, 'my-key');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInValidNames
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_RouteKeyFailures($input)
	{
		$bind = new AmqpBind('queue', 'exchange', $input);
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
	public function testConstructor_NoWait($input)
	{
		$bind = new AmqpBind('queue', 'exchange', 'key', $input);
		$this->assertEquals($input, $bind->isNoWait());
		
		$bind = new AmqpBind('queue', 'exchange', 'key', null);
		$this->assertFalse($bind->isNoWait());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_NoWaitFailures($input)
	{
		$queue = new AmqpBind('queue', 'exchange', 'key', $input);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Args()
	{
		$args = array('a', 'b', 'c');
		$bind = new AmqpBind('queue', 'exchange', null, null, $args);
		$this->assertEquals($args, $bind->getArguments()); 

		/* array kind does not matter */
		$args = array('a'=> 'x', 'b'=>'y', 'c'=>'ww');
		$bind = new AmqpBind('queue', 'exchange', null, null, $args);
		$this->assertEquals($args, $bind->getArguments()); 

		/* empty arrays are not excepted */
		$args = array();
		$bind = new AmqpBind('queue', 'exchange', null, null, $args);
		$this->assertNull($bind->getArguments()); 
	
		$args  = '';
		$bind = new AmqpBind('queue', 'exchange', null, null, $args);
		$this->assertNull($bind->getArguments()); 

		/* anything not an array is ignored */
		$args = 12345;
		$bind = new AmqpBind('queue', 'exchange', null, null, $args);
		$this->assertNull($bind->getArguments()); 

		$args = 'i am a string';
		$bind = new AmqpBind('queue', 'exchange', null, null, $args);
		$this->assertNull($bind->getArguments()); 

		$args = new StdClass();
		$bind = new AmqpBind('queue', 'exchange', null, null, $args);
		$this->assertNull($bind->getArguments()); 
	}
}
