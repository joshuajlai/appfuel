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
namespace TestFuel\Test\View;

use StdClass,
	SplFileInfo,
	Appfuel\View\JsonTemplate,
	TestFuel\TestCase\BaseTestCase;

/**
 * The Html doc is the main template for any html page. Its primary 
 * responibility is to manage all the elements of the document itself like the
 * head, body and scripts that get added to the bottom of the body.
 */
class JsonTemplateTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $template = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->template	= new JsonTemplate();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->template = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\JsonTemplateInterface',
			$this->template
		);

		$this->assertInstanceOf(
			'Appfuel\View\ViewTemplateInterface',
			$this->template
		);
	}

	/**
	 * When no parameters are passed the formatter is a json formatter
	 * and no data is added. Also the code is 200 and text is OK
	 *
	 * @return	null
	 */
	public function testDefaultConstructor()
	{
		$formatter = $this->template->getViewFormatter();
		$this->assertInstanceOf(
			'Appfuel\View\Formatter\JsonFormatter',
			$formatter
		);

		$this->assertEquals(array(), $this->template->getAllAssigned());
		$this->assertEquals(200, $this->template->getStatusCode());
		$this->assertEquals('OK', $this->template->getStatusText());

		$expected = '{"code":200,"message":"OK","data":[]}';
		$this->assertEquals($expected, $this->template->build());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetStatusCode()
	{
		$this->assertEquals(200, $this->template->getStatusCode());
		$this->assertSame(
			$this->template,
			$this->template->setStatusCode(500),
			'uses a fluent interface'
		);
		$this->assertEquals(500, $this->template->getStatusCode());

		/* you can have a code of zero */	
		$this->assertSame(
			$this->template,
			$this->template->setStatusCode(0),
			'uses a fluent interface'
		);
		$this->assertEquals(0, $this->template->getStatusCode());
	
		/* you can have a code that is a string */
		$this->assertSame(
			$this->template,
			$this->template->setStatusCode("i am a code"),
			'uses a fluent interface'
		);
		$this->assertEquals('i am a code', $this->template->getStatusCode());

		/* you can have a code that is an empty string */	
		$this->assertSame(
			$this->template,
			$this->template->setStatusCode(''),
			'uses a fluent interface'
		);
		$this->assertEquals('', $this->template->getStatusCode());	
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetStatusText()
	{
		$this->assertEquals('OK', $this->template->getStatusText());
		$this->assertSame(
			$this->template,
			$this->template->setStatusCode('I am some text'),
			'uses a fluent interface'
		);
		$this->assertEquals('I am some text', $this->template->getStatusCode());

		/* you can have a code that is an empty string */	
		$this->assertSame(
			$this->template,
			$this->template->setStatusText(''),
			'uses a fluent interface'
		);
		$this->assertEquals('', $this->template->getStatusText());	
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetStatus()
	{
		$this->assertEquals(200, $this->template->getStatusCode());
		$this->assertEquals('OK', $this->template->getStatusText());

		$this->assertSame(
			$this->template,
			$this->template->setStatus(500, 'Error'),
			'uses a fluent interface'
		);

		$this->assertEquals(500, $this->template->getStatusCode());
		$this->assertEquals('Error', $this->template->getStatusText());

		$this->assertSame(
			$this->template,
			$this->template->setStatus(0, ''),
			'uses a fluent interface'
		);
		$this->assertEquals(0, $this->template->getStatusCode());
		$this->assertEquals('', $this->template->getStatusText());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuild()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => array(1,2,3)
		);

		$this->template->setStatus(3001, "Custom Message")
					   ->load($data);

		$expected  = '{"code":3001,"message":"Custom Message",';
		$expected .= '"data":{"foo":"bar","baz":[1,2,3]}}';

		$this->assertEquals($expected, $this->template->build());		
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetStatusCodeNotScalarArray_Failure()
	{
		$this->template->setStatusCode(array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetStatusCodeNotScalarObj_Failure()
	{
		$this->template->setStatusCode(new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetStatusTextNotStringArray_Failure()
	{
		$this->template->setStatusText(array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetStatusTextNotStringObj_Failure()
	{
		$this->template->setStatusText(new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetStatusTextNotStringInt_Failure()
	{
		$this->template->setStatusText(12345);
	}
}
