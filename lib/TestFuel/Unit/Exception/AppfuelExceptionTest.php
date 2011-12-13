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
namespace TestFuel\Test\Exception;

use StdClass,
	Exception,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Exception\AppfuelException;

/**
 * We extend the php exception and add namespaces and tags to be used to 
 * log aggregators, so we can search through and filter different exceptions
 * based on keywords or namespaces.
 */
class AppfuelExceptionTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	AppfuelException
	 */
	protected $exception = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->exception = new AppfuelException();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->exception = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Exception',
			$this->exception
		);

		$this->assertInstanceOf(
			'Appfuel\Exception\AppfuelExceptionInterface',
			$this->exception
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultConstructor()
	{
		$this->assertEquals(0, $this->exception->getCode());
		$this->assertEquals('', $this->exception->getMessage());
		$this->assertNull($this->exception->getPrevious());
		$this->assertEquals('', $this->exception->getNamespace());
		$this->assertEquals('', $this->exception->getTags());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorParams()
	{
		$msg  = 'this is my message';
		$code = 12345;
		$prev = new Exception('new error');
		$ns   = 'my-namespace';
		$tags = 'tag a b c';
		
		$e = new AppfuelException($msg);
		$this->assertEquals($msg, $e->getMessage());

		$e = new AppfuelException($msg, $code);
		$this->assertEquals($msg, $e->getMessage());
		$this->assertEquals($code, $e->getCode());

		$e = new AppfuelException($msg, $code, $prev);
		$this->assertEquals($msg, $e->getMessage());
		$this->assertEquals($code, $e->getCode());
		$this->assertSame($prev, $e->getPrevious());
		
		$e = new AppfuelException($msg, $code, $prev, $ns);
		$this->assertEquals($msg, $e->getMessage());
		$this->assertEquals($code, $e->getCode());
		$this->assertSame($prev, $e->getPrevious());
		$this->assertEquals($ns, $e->getNamespace());
	
		$e = new AppfuelException($msg, $code, $prev, $ns, $tags);
		$this->assertEquals($msg, $e->getMessage());
		$this->assertEquals($code, $e->getCode());
		$this->assertSame($prev, $e->getPrevious());
		$this->assertEquals($ns, $e->getNamespace());
		$this->assertEquals($tags, $e->getTags());
	}

	/**
	 * @return	null
	 */
	public function provideBadStrings()
	{
		return	array(
			array(''),
			array(' '),
			array("\t"),
			array(" \t"),
			array("\n"),
			array(" \t\n"),
			array(123434),
			array(1.23),
			array(0),
			array(true),
			array(false),
			array(array()),
			array(array(1,2,3)),
			array(new StdClass()),
		);
	}

	/**
	 * @dataProvider	provideBadStrings
	 * @return			null
	 */
	public function testConstructorNsBadParam($param)
	{
		$e = new AppfuelException('msg', 1, null, $param);
		$this->assertEquals('', $e->getNamespace());
	}

	/**
	 * @dataProvider	provideBadStrings
	 * @return			null
	 */
	public function testConstructorTagsBadParam($param)
	{
		$e = new AppfuelException('msg', 1, null, 'ns', $param);
		$this->assertEquals('', $e->getTags());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testToStringNs()
	{
		$e = new AppfuelException("my message", 55, null, 'my-namespace');
		$result = $e->__toString();
		$this->assertContains('my-namespace', $result);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testToStringTags()
	{
		$e = new AppfuelException("my message", 55, null, null, 'my tag a b');
		$result = $e->__toString();
		$this->assertContains('my tag a b', $result);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testToStringBoth()
	{
		$e = new AppfuelException("my message", 55, null, 'my\ns', 'a b');
		$result = $e->__toString();
		$this->assertContains('my\ns', $result);
		$this->assertContains('a b', $result);
	}



}
