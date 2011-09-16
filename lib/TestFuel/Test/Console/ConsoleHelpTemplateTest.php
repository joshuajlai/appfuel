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
	public function testDefaultConstructor()
	{
		$formatter = $this->template->getViewFormatter();
		$this->assertInstanceOf(
			'Appfuel\View\Formatter\TextFormatter',
			$formatter
		);

		$this->assertEquals(array(), $this->template->getAllAssigned());
		$this->assertEquals(1, $this->template->getStatusCode());

		$expected = 'unkown error has occured';
		$this->assertEquals($expected, $this->template->getStatusText());
		$this->assertTrue($this->template->isErrorTitleEnabled());
	}

	/**
	 * @depends	testDefaultConstructor
	 * @return	null
	 */
	public function testSetStatusCode()
	{
		/* you can have a code of zero */	
		$this->assertSame(
			$this->template,
			$this->template->setStatusCode(500),
			'uses a fluent interface'
		);
		$this->assertEquals(500, $this->template->getStatusCode());

		$this->assertSame(
			$this->template,
			$this->template->setStatusCode(0),
			'uses a fluent interface'
		);
		$this->assertEquals(0, $this->template->getStatusCode());
	
		$this->assertSame(
			$this->template,
			$this->template->setStatusCode(-1),
			'uses a fluent interface'
		);
		$this->assertEquals(-1, $this->template->getStatusCode());

		$this->assertSame(
			$this->template,
			$this->template->setStatusCode('255'),
			'uses a fluent interface'
		);
		$this->assertEquals('255', $this->template->getStatusCode());	
	}

	/**
	 * @depends	testDefaultConstructor
	 * @return	null
	 */
	public function testSetStatusText()
	{
		$this->assertSame(
			$this->template,
			$this->template->setStatusText('I am some text'),
			'uses a fluent interface'
		);
		$this->assertEquals('I am some text', $this->template->getStatusText());

		/* you can have a code that is an empty string */	
		$this->assertSame(
			$this->template,
			$this->template->setStatusText(''),
			'uses a fluent interface'
		);
		$this->assertEquals('', $this->template->getStatusText());	
	}

	/**
	 * @depends	testDefaultConstructor
	 * @return	null
	 */
	public function testSetStatus()
	{
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
	 * @depends	testDefaultConstructor
	 * @return null
	 */
	public function testEnableDisableIsErrorTitleEnabled()
	{
		$this->assertTrue($this->template->isErrorTitleEnabled());
		$this->assertSame(
			$this->template,
			$this->template->disableErrorTitle(),
			'uses a fluent interface'
		);

		$this->assertFalse($this->template->isErrorTitleEnabled());
		$this->assertSame(
			$this->template,
			$this->template->enableErrorTitle(),
			'uses a fluent interface'
		);
		$this->assertTrue($this->template->isErrorTitleEnabled());


	}

	/**
	 * @depends	testEnableDisableIsErrorTitleEnabled
	 * @return	null
	 */
	public function testBuild()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => array(1,2,3)
		);

		$this->template->setStatus(-1, "Some error has occured")
					   ->load($data);

		$expected  = '[-1] Some error has occured' . PHP_EOL;
		$expected .= 'bar 1 2 3';
		$this->assertEquals($expected, $this->template->build());

		$this->template->disableErrorTitle();
		$expected = 'bar 1 2 3';
		$this->assertEquals($expected, $this->template->build());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetStatusCodeNotScalarArray_Failure()
	{
		$this->template->setStatusCode(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetStatusCodeNotScalarObj_Failure()
	{
		$this->template->setStatusCode(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetStatusTextNotStringArray_Failure()
	{
		$this->template->setStatusText(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetStatusTextNotStringObj_Failure()
	{
		$this->template->setStatusText(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetStatusTextNotStringInt_Failure()
	{
		$this->template->setStatusText(12345);
	}
}
