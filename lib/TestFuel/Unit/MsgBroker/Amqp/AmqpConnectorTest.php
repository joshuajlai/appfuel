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

    /**
     * @depends testInterface
     * @return  null
     */
    public function testMembers()
    {
        $this->assertEquals(
			$this->params['host'], 
			$this->connector->getHost()
		);
        $this->assertEquals(
			$this->params['port'], 
			$this->connector->getPort()
		);
        $this->assertEquals(
			$this->params['user'], 
			$this->connector->getUser()
		);
        $this->assertEquals(
            $this->params['password'],
            $this->connector->getPassword()
        );
        $this->assertEquals(
            $this->params['vhost'],
            $this->connector->getVirtualHost()
        );
    }

    /**
     * @depends testInterface
     * @return  null
     */
    public function testPasswordNumeric()
    {
        $params = $this->params;
        $params['password'] = 12345;

        $conn = new AmqpConnector($params);
        $this->assertEquals($params['password'], $conn->getPassword());
    }

    /**
     * @depends testMembers
     * @return  null
     */
    public function testDefaultPort()
    {
        $default = 5672;
        $params = $this->params;
        unset($params['port']);

        $conn = new AmqpConnector($params);
        $this->assertEquals($default, $conn->getPort());

        $params['port'] = '';
        $config = new AmqpConnector($params);
        $this->assertEquals($default, $conn->getPort());

        $params['port'] = array();
        $config = new AmqpConnector($params);
        $this->assertEquals($default, $conn->getPort());

        $params['port'] = false;
        $config = new AmqpConnector($params);
        $this->assertEquals($default, $conn->getPort());

        $params['port'] = 0;
        $config = new AmqpConnector($params);
        $this->assertEquals($default, $conn->getPort());
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testHost_NotSetFailure()
    {
        $params = $this->params;
        unset($params['host']);

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testHost_EmptyStringFailure()
    {
        $params = $this->params;
        $params['host'] = '';

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testHost_EmptyArrayFailure()
    {
        $params = $this->params;
        $params['host'] = array();

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testHost_EmptyNullFailure()
    {
        $params = $this->params;
        $params['host'] = null;

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testHost_ArrayFailure()
    {
        $params = $this->params;
        $params['host'] = array(1,23,4);

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testHost_IntFailure()
    {
        $params = $this->params;
        $params['host'] = 12345;

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return null
     */
    public function testPort_ArrayFailure()
    {
        $params = $this->params;
        $params['port'] = array(1,2,3);

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return null
     */
    public function testPort_FloatFailure()
    {
        $params = $this->params;
        $params['port'] = 1.234;

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return null
     */
    public function testPort_ObjectFailure()
    {
        $params = $this->params;
        $params['port'] = new StdClass();

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return null
     */
    public function testPort_NegativeIntFailure()
    {
        $params = $this->params;
        $params['port'] = -123;

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testUser_NotSetFailure()
    {
        $params = $this->params;
        unset($params['user']);

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testUser_EmptyStringFailure()
    {
        $params = $this->params;
        $params['user'] = '';

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testUser_EmptyArrayFailure()
    {
        $params = $this->params;
        $params['user'] = array();

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testUser_EmptyNullFailure()
    {
        $params = $this->params;
        $params['user'] = null;

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testUser_ArrayFailure()
    {
        $params = $this->params;
        $params['user'] = array(1,23,4);

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testUser_IntFailure()
    {
        $params = $this->params;
        $params['user'] = 12345;

        $conn = new AmqpConnector($params);
    }
    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testPass_NotSetFailure()
    {
        $params = $this->params;
        unset($params['password']);

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testPass_EmptyStringFailure()
    {
        $params = $this->params;
        $params['password'] = '';

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testPass_EmptyArrayFailure()
    {
        $params = $this->params;
        $params['password'] = array();

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testPass_EmptyNullFailure()
    {
        $params = $this->params;
        $params['password'] = null;

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testPass_ArrayFailure()
    {
        $params = $this->params;
        $params['password'] = array(1,23,4);

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testVHost_NotSetFailure()
    {
        $params = $this->params;
        unset($params['vhost']);

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testVHost_EmptyStringFailure()
    {
        $params = $this->params;
        $params['vhost'] = '';

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testVHost_EmptyArrayFailure()
    {
        $params = $this->params;
        $params['vhost'] = array();

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testVHost_EmptyNullFailure()
    {
        $params = $this->params;
        $params['vhost'] = null;

        $conn = new AmqpConnector($params);
    }

    /**
     * @expectedException   Exception
     * @return              null
     */
    public function testVHost_ArrayFailure()
    {
        $params = $this->params;
        $params['vhost'] = array(1,23,4);

        $config = new AmqpConnector($params);
    }
}
