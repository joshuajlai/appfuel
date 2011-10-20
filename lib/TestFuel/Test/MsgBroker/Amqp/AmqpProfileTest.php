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

use Appfuel\MsgBroker\Amqp\AmqpProfile,
	TestFuel\TestCase\BaseTestCase;

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
}
