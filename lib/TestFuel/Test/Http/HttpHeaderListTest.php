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
	Appfuel\Http\HttpHeaderList,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the ability of the header list to manage headers
 */
class HttpHeaderListTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HttpHeaderList
	 */
	protected $list = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->list = new HttpHeaderList();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->list = null;
	}

	/**
	 * @return	null
	 */
	public function provideBadHeader()
	{
		return array(
			array(''),
			array(' '),
			array("\t"),
			array("\n"),
			array(" \t\n"),
			array("              \t          \n\n       "),
			array(122345),
			array(1.23445),
			array(array()),
			array(array(1,2,3,4)),
			array(new StdClass()),
			array(true),
			array(false),
			array(null),
			array(0),
			array(-1)
		);
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Http\HttpHeaderListInterface',
			$this->list
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCountAddIsGetAllHeaders()
	{
		$this->assertEquals(0, $this->list->count());
		$this->assertEquals(array(), $this->list->getAllHeaders());

		$header1 = 'Location: http//www.example.com/';
		$this->assertFalse($this->list->isHeader($header1));

		$this->assertTrue($this->list->addHeader($header1));
		$this->assertTrue($this->list->isHeader($header1));
		$this->assertEquals(1, $this->list->count());

		$expected = array($header1);
		$this->assertEquals($expected, $this->list->getAllHeaders());

		/* will not add dublicates */
		$this->assertFalse($this->list->addHeader($header1));
		$this->assertEquals($expected, $this->list->getAllHeaders());
	
		/* case insensitive */	
		$this->assertFalse($this->list->addHeader(strtoupper($header1)));	
		$this->assertEquals($expected, $this->list->getAllHeaders());

		$header2 = "HTTP/1.0 404 Not Found";
		$this->assertTrue($this->list->addHeader($header2));
		$this->assertEquals(2, $this->list->count());
		$this->assertTrue($this->list->isHeader($header1));
		$this->assertTrue($this->list->isHeader($header2));
	
		$expected = array($header1, $header2);
		$this->assertEquals($expected, $this->list->getAllHeaders());

		$header3 = "Status: 404 Not Found";
		$this->assertTrue($this->list->addHeader($header3));
		$this->assertEquals(3, $this->list->count());
		$this->assertTrue($this->list->isHeader($header1));
		$this->assertTrue($this->list->isHeader($header2));
		$this->assertTrue($this->list->isHeader($header3));
	
		$expected = array($header1, $header2, $header3);
		$this->assertEquals($expected, $this->list->getAllHeaders());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideBadHeader
	 * @depends				testInterface
	 * @return				null
	 */
	public function testAddHeader_Failure($header)
	{
		$this->list->addHeader($header);
	}

	/**
	 * Load calls addHeader so any invalid header in the array will cause
	 * an execption. Load will call rewind once all the headers are added 
	 * 
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLoad()
	{
		$headers = array(
			'Location: http//www.example.com/',
			'WWW-Authenticate: Negotiate',
			'HTTP/1.0 404 Not Found',
		);
		$this->assertEquals(0, $this->list->count());
		$this->assertNull($this->list->loadHeaders($headers));
		$this->assertEquals(3, $this->list->count());
		$this->assertEquals(0, $this->list->key());
		$this->assertEquals($headers, $this->list->getAllHeaders());

		/* adding an empty array does not do anything */
		$this->assertNull($this->list->loadHeaders(array()));
		$this->assertEquals($headers, $this->list->getAllHeaders());
	}

	/**
	 * @depends	testLoad
	 * @return	null
	 */
	public function testIterator()
	{
		$headers = array(
			'Location: http//www.example.com/',
			'WWW-Authenticate: Negotiate',
			'HTTP/1.0 404 Not Found',
		);
		
		$this->list->loadHeaders($headers);
		$this->assertEquals(0, $this->list->key());
		$this->assertTrue($this->list->valid());
		$this->assertEquals($headers[0], $this->list->current());
		$this->assertEquals($headers[0], $this->list->getHeader());
	
		$this->assertNull($this->list->next());
		$this->assertTrue($this->list->valid());
		$this->assertEquals(1, $this->list->key());
		$this->assertEquals($headers[1], $this->list->current());
		$this->assertEquals($headers[1], $this->list->getHeader());
	
		$this->assertNull($this->list->next());
		$this->assertTrue($this->list->valid());
		$this->assertEquals(2, $this->list->key());
		$this->assertEquals($headers[2], $this->list->current());
		$this->assertEquals($headers[2], $this->list->getHeader());
	
		$this->assertNull($this->list->next());
		$this->assertFalse($this->list->valid());
		$this->assertNull($this->list->key());
		$this->assertFalse($this->list->current());
		$this->assertFalse($this->list->getHeader());
			
		$this->assertNull($this->list->rewind());
		$this->assertEquals(0, $this->list->key());
		$this->assertTrue($this->list->valid());
		$this->assertEquals($headers[0], $this->list->current());
		$this->assertEquals($headers[0], $this->list->getHeader());
	}
}

