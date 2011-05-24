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
	Appfuel\App\View\Html\Response,
	Appfuel\View\Html\Element\Meta\Tag as MetaTag,
	StdClass;

/**
 * The html responseument is a template file that controller the html head, css 
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
	protected $response = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		
		$this->response = new Response();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->response);
	}

	/**
	 * @return null
	 */	
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'Appfuel\App\View\FileTemplate',
			$this->response,
			'The html response is also a template'
		);

		$this->assertInstanceOf(
			'Appfuel\App\View\Data',
			$this->response,
			'The html response must extend the view data class'
		);

		$this->assertInstanceOf(
			'Appfuel\Data\Dictionary',
			$this->response,
			'The json response is also a dictionary'
		);
	}

	/**
	 * @return null
	 */
	public function testGetSetTitle()
	{
		/* initial value is null */
		$this->assertNull($this->response->getTitle());
		
		$title = $this->getMockBuilder('\Appfuel\View\Html\Element\Title')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->response,
			$this->response->setTitle($title),
			'uses fluent interface'
		);

		$this->assertSame($title, $this->response->getTitle());
	}

	/**
	 * @return null
	 */
	public function testGetSetBase()
	{
		/* initial value is null */
		$this->assertNull($this->response->getBase());
		
		$base = $this->getMockBuilder('\Appfuel\View\Html\Element\Base')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->response,
			$this->response->setBase($base),
			'uses fluent interface'
		);

		$this->assertSame($base, $this->response->getBase());
	}

	/**
	 * @return null
	 */
	public function testGetSetCharset()
	{
		/* initial value is null */
		$this->assertNull($this->response->getCharset());
		
		$class = '\Appfuel\View\Html\Element\Meta\Charset';	
		$tag   = $this->getMockBuilder($class)
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->response,
			$this->response->setCharset($tag),
			'uses fluent interface'
		);

		$this->assertSame($tag, $this->response->getCharset());
	}

	/**
	 * @return null
	 */
	public function testAddGetMeta()
	{
		/* initial value is null */
		$result = $this->response->getMeta();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
		
		$tag = new MetaTag();
		
		$this->assertSame(
			$this->response,
			$this->response->addMeta($tag),
			'uses a fluent interface'
		);

		$expected = array($tag);
		$result = $this->response->getMeta();
		$this->assertEquals($expected, $result);

		$tag2 = new MetaTag();
		$this->response->addMeta($tag2);

		$expected = array($tag, $tag2);
		$result = $this->response->getMeta();
		$this->assertEquals($expected, $result);

		/* prove charsets are not added */
		$tag3 = new MetaTag();
		$tag3->addAttribute('charset', 'UTF-8');
	
		$this->assertSame(
			$this->response,
			$this->response->addMeta($tag3),
			'uses a fluent interface'
		);

		/* 
		 * note that expected is still the same array with just two tags
		 * and not 3
		 */
		$result = $this->response->getMeta();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return null
	 */
	public function testAddGetCss()
	{
		/* initial value is null */
		$result = $this->response->getCssLinks();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
			
		$class = '\Appfuel\View\Html\Element\Link';
		$tag   = $this->getMockBuilder($class)
					  ->disableOriginalConstructor()
					  ->getMock();
		
		$this->assertSame(
			$this->response,
			$this->response->addCssLink($tag),
			'uses a fluent interface'
		);

		$expected = array($tag);
		$result = $this->response->getCssLinks();
		$this->assertEquals($expected, $result);

		$tag2 = $this->getMockBuilder($class)
					 ->disableOriginalConstructor()
					 ->getMock();

		$this->response->addCssLink($tag2);

		$expected = array($tag, $tag2);
		$result = $this->response->getCssLinks();
		$this->assertEquals($expected, $result);
	}

	/**
	 * Used to determine if styles are enabled or disabled for the html response.
	 *
	 * @return null
	 */
	public function testIsEnableDisableCss()
	{
		/* default setting is true */
		$this->assertTrue($this->response->isCss());

		$this->assertSame(
			$this->response,
			$this->response->disableCss(),
			'uses fluent interface'
		);
		$this->assertFalse($this->response->isCss());
	
		$this->assertSame(
			$this->response,
			$this->response->enableCss(),
			'uses fluent interface'
		);

		$this->assertTrue($this->response->isCss());
	}

	/**
	 * @return null
	 */
	public function testGetSetInlineStyle()
	{
		/* initial value is null */
		$this->assertNull($this->response->getInlineCss());
		
		$tag = $this->getMockBuilder('\Appfuel\View\Html\Element\Style')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->response,
			$this->response->setInlineCss($tag),
			'uses fluent interface'
		);

		$this->assertSame($tag, $this->response->getInlineCss());
	}

	/**
	 * Used to determine if inline styles are enabled or disabled for the 
	 * html response.
	 *
	 * @return null
	 */
	public function testIsEnableDisableInlineCss()
	{
		/* default setting is false */
		$this->assertFalse($this->response->isInlineCss());

		$this->assertSame(
			$this->response,
			$this->response->enableInlineCss(),
			'uses fluent interface'
		);
		$this->assertTrue($this->response->isInlineCss());
	
		$this->assertSame(
			$this->response,
			$this->response->disableInlineCss(),
			'uses fluent interface'
		);

		$this->assertFalse($this->response->isInlineCss());
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
		$this->assertEquals(array(), $this->response->getJsScripts('head'));
		$this->assertEquals(array(), $this->response->getJsScripts('body'));

		$tag = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->response,
			$this->response->addJsScript($tag, 'head'),
			'uses fluent interface'
		);

		$expectedHead = array($tag);
		$this->assertEquals($expectedHead, $this->response->getJsScripts('head'));
		$this->assertEquals(array(), $this->response->getJsScripts('body'));

		$tag2 = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					 ->disableOriginalConstructor()
					 ->getMock();

		$this->response->addJsScript($tag2, 'head');
		$expectedHead = array($tag, $tag2);
		$this->assertEquals($expectedHead, $this->response->getJsScripts('head'));
		$this->assertEquals(array(), $this->response->getJsScripts('body'));

		$tag3 = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					 ->disableOriginalConstructor()
					 ->getMock();


		$this->response->addJsScript($tag3, 'body');
		$expectedBody = array($tag3);
		$this->assertEquals($expectedHead, $this->response->getJsScripts('head'));
		$this->assertEquals($expectedBody, $this->response->getJsScripts('body'));

		$tag4 = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					 ->disableOriginalConstructor()
					 ->getMock();

		$this->response->addJsScript($tag4, 'body');
		$expectedBody = array($tag3, $tag4);
		$this->assertEquals($expectedHead, $this->response->getJsScripts('head'));
		$this->assertEquals($expectedBody, $this->response->getJsScripts('body'));
		
		/* try to get from a location nknown not to exist */
		$this->assertEquals(array(), $this->response->getJsScripts('no-loc'));
		$this->assertSame(
			$this->response,
			$this->response->addJsScript($tag, 'no-loc'),
			'uses fluent interface'
		);
		$this->assertEquals(array(), $this->response->getJsScripts('no-loc'));
	}

	/**
	 * @return null
	 */
	public function testIsEnableDisableJs()
	{
		/* default setting is true */
		$this->assertTrue($this->response->isJs());

		$this->assertSame(
			$this->response,
			$this->response->disableJs(),
			'uses fluent interface'
		);
		$this->assertFalse($this->response->isJs());
	
		$this->assertSame(
			$this->response,
			$this->response->enableJs(),
			'uses fluent interface'
		);

		$this->assertTrue($this->response->isJs());
	}

	/**
	 * @return null
	 */
	public function testGetSetInlineJs()
	{
		/* default values are null */
		$this->assertNull($this->response->getInlineJs('head'));
		$this->assertNull($this->response->getInlineJs('body'));

		$tag = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					  ->disableOriginalConstructor()
					  ->getMock();

		$this->assertSame(
			$this->response,
			$this->response->setInlineJs($tag, 'head'),
			'uses fluent interface'
		);

		$this->assertSame($tag, $this->response->getInlineJs('head'));
		$this->assertNull($this->response->getInlineJs('body'));

		$tag2 = $this->getMockBuilder('\Appfuel\View\Html\Element\Script')
					 ->disableOriginalConstructor()
					 ->getMock();

		$this->assertSame(
			$this->response,
			$this->response->setInlineJs($tag2, 'body'),
			'uses fluent interface'
		);

		$this->assertSame($tag, $this->response->getInlineJs('head'));
		$this->assertSame($tag2, $this->response->getInlineJs('body'));

		/* try to get from a location nknown not to exist */
		$this->assertEquals(null, $this->response->getInlineJs('no-loc'));
		$this->assertSame(
			$this->response,
			$this->response->setInlineJs($tag, 'no-loc'),
			'uses fluent interface'
		);
		$this->assertEquals(null, $this->response->getInlineJs('no-loc'));
	}

	/**
	 * @return null
	 */
	public function testIsEnableDisableInlineJs()
	{
		/* default setting is true */
		$this->assertTrue($this->response->isInlineJs());

		$this->assertSame(
			$this->response,
			$this->response->disableInlineJs(),
			'uses fluent interface'
		);
		$this->assertFalse($this->response->isInlineJs());
	
		$this->assertSame(
			$this->response,
			$this->response->enableInlineJs(),
			'uses fluent interface'
		);

		$this->assertTrue($this->response->isInlineJs());
	}
}
