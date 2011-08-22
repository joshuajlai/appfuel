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
	Appfuel\App\Context;

/**
 *
 */
class ContextTest extends ParentTestCase
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
        $this->message = new Context();
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
    public function testGetSetIsRequest()
    {
        $this->assertFalse($this->message->isRequest());

        $request = $this->getMock(
			'Appfuel\Framework\App\Request\RequestInterface'
		);
        $this->message->setRequest($request);
        $this->assertTrue($this->message->isRequest());
		$this->assertSame($request, $this->message->getRequest());
    }

	/**
     *
     * @return null
     */
    public function testGetSetResponseType()
    {
		$this->assertNull($this->message->getResponseType());
		$type = 'responseType';
		$this->assertSame(
			$this->message,
			$this->message->setResponseType($type),
			'must use a fluent interface'
		);

		$this->assertEquals($type, $this->message->getResponseType());
    }

	/**
	 * Test that when Request::isResponseType is false the routes response
	 * type is set
	 *
	 * @return null
	 */
	public function testCalculateResponseTypeNotInRequest()
	{
		$mockClass = 'Appfuel\App\Request';
        $request  = $this->getMockBuilder($mockClass)
						 ->disableOriginalConstructor()
						->setMethods(array('isResponseType'))
						->getMock();

		$request->expects($this->any())
				->method('isResponseType')
				->will($this->returnValue(false));

		$this->message->setRequest($request);
        
		$route = $this->getMock('Appfuel\Framework\App\Route\RouteInterface');
		$route->expects($this->any())
			  ->method('getResponseType')
			  ->will($this->returnValue('html'));
		$this->message->setRoute($route);

		$result = $this->message->calculateResponseType($request, $route);
		$this->assertEquals('html', $result);
	}

	/**
	 * Test that when Request::isResponseType is false the routes response
	 * type is set
	 *
	 * @return null
	 */
	public function testCalculateResponseTypeInRequest()
	{
		$mockClass = 'Appfuel\App\Request';
        $request  = $this->getMockBuilder($mockClass)
						 ->disableOriginalConstructor()
						->setMethods(array('isResponseType', 'getResponseType'))
						->getMock();

		$request->expects($this->any())
				->method('isResponseType')
				->will($this->returnValue(true));

		$request->expects($this->any())
				->method('getResponseType')
				->will($this->returnValue('json'));


		$this->message->setRequest($request);
        
		$route = $this->getMock('Appfuel\Framework\App\Route\RouteInterface');
		$route->expects($this->any())
			  ->method('getResponseType')
			  ->will($this->returnValue('html'));
		$this->message->setRoute($route);

		$result = $this->message->calculateResponseType($request, $route);
		$this->assertEquals('json', $result);
	}

	/**
	 * Test that when Request::isResponseType is true by the data is empty then
	 * routes response type is set
	 *
	 * @return null
	 */
	public function testCalculateResponseTypeInRequestButEmpty()
	{
		$mockClass = 'Appfuel\App\Request';
        $request  = $this->getMockBuilder($mockClass)
						 ->disableOriginalConstructor()
						->setMethods(array('isResponseType', 'getResponseType'))
						->getMock();

		$request->expects($this->any())
				->method('isResponseType')
				->will($this->returnValue(true));

		$request->expects($this->any())
				->method('getResponseType')
				->will($this->returnValue(''));


		$this->message->setRequest($request);
        
		$route = $this->getMock('Appfuel\Framework\App\Route\RouteInterface');
		$route->expects($this->any())
			  ->method('getResponseType')
			  ->will($this->returnValue('html'));
		$this->message->setRoute($route);

		$result = $this->message->calculateResponseType($request, $route);
		$this->assertEquals('html', $result);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testSetResponseTypeBadTypeEmpty()
	{
		$this->message->setResponseType('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testSetResponseTypeBadTypeArray()
	{
		$this->message->setResponseType(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testSetResponseTypeBadTypeInt()
	{
		$this->message->setResponseType(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testSetResponseTypeBadTypeObj()
	{
		$this->message->setResponseType(new \StdClass());
	}
}
