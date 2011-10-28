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

}
