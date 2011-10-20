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

use Appfuel\MsgBroker\Amqp\AmqpConnector,
	TestFuel\TestCase\BaseTestCase;

/**
 * The connector only holds the connection data. It is a value object, its 
 * constructor validates the array of data to be a valid data set.
 */
class AmqpConnectorTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpConnector
	 */
	protected $connector = null;

	/**
	 * Parameters used in the constructor
	 * @var array
	 */
	protected $params = array();
	/**
	 * @return null
	 */
	public function setUp()
	{
        $this->params = array(
            'host'      => 'somehost',
            'port'      => 12345,
            'user'      => 'my-user',
            'password'  => 'my-pass',
            'vhost'     => 'my-vhost'
        );

		$this->connector = new AmqpConnector($this->params);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->connector = null;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface',
            $this->connector
        );
    }
}
