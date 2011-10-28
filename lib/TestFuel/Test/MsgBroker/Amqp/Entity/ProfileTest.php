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
	Appfuel\MsgBroker\Amqp\Entity\Profile,
	Appfuel\MsgBroker\Amqp\Entity\AmqpBind,
	Appfuel\MsgBroker\Amqp\Entity\AmqpQueue,
	Appfuel\MsgBroker\Amqp\Entity\AmqpExchange;

/**
 * The exchange models the amqp exchange entity and does not perform any
 * operations. It is an immutable value object that can not be modified once
 * instantiated.
 */
class ProfileTest extends BaseTestCase
{
	/**
	 * First param of constructor
	 * @var AmqpQueue
	 */
	protected $queue = null;

	/**
	 * Second param of constructor
	 * @var AmqpExchange
	 */
	protected $exchange = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->queue    = new AmqpQueue('my-queue');
		$this->exchange = new AmqpExchange();
		$this->profile  = new Profile($this->queue, $this->exchange);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->queue    = null;
		$this->exchange = null;
		$this->profile  = null;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\Entity\ProfileInterface',
            $this->profile
        );
    }

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorInSetup()
	{
		$this->assertSame($this->queue, $this->profile->getQueue());
		$this->assertSame($this->exchange, $this->profile->getExchange());
		$this->assertEquals(array(), $this->profile->getBindings());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorNoExchangeOrBindings()
	{
		$profile = new Profile($this->queue);
		$this->assertSame($this->queue, $this->profile->getQueue());

		/* 
		 * will create a default exchange which is an exchange with an
		 * empty string as the exchange name
		 */
		$exchange = $profile->getExchange();
		$this->assertInstanceOf(
			'Appfuel\Framework\MsgBroker\Amqp\Entity\AmqpExchangeInterface',
			$exchange
		);
		$this->assertEquals('', $exchange->getExchangeName());
		$this->assertEquals(array(), $profile->getBindings());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorNoExchangeBindings()
	{
		$bindA = new AmqpBind('queue', 'exchange', 'keyA');
		$bindB = new AmqpBind('queue', 'exchange', 'keyB');
		$bindC = new AmqpBind('queue', 'exchange', 'keyC');
		$bindings = array($bindA, $bindB, $bindC);
		$profile = new Profile($this->queue, null, $bindings);
		$this->assertSame($this->queue, $this->profile->getQueue());
		/* 
		 * will create a default exchange which is an exchange with an
		 * empty string as the exchange name
		 */
		$exchange = $profile->getExchange();
		$this->assertInstanceOf(
			'Appfuel\Framework\MsgBroker\Amqp\Entity\AmqpExchangeInterface',
			$exchange
		);
		$this->assertEquals($bindings, $profile->getBindings());
	}
}
