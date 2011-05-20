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
	Appfuel\App\View\Html\Document,
	Appfuel\View\Html\Element\Meta\Tag as MetaTag,
	StdClass;

/**
 * The html document is a template file that controller the html head, css 
 * link tags, css style tag, javascript script files and content and body tag
 * It does not manage the content inside the body tag. That is left to the 
 * layout.
 */
class DocumentTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $doc = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		
		$this->doc = new Document();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->doc);
	}

	/**
	 * @return null
	 */	
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'Appfuel\App\View\FileTemplate',
			$this->doc,
			'The html doc is also a template'
		);

		$this->assertInstanceOf(
			'Appfuel\App\View\Data',
			$this->doc,
			'The html doc must extend the view data class'
		);

		$this->assertInstanceOf(
			'Appfuel\Data\Dictionary',
			$this->doc,
			'The json doc is also a dictionary'
		);
	}

	/**
	 * @return null
	 */
	public function testGetSetTitle()
	{
		/* initial value is null */
		$this->assertNull($this->doc->getTitle());
		
		$title = $this->getMockBuilder('\Appfuel\View\Html\Element\Title')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->doc,
			$this->doc->setTitle($title),
			'uses fluent interface'
		);

		$this->assertSame($title, $this->doc->getTitle());
	}

	/**
	 * @return null
	 */
	public function testGetSetBase()
	{
		/* initial value is null */
		$this->assertNull($this->doc->getBase());
		
		$base = $this->getMockBuilder('\Appfuel\View\Html\Element\Base')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->doc,
			$this->doc->setBase($base),
			'uses fluent interface'
		);

		$this->assertSame($base, $this->doc->getBase());
	}

	/**
	 * @return null
	 */
	public function testGetSetCharset()
	{
		/* initial value is null */
		$this->assertNull($this->doc->getCharset());
		
		$class = '\Appfuel\View\Html\Element\Meta\Charset';	
		$tag   = $this->getMockBuilder($class)
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->doc,
			$this->doc->setCharset($tag),
			'uses fluent interface'
		);

		$this->assertSame($tag, $this->doc->getCharset());
	}

	/**
	 * @return null
	 */
	public function testAddGetMeta()
	{
		/* initial value is null */
		$result = $this->doc->getMeta();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
		
		$tag = new MetaTag();
		
		$this->assertSame(
			$this->doc,
			$this->doc->addMeta($tag),
			'uses a fluent interface'
		);

		$expected = array($tag);
		$result = $this->doc->getMeta();
		$this->assertEquals($expected, $result);

		$tag2 = new MetaTag();
		$this->doc->addMeta($tag2);

		$expected = array($tag, $tag2);
		$result = $this->doc->getMeta();
		$this->assertEquals($expected, $result);

		/* prove charsets are not added */
		$tag3 = new MetaTag();
		$tag3->addAttribute('charset', 'UTF-8');
	
		$this->assertSame(
			$this->doc,
			$this->doc->addMeta($tag3),
			'uses a fluent interface'
		);

		/* 
		 * note that expected is still the same array with just two tags
		 * and not 3
		 */
		$result = $this->doc->getMeta();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return null
	 */
	public function testAddGetCss()
	{
		/* initial value is null */
		$result = $this->doc->getCssLinks();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
			
		$class = '\Appfuel\View\Html\Element\Link';
		$tag   = $this->getMockBuilder($class)
					  ->disableOriginalConstructor()
					  ->getMock();
		
		$this->assertSame(
			$this->doc,
			$this->doc->addCssLink($tag),
			'uses a fluent interface'
		);

		$expected = array($tag);
		$result = $this->doc->getCssLinks();
		$this->assertEquals($expected, $result);

		$tag2 = $this->getMockBuilder($class)
					 ->disableOriginalConstructor()
					 ->getMock();

		$this->doc->addCssLink($tag2);

		$expected = array($tag, $tag2);
		$result = $this->doc->getCssLinks();
		$this->assertEquals($expected, $result);
	}

	/**
	 * Used to determine if styles are enabled or disabled for the html doc.
	 *
	 * @return null
	 */
	public function testIsEnableDisableCss()
	{
		/* default setting is true */
		$this->assertTrue($this->doc->isCss());

		$this->assertSame(
			$this->doc,
			$this->doc->disableCss(),
			'uses fluent interface'
		);
		$this->assertFalse($this->doc->isCss());
	
		$this->assertSame(
			$this->doc,
			$this->doc->enableCss(),
			'uses fluent interface'
		);

		$this->assertTrue($this->doc->isCss());
	}

	/**
	 * @return null
	 */
	public function testGetSetInlineStyle()
	{
		/* initial value is null */
		$this->assertNull($this->doc->getInlineCss());
		
		$tag = $this->getMockBuilder('\Appfuel\View\Html\Element\Style')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->doc,
			$this->doc->setInlineCss($tag),
			'uses fluent interface'
		);

		$this->assertSame($tag, $this->doc->getInlineCss());
	}

	/**
	 * Used to determine if inline styles are enabled or disabled for the 
	 * html doc.
	 *
	 * @return null
	 */
	public function testIsEnableDisableInlineCss()
	{
		/* default setting is false */
		$this->assertFalse($this->doc->isInlineCss());

		$this->assertSame(
			$this->doc,
			$this->doc->enableInlineCss(),
			'uses fluent interface'
		);
		$this->assertTrue($this->doc->isInlineCss());
	
		$this->assertSame(
			$this->doc,
			$this->doc->disableInlineCss(),
			'uses fluent interface'
		);

		$this->assertFalse($this->doc->isInlineCss());
	}

	/**
	 * Javascript files can be stored in one of two locations: in the html head
	 * or in the body addJsScript takes two parameter, the tag and the location
	 * we will be testing that add does in fact store script in one of those 
	 * locations
	 *
	 * @return null
	 */
	public function testGetAddJsScript()
	{
		/* default values are empty arrays */
		$this->assertEquals(array(), $this->doc->getJsScripts('head'));
		$this->assertEquals(array(), $this->doc->getJsScripts('body'));

		$tag = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->doc,
			$this->doc->addJsScript($tag, 'head'),
			'uses fluent interface'
		);

		$expectedHead = array($tag);
		$this->assertEquals($expectedHead, $this->doc->getJsScripts('head'));
		$this->assertEquals(array(), $this->doc->getJsScripts('body'));

		$tag2 = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					 ->disableOriginalConstructor()
					 ->getMock();

		$this->doc->addJsScript($tag2, 'head');
		$expectedHead = array($tag, $tag2);
		$this->assertEquals($expectedHead, $this->doc->getJsScripts('head'));
		$this->assertEquals(array(), $this->doc->getJsScripts('body'));

		$tag3 = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					 ->disableOriginalConstructor()
					 ->getMock();


		$this->doc->addJsScript($tag3, 'body');
		$expectedBody = array($tag3);
		$this->assertEquals($expectedHead, $this->doc->getJsScripts('head'));
		$this->assertEquals($expectedBody, $this->doc->getJsScripts('body'));

		$tag4 = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					 ->disableOriginalConstructor()
					 ->getMock();

		$this->doc->addJsScript($tag4, 'body');
		$expectedBody = array($tag3, $tag4);
		$this->assertEquals($expectedHead, $this->doc->getJsScripts('head'));
		$this->assertEquals($expectedBody, $this->doc->getJsScripts('body'));
		
		/* try to get from a location nknown not to exist */
		$this->assertEquals(array(), $this->doc->getJsScripts('no-loc'));
		$this->assertSame(
			$this->doc,
			$this->doc->addJsScript($tag, 'no-loc'),
			'uses fluent interface'
		);
		$this->assertEquals(array(), $this->doc->getJsScripts('no-loc'));
	}

	/**
	 * @return null
	 */
	public function testIsEnableDisableJs()
	{
		/* default setting is true */
		$this->assertTrue($this->doc->isJs());

		$this->assertSame(
			$this->doc,
			$this->doc->disableJs(),
			'uses fluent interface'
		);
		$this->assertFalse($this->doc->isJs());
	
		$this->assertSame(
			$this->doc,
			$this->doc->enableJs(),
			'uses fluent interface'
		);

		$this->assertTrue($this->doc->isJs());
	}

	/**
	 * @return null
	 */
	public function testGetSetInlineJs()
	{
		/* default values are null */
		$this->assertNull($this->doc->getInlineJs('head'));
		$this->assertNull($this->doc->getInlineJs('body'));

		$tag = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->doc,
			$this->doc->setInlineJs($tag, 'head'),
			'uses fluent interface'
		);

		$this->assertSame($tag, $this->doc->getInlineJs('head'));
		$this->assertNull($this->doc->getInlineJs('body'));

		$tag2 = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					 ->disableOriginalConstructor()
					 ->getMock();

		$this->assertSame(
			$this->doc,
			$this->doc->setInlineJs($tag2, 'body'),
			'uses fluent interface'
		);

		$this->assertSame($tag, $this->doc->getInlineJs('head'));
		$this->assertSame($tag2, $this->doc->getInlineJs('body'));

		/* try to get from a location nknown not to exist */
		$this->assertEquals(null, $this->doc->getInlineJs('no-loc'));
		$this->assertSame(
			$this->doc,
			$this->doc->setInlineJs($tag, 'no-loc'),
			'uses fluent interface'
		);
		$this->assertEquals(null, $this->doc->getInlineJs('no-loc'));
	}

	/**
	 * @return null
	 */
	public function testIsEnableDisableInlineJs()
	{
		/* default setting is true */
		$this->assertTrue($this->doc->isInlineJs());

		$this->assertSame(
			$this->doc,
			$this->doc->disableInlineJs(),
			'uses fluent interface'
		);
		$this->assertFalse($this->doc->isInlineJs());
	
		$this->assertSame(
			$this->doc,
			$this->doc->enableInlineJs(),
			'uses fluent interface'
		);

		$this->assertTrue($this->doc->isInlineJs());
	}
}
