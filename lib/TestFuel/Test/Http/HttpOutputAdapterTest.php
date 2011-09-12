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
namespace TestFuel\Test\Http;

use StdClass,
	Appfuel\Http\HttpHeaderField,
	Appfuel\Http\HttpOutputAdapter,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HttpOutputAdapterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HttpOutputAdapter
	 */
	protected $adapter = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->adapter = new HttpOutputAdapter();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->adapter = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Output\EngineAdapterInterface',
			$this->adapter
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\Output\AdapterHeaderInterface',
			$this->adapter
		);
	}
	
	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor()
	{
		$response = $this->adapter->getResponse();
		$this->assertInstanceOf(
			'Appfuel\Http\HttpResponse',
			$response
		);

		$response = $this->getMock(
			'Appfuel\Framework\Http\HttpResponseInterface'
		);
		$adapter = new HttpOutputAdapter($response);
		$this->assertSame($response, $adapter->getResponse());
	}

	/**
	 * @depends	testConstructor
	 * @return	null
	 */
	public function testAddResponseHeaders()
	{
		$response = $this->adapter->getResponse();
		$this->assertEquals(array(), $response->getHeaders());

		$headers = array(
			new HttpHeaderField('WWW-Authenticate: Negotiate'),
			new HttpHeaderField('Content-type: application/pdf')
		);
		$this->assertSame(
			$this->adapter,
			$this->adapter->addResponseHeaders($headers),
			'uses fluent interface'
		);

		$this->assertEquals($headers, $response->getHeaders());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testOutput()
	{
		$response = $this->getMockBuilder(
			'Appfuel\Framework\Http\HttpResponseInterface'
		)->setMethods(array('send', 'setContent'))
		 ->getMock();
		
		$response->expects($this->once())
				 ->method('setContent')
				 ->will($this->returnValue(null));

		$response->expects($this->once())
				 ->method('send')
			 ->will($this->returnCallback(array($this, 'outputCallBack')));

		$adapter = new HttpOutputAdapter($response);
		
		$this->expectOutputString('I am a response');
		$adapter->output('content does not matter for this test');

	}

	public function outputCallBack()
	{
		echo "I am a response";
	}
}
