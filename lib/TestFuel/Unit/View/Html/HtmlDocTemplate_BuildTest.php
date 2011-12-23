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
	Appfuel\View\Html\Element\Base,
	Appfuel\View\Html\Element\Script,
	Appfuel\View\Html\HtmlDocTemplate;

/**
 */
class HtmlDocTemplate_BuildTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlDocTemplate
	 */
	protected $htmlDoc = null;

	/**
	 * @var string
	 */
	protected $tagInterface = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->tagInterface = 'Appfuel\View\Html\Element\HtmlTagInterface';
		$this->htmlDoc = new HtmlDocTemplate();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->page = null;
	}

	/**
	 * @return	null
	 */
	public function testDefaultBuildTitleTest()
	{
		$this->assertFalse($this->htmlDoc->isAssigned('html-title'));
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildTitle()
		);
		$this->assertTrue($this->htmlDoc->isAssigned('html-title'));
		
		$title = $this->htmlDoc->get('html-title');
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Title',
			$title
		);

		$this->assertEmpty($title->buildContent());
	}

	/**
	 * @depends	testDefaultBuildTitleTest
	 * @return	null
	 */
	public function testBuildTitleWithContent()
	{
		$content = 'i am a title';
		$this->htmlDoc->setTitle($content);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildTitle()
		);
			
		$title = $this->htmlDoc->get('html-title');
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Title',
			$title
		);

		$this->assertEquals($content, $title->buildContent());
	}

	/**
	 * @return	null
	 */
	public function testDefaultBuildCharset()
	{
		$this->assertFalse($this->htmlDoc->isAssigned('html-charset'));
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildCharset()
		);	
		$this->assertTrue($this->htmlDoc->isAssigned('html-charset'));
	
		$charset = $this->htmlDoc->get('html-charset');
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Meta\Charset',
			$charset
		);
			
		$this->assertEquals(
			'Content-Type', 
			$charset->getAttribute('http-equiv')
		);	
		$this->assertEquals('text/html', $charset->getAttribute('content'));	
		$this->assertEquals('UTF-8', $charset->getAttribute('charset'));
	}

	/**
	 * @depends	testDefaultBuildCharset
	 * @return	null
	 */
	public function testBuildCharset()
	{
		$this->htmlDoc->setCharset('some-charset');
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildCharset()
		);	

		$this->assertTrue($this->htmlDoc->isAssigned('html-charset'));

		$charset = $this->htmlDoc->get('html-charset');
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Meta\Charset',
			$charset
		);
		$this->assertEquals(
			'Content-Type', 
			$charset->getAttribute('http-equiv')
		);	
		$this->assertEquals('text/html', $charset->getAttribute('content'));	
		$this->assertEquals('some-charset', $charset->getAttribute('charset'));
	}

	/**
	 * No base is set by default
	 * 
	 * @return	null
	 */
	public function testDefaultBuildBase()
	{
		$this->assertFalse($this->htmlDoc->isAssigned('html-base'));

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildBase()
		);	

		$this->assertFalse($this->htmlDoc->isAssigned('html-base'));
	}

	/**
	 * @depends	testDefaultBuildBase
	 * @return	null
	 */
	public function testBuildBaseSetBaseTag()
	{
		$base = new Base('some/path');
		$this->htmlDoc->setBaseTag($base);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildBase()
		);	
	
		$this->assertTrue($this->htmlDoc->isAssigned('html-base'));
		$this->assertSame($base, $this->htmlDoc->get('html-base'));
	}

	/**
	 * @return	null
	 */
	public function testDefaultBuildMetaTags()
	{
		$this->assertFalse($this->htmlDoc->isAssigned('html-meta'));
	
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildMeta()
		);

		$this->assertFalse($this->htmlDoc->isAssigned('html-meta'));
		$this->assertNull($this->htmlDoc->get('html-meta'));
	}

	/**
	 * @depends	testDefaultBuildMetaTags
	 * @return	null
	 */
	public function testBuildMetaTag()
	{
		$this->htmlDoc->addMeta('author', 'robert scott');
		$list = $this->htmlDoc->getMetaTags();

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildMeta()
		);
		$this->assertTrue($this->htmlDoc->isAssigned('html-meta'));
		$this->assertSame($list, $this->htmlDoc->get('html-meta'));
	}

	/**
	 * Css is enabled by default. There no link tags assigned by 
	 * default then the assignement does not happen. The style tag is set 
	 * by default so it is assigned, however, there is no content so the
	 * template file will not render the style tag. 
	 *
	 * @return	null
	 */
	public function testDefaultCss()
	{
		$this->assertFalse($this->htmlDoc->isAssigned('is-css'));
		$this->assertFalse($this->htmlDoc->isAssigned('link-css'));
		$this->assertFalse($this->htmlDoc->isAssigned('inline-css'));

		$isCss  = $this->htmlDoc->isCssEnabled();
		$tags   = $this->htmlDoc->getLinkTags();
		$inline = $this->htmlDoc->getCssStyleTag();

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->buildCss()
		);
		$this->assertTrue($this->htmlDoc->isAssigned('is-css'));
		$this->assertEquals($isCss, $this->htmlDoc->get('is-css'));

		$this->assertFalse($this->htmlDoc->isAssigned('link-css'));
		$this->assertEquals(array(), $tags);
		$this->assertNull($this->htmlDoc->get('link-css'));

		$this->assertTrue($this->htmlDoc->isAssigned('inline-css'));
		$this->assertSame($inline, $this->htmlDoc->get('inline-css'));
	}
}
