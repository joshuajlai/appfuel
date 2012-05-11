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
namespace TestFuel\Unit\Http;

use StdClass,
	SplFileInfo,
	Appfuel\Http\HttpStatus,
	Appfuel\Http\HttpResponse,
	Appfuel\Http\HttpHeaderField,
	TestFuel\TestCase\BaseTestCase;

/**
 * A value object that handles mapping of default text for a given status
 */
class HttpResponseTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HttpResponse
	 */
	protected $response = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->response = new HttpResponse();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->response = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Http\HttpResponseInterface',
			$this->response
		);
	}

	/**
	 * The response construct has four paramters:
	 * 1) the content body default to an empty string
	 * 2) the http protocal version	defaults to 1.0
	 * 3) the status	status code and text in a HttpStatusInterface
	 * 4) headers		optional headers to be applied before sending content
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultValues()
	{
		$this->assertEquals('', $this->response->getContent());
		$this->assertEquals('1.0', $this->response->getProtocolVersion());
		
		$status = $this->response->getStatus();
		$this->assertInstanceOf(
			'Appfuel\Http\HttpStatus',
			$status
		);

		$this->assertEquals(200, $status->getCode());
		$this->assertEquals('OK', $status->getText());

		$this->assertEquals(array(), $this->response->getAllHeaders());

		/* the status line is calculated in the constructor based on
		 * the protocol version and status
		 */
		$expected = 'HTTP/1.0 200 OK';
		$this->assertEquals($expected, $this->response->getStatusLine());
	}

	/**
	 * The default version when create a response is 1.0. We will now create
	 * a response for version 1.1
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorWithVersion1dot1()
	{
		$response = new HttpResponse('my content', null, '1.1');
		$this->assertEquals('my content', $response->getContent());
		$this->assertEquals('1.1', $response->getProtocolVersion());

		$expected = 'HTTP/1.1 200 OK';
		$this->assertEquals($expected, $response->getStatusLine());
		$this->assertEquals(array(), $response->getAllHeaders());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testContructorWithStatus()
	{
		$status = new HttpStatus(201);
		$response = new HttpResponse('my content', $status, '1.1');
		$this->assertEquals('my content', $response->getContent());
		$this->assertEquals('1.1', $response->getProtocolVersion());
		$this->assertSame($status, $response->getStatus());

		$expected = "HTTP/1.1 $status";
		$this->assertEquals($expected, $response->getStatusLine());
		$this->assertEquals(array(), $response->getAllHeaders());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testContructorWithProtocolStatus()
	{
		$content = 'this is my content';
		$version = '1.1';
		$status = new HttpStatus(202);
		$headers = array(
			'Status: 404 Not Found',
			'WWW-Authenticate: Negotiate',
			'Content-type: application/pdf',
		);
			
		$response = new HttpResponse($content, $status, $version, $headers);
		$this->assertEquals($content, $response->getContent());
		$this->assertEquals($version, $response->getProtocolVersion());
		$this->assertSame($status, $response->getStatus());

		$expected = "HTTP/1.1 $status";
		$this->assertEquals($expected, $response->getStatusLine());
		$this->assertEquals($headers, $response->getAllHeaders());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetAddHeader()
	{
		$this->assertEquals(array(), $this->response->getAllHeaders());
	
		$header1 = 'Status: 404 Not Found';
		$this->assertSame(
			$this->response,
			$this->response->addHeader($header1),
			'uses fluent interface'
		);

		$expected = array($header1);
		$this->assertEquals($expected, $this->response->getAllHeaders());


		$header2 = 'WWW-Authenticate: Negotiate';
		$this->assertSame(
			$this->response,
			$this->response->addHeader($header2),
			'uses fluent interface'
		);
		$expected = array($header1, $header2);
		$this->assertEquals($expected, $this->response->getAllHeaders());


		$header3 = 'Content-type: application/pdf';
		$this->assertSame(
			$this->response,
			$this->response->addHeader($header3),
			'uses fluent interface'
		);
		$expected = array($header1, $header2, $header3);
		$this->assertEquals($expected, $this->response->getAllHeaders());
	}

	/**
	 * Currently we do not check for duplicates
	 *
	 * @depends	testGetAddHeader
	 * @return	null
	 */
	public function testAddHeaderDuplicates()
	{
		$this->assertEquals(array(), $this->response->getAllHeaders());
	
		$header1 = 'Status: 404 Not Found';
		$this->response->addHeader($header1);
		$this->response->addHeader($header1);
		$this->response->addHeader($header1);
		$expected = array($header1);
		$this->assertEquals($expected, $this->response->getAllHeaders());
	}

	/**
	 * @depends testGetAddHeader
	 * @return	null
	 */	
	public function testLoadHeaders()
	{
		$headers = array(
			'Status: 404 Not Found',
			'WWW-Authenticate: Negotiate',
			'Content-type: application/pdf',
		);
		
		$this->assertEquals(array(), $this->response->getAllHeaders());
		$this->assertSame(
			$this->response,
			$this->response->loadHeaders($headers),
			'uses fluent interface'
		);
		$this->assertEquals($headers, $this->response->getAllHeaders());
	}

	/**
	 * Whenever the status chages the status line header is updated.
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetStatus()
	{
		/* default status is 200 */
		$result = $this->response->getStatus();
		$this->assertInstanceOf('Appfuel\Http\HttpStatus',$result);
		$this->assertEquals(200, $result->getCode());


		$expected = 'HTTP/1.0 200 OK';
		$statusLine = $this->response->getStatusLine();
		$this->assertEquals($expected, $statusLine);

		$status = new HttpStatus(400);
		$this->assertSame(
			$this->response,
			$this->response->setStatus($status),
			'exposes a fluent interface'
		);
		$this->assertEquals($status, $this->response->getStatus());

		$expected = 'HTTP/1.0 400 Bad Request';
		$statusLine = $this->response->getStatusLine();
		$this->assertEquals($expected, $statusLine);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsValidContent()
	{
		$this->assertTrue($this->response->isValidContent('i am a string'));
		$this->assertTrue($this->response->isValidContent(''));
		$this->assertTrue($this->response->isValidContent(0));
		$this->assertTrue($this->response->isValidContent(1234));
		$this->assertTrue($this->response->isValidContent(1234.1234));

		/* SplFileInfo is known to support __toString */
		$file = new SplFileInfo('/my/path');
		$this->assertTrue($this->response->isValidContent($file));
		
		/* user defined class known to support __toString */
		$status = new HttpStatus();
		$this->assertTrue($this->response->isValidContent($status));

		/* StdClass does not support __toString */
		$obj = new StdClass();
		$this->assertFalse($this->response->isValidContent($obj));
		$this->assertFalse($this->response->isValidContent(array()));
		$this->assertFalse($this->response->isValidContent(array(1,2,3)));
	}

	/**
	 * @depends	testIsValidContent
	 * @return	null
	 */
	public function testGetSetContentString()
	{
		$data = 'I am not the default string';
		$this->assertNotEquals($data, $this->response->getContent());
		$this->assertSame(
			$this->response,
			$this->response->setContent($data),
			'exposes fluent interface'
		);
		$this->assertEquals($data, $this->response->getContent());	
	}
	
	/**
	 * setContent always converts any object supporting to string into a 
	 * string and assigns the string. getContent always returns a string
	 *
	 * @depends	testIsValidContent
	 * @return	null
	 */
	public function testGetSetContentObjectSupportingToString()
	{
		$data = new HttpStatus();
		$this->assertNotEquals($data, $this->response->getContent());
		$this->assertSame(
			$this->response,
			$this->response->setContent($data),
			'exposes fluent interface'
		);
		
		$this->assertEquals((string)$data, $this->response->getContent());	
	}

	/**
	 * @depends testGetSetContentString
	 * @return	null
	 */
	public function testRenderContentNonEmptyString()
	{
		$content = "this is my content";
		$response = new HttpResponse($content);
		$this->expectOutputString($content);
		$response->renderContent();
	}

	/**
	 * @depends testGetSetContentString
	 * @return	null
	 */
	public function testRenderContentObjectSupportsToString()
	{
		$path = "/some/path/of/mine";
		$content = new SplFileInfo($path);
		$response = new HttpResponse($content);
		$this->expectOutputString($path);
		$response->renderContent();
	}
	
	/**
	 * @depends testIsValidContent
	 * @return	null
	 */
	public function testRenderContentEmptyString()
	{
		$response = new HttpResponse();
		$this->expectOutputString('');
		$response->renderContent();
	}
}
