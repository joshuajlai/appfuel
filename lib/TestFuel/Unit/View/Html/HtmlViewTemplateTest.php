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
namespace TestFuel\Unit\View\Html;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\HtmlViewTemplate;

/**
 * 
 */
class HtmlViewTemplateTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlViewTemplate
	 */
	protected $template = null;

	/**
	 * First param of constructor, which is the location of the tpl file
	 * @var string
	 */
	protected $tplFile = null;
	
	/**
	 * Second param of the constructor, which is the location of the inline
	 * js tpl file
	 * @jsFile
	 */
	protected $jsFile = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->tplFile = 'appfuel/tpl/page/welecome/welcome.phtml';
		$this->template = new HtmlViewTemplate($this->tplFile);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->page = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlViewInterface',
			$this->template
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetFile()
	{
		$this->assertEquals($this->tplFile, $this->template->getFile());
	}

	/**
	 * @depends				testInterface
	 * @return				null
	 */
	public function testGetLayoutClassDefault()
	{
		$this->assertNull($this->template->getLayoutClass());
	}

	/**
	 * @dataProvider	provideNonEmptyStringsNoNumbers
	 * @depends			testInterface
	 * @return			null
	 */
	public function testSetLayoutClass($class)
	{
		$this->assertSame(
			$this->template,
			$this->template->setLayoutClass($class)
		);
		$this->assertEquals($class, $this->template->getLayoutClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideEmptyStrings
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetLayoutClassEmptyString_Failure($class)
	{
		$this->template->setLayoutClass($class);
	}

	/**
	 * @depends				testInterface
	 * @return				null
	 */
	public function testGetHtmlPageClassDefault()
	{
		$this->assertNull($this->template->getHtmlPageClass());
	}

	/**
	 * @dataProvider	provideNonEmptyStringsNoNumbers
	 * @depends			testInterface
	 * @return			null
	 */
	public function testSetHtmlPageClass($class)
	{
		$this->assertSame(
			$this->template,
			$this->template->setHtmlPageClass($class)
		);
		$this->assertEquals($class, $this->template->getHtmlPageClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideEmptyStrings
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetHtmlPageClassEmptyString_Failure($class)
	{
		$this->template->setHtmlPageClass($class);
	}

	/**
	 * @return	null
	 */
	public function testGetSetInlineJsKey()
	{
		$this->assertEquals('initjs', $this->template->getInlineJsKey());
		$key = 'my-jskey';
		$this->assertSame(
			$this->template,
			$this->template->setInlineJsKey($key)
		);
		$this->assertEquals($key, $this->template->getInlineJsKey());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetInlineJsKeyInvalidString_Failure($str)
	{
		$this->template->setInlineJsKey($str);
	}

	/**
	 * @return	null
	 */
	public function testGetSetInlineJsTemplate()
	{
		$this->assertFalse($this->template->isInlineJsTemplate());

		$template = $this->getMock('Appfuel\View\ViewInterface');
		$this->assertSame(
			$this->template,
			$this->template->setInlineJsTemplate($template)
		);
		$this->assertSame($template, $this->template->getInlineJsTemplate());
		$this->assertTrue($this->template->isInlineJsTemplate());
	}

	/**
	 * @return	null
	 */
	public function testGetSetInlineJsTemplateAsString()
	{
		$this->assertFalse($this->template->isInlineJsTemplate());
		$path = 'appfuel/html/tpl/view/welcome/welcome-init.phtml';
		$this->assertSame(
			$this->template,
			$this->template->setInlineJsTemplate($path)
		);

		$template = $this->template->getInlineJsTemplate();
		$this->assertInstanceOf(
			'Appfuel\View\FileViewTemplate',
			$template
		);
		$this->assertTrue($this->template->isInlineJsTemplate());
	}

	/**
	 * @return	null
	 */
	public function testAssignInlineJs()
	{
		$this->assertFalse($this->template->isInlineJsTemplate());
		$path = 'appfuel/html/tpl/view/welcome/welcome-init.phtml';
		$this->template->setInlineJsTemplate($path);

		$template = $this->template->getInlineJsTemplate();
		$this->assertSame(
			$this->template,
			$this->template->assignInlineJs('foo', 'bar')
		);

		$this->assertEquals('bar', $template->get('foo'));
	}
}
