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
namespace Test\Appfuel\App\View;

use Test\AfTestCase as ParentTestCase,
	Appfuel\App\View\Html\Layout;

/**
 */
class LayoutTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $layout = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		
		$this->layout = new Layout();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->layout);
	}

	/**
	 * @return null
	 */	
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'Appfuel\App\View\FileTemplate',
			$this->layout,
			'The html doc is also a template'
		);

		$this->assertInstanceOf(
			'Appfuel\App\View\Data',
			$this->layout,
			'The html doc must extend the view data class'
		);

		$this->assertInstanceOf(
			'Appfuel\Data\Dictionary',
			$this->layout,
			'The json doc is also a dictionary'
		);

		$this->assertTrue($this->layout->fileExists('markup'));
		
		/* prove we are storing the file as a string */
		$file = $this->layout->getFile('markup');
		$this->assertInternalType('string', $file);
		$this->assertEquals('grid/grid.phtml', $file);
	}

	/**
	 * Basic test case where the order is provided
	 * @return null
	 */
	public function testAddGetExistsTemplate()
	{
		/* 
		 * add the first template. it should not exist and have no order
		 * before. after it should exist and have an order of 1
		 */
		$name     = 'my-template';
		$template = $this->getMock(
			'\Appfuel\Framework\App\View\ViewInterface'
		);
		$order    = 1;

		$this->assertFalse($this->layout->templateExists($name));
		$this->assertNull($this->layout->getTemplate($name));
		$this->assertFalse($this->layout->getTemplateOrder($name));

		$this->assertSame(
			$this->layout,
			$this->layout->addTemplate($name, $template, $order),
			'must use a fluent interface'
		);
		$this->assertTrue($this->layout->templateExists($name));
		$this->assertSame($template, $this->layout->getTemplate($name));
		$this->assertEquals(1, $this->layout->getTemplateOrder($name));

		/* 
		 * add the second template and test its order and that it exists
		 */
		$name2     = 'my-second-template';
		$template2 = $this->getMock(
			'\Appfuel\Framework\App\View\ViewInterface'
		);
		$order2    = 2;

		$this->assertFalse($this->layout->templateExists($name2));
		$this->assertNull($this->layout->getTemplate($name2));
		$this->assertFalse($this->layout->getTemplateOrder($name2));
		
		$this->assertSame(
			$this->layout,
			$this->layout->addTemplate($name2, $template2, $order2),
			'must use a fluent interface'
		);
		$this->assertTrue($this->layout->templateExists($name2));
		$this->assertSame($template2, $this->layout->getTemplate($name2));
		$this->assertEquals(2, $this->layout->getTemplateOrder($name2));
	}

	public function testAddGetExistsNoOrderProvided()
	{
		$name     = 'my-template';
		$template = $this->getMock('\Appfuel\Framework\App\View\ViewInterface');

		$this->assertFalse($this->layout->templateExists($name));
		$this->assertNull($this->layout->getTemplate($name));
		$this->assertFalse($this->layout->getTemplateOrder($name));

		$this->assertSame(
			$this->layout,
			$this->layout->addTemplate($name, $template),
			'must use a fluent interface'
		);
		$this->assertTrue($this->layout->templateExists($name));
		$this->assertSame($template, $this->layout->getTemplate($name));
		$this->assertEquals(1, $this->layout->getTemplateOrder($name));
	
		$name2     = 'my-second-template';
		$template2 = $this->getMock(
			'\Appfuel\Framework\App\View\ViewInterface'
		);

		$this->assertFalse($this->layout->templateExists($name2));
		$this->assertNull($this->layout->getTemplate($name2));
		$this->assertFalse($this->layout->getTemplateOrder($name2));

		$this->layout->addTemplate($name2, $template2);

		$this->assertTrue($this->layout->templateExists($name2));
		$this->assertSame($template2, $this->layout->getTemplate($name2));
		$this->assertEquals(2, $this->layout->getTemplateOrder($name2));
	}
}
