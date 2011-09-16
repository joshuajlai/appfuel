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
namespace TestFuel\Test\Console;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Console\ConsoleHelpTemplate;

/**
 * The Html doc is the main template for any html page. Its primary 
 * responibility is to manage all the elements of the document itself like the
 * head, body and scripts that get added to the bottom of the body.
 */
class ConsoleHelpTemplateTest extends BaseTestCase
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
		$this->template	= new ConsoleHelpTemplate();
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
			'Appfuel\Framework\Console\ConsoleHelpTemplateInterface',
			$this->template
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\View\ViewCompositeTemplateInterface',
			$this->template
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\View\ViewTemplateInterface',
			$this->template
		);
	}

	/**
	 * When no parameters are passed the formatter is a json formatter
	 * and no data is added. Also the code is 200 and text is OK
	 *
	 * @return	null
	 */
	public function xtestDefaultConstructor()
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
	public function xtestSetStatusCode()
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
	public function xtestSetStatusText()
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
	public function xtestSetStatus()
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
	public function xtestBuild()
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
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function xtestSetStatusCodeNotScalarArray_Failure()
	{
		$this->template->setStatusCode(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function xtestSetStatusCodeNotScalarObj_Failure()
	{
		$this->template->setStatusCode(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function xtestSetStatusTextNotStringArray_Failure()
	{
		$this->template->setStatusText(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function xtestSetStatusTextNotStringObj_Failure()
	{
		$this->template->setStatusText(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function xtestSetStatusTextNotStringInt_Failure()
	{
		$this->template->setStatusText(12345);
	}
}
