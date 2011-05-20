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
namespace Test\Appfuel\App;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\App\Message;

/**
 *
 */
class MessageTest extends ParentTestCase
{
    /**
     * System under test
     * @var Message
     */
    protected $message = null;

    /**
     * @return null
     */
    public function setUp()
    {   
        $this->message = new Message();
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        unset($this->message);
    }

    /**
     * Testing how isRoute reacts when the route is present and abesent
     * from the message. The key message uses for route is 'route'
     *
     * @return null
     */
    public function testIsRoute()
    {
        $this->assertFalse($this->message->isRoute());

        $route = $this->getMock('Appfuel\Framework\App\Route\RouteInterface');
        $this->message->setRoute($route);
        $this->assertTrue($this->message->isRoute());
		$this->assertSame($route, $this->message->getRoute());
    }

    /**
     * Testing how isRequest reacts when the request is present and abesent
     * from the message. The key the message uses for request is 'request'
     *
     * @return null
     */
    public function testIsRequest()
    {
    }
}
