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

        $this->message->add('route', 'should_not_work');
        $this->assertFalse($this->message->isRoute());

        $route = $this->getMock('Appfuel\Framework\RouteInterface');
        $this->message->add('route', $route);
        $this->assertTrue($this->message->isRoute());
    }

    /**
     * Testing how isRequest reacts when the request is present and abesent
     * from the message. The key the message uses for request is 'request'
     *
     * @return null
     */
    public function testIsRequest()
    {
        $this->assertFalse($this->message->isRequest());

        $this->message->add('request', 'should_not_work');
        $this->assertFalse($this->message->isRequest());

        $request = $this->getMock('Appfuel\Framework\Request\RequestInterface');
        $this->message->add('request', $request);
        $this->assertTrue($this->message->isRequest());
    }

    /**
     * Testing how isDocreacts when the doc is present and abesent
     * from the message. The key the message uses for doc is 'doc'
     *
     * @return null
     */
    public function testIsDoc()
    {
        $this->assertFalse($this->message->isDoc());

        $this->message->add('client', 'should_not_work');
        $this->assertFalse($this->message->isDoc());

        $doc = $this->getMock('Appfuel\Framework\View\DocumentInterface');
        $this->message->add('doc', $doc);
        $this->assertTrue($this->message->isDoc());
    }

    /**
     * @return null
     */
    public function testIsDocRender()
    {
        /* default value of isDocRender is true */
        $this->assertTrue($this->message->isDocRender());

        $this->assertSame(
            $this->message,
            $this->message->disableDocRender()
        );
        $this->assertFalse($this->message->isDocRender());

        $this->assertSame(
            $this->message,
            $this->message->enableDocRender()
        );
        $this->assertTrue($this->message->isDocRender());
    }
}

