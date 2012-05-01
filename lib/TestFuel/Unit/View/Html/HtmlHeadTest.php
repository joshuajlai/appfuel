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
	Appfuel\View\Html\HtmlHead,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Tag\HtmlTagFactory;

/**
 */
class HtmlHeadTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlBody
	 */
	protected $head = null;

	/**
	 * Used to create all html tags
	 * @var HtmlTagFactory
	 */
	protected $factory = null;

	/**
	 * @var string
	 */
	protected $tagInterface = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->tagInterface = 'Appfuel\View\Html\Tag\GenericTagInterface';
		$this->factory = new HtmlTagFactory();
		$this->head = new HtmlHead($this->factory);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->head = null;
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlHeadInterface',
			$this->head
		);
		
		$head = $this->head->getHeadTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HeadTag',
			$head
		);
		$this->assertTrue($head->isEmpty());
		$this->assertTrue($this->head->isJs());
		$this->assertTrue($this->head->isCss());

		$script = $this->head->getInlineScriptTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\ScriptTag',
			$script
		);
		$this->assertTrue($script->isEmpty());
		
		$this->assertEquals(array(), $this->head->getScripts());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetHeadTag()
	{
		$head = $this->getMock('Appfuel\View\Html\Tag\HeadTagInterface');
		$head->expects($this->any())
			 ->method('getTagName')
			 ->will($this->returnValue('head'));

		$this->assertSame($this->head, $this->head->setHeadTag($head));
		$this->assertSame($head, $this->head->getHeadTag());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetTitle()
	{
		$headTag = $this->head->getHeadTag();
		$titleTag = $headTag->getTitle();
	
		$this->assertTrue($titleTag->isEmpty());
		$block1 = 'title 1';
		$block2 = 'title 2';
		$block3 = 'title 3';
		$block4 = 'title 4';
		$this->assertSame($this->head, $this->head->setTitle($block1));
		$this->assertEquals($block1, $titleTag->getContent(0));
		
		$this->assertSame($this->head, $this->head->setTitle($block2));
		$this->assertEquals($block2, $titleTag->getContent(1));

		$this->assertSame(
			$this->head, 
			$this->head->setTitle($block3, 'prepend')
		);
		$this->assertEquals($block3, $titleTag->getContent(0));

		$this->assertSame(
			$this->head, 
			$this->head->setTitle($block4, 'replace')
		);
		$this->assertEquals($block4, $titleTag->getContent(0));
		$this->assertEquals(array($block4), $titleTag->getContent());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetTitleSeparator()
	{
		$this->assertEquals(' ', $this->head->getTitleSeparator());
		$this->assertSame($this->head, $this->head->setTitleSeparator(':'));
		$this->assertEquals(':', $this->head->getTitleSeparator());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddMetaGetMetaTags()
	{
		$this->assertEquals(array(), $this->head->getMetaTags());
		$tag1 = $this->factory->createMetaTag('author', 'robert');
		$this->assertSame(
			$this->head, 
			$this->head->addMeta('author', 'robert')
		);
		$expected = array($tag1);
		$this->assertEquals($expected, $this->head->getMetaTags());

		$tag2 = $this->factory->createMetaTag(null, null, null, 'UTF-8');
		$this->assertSame(
			$this->head, 
			$this->head->addMeta(null, null, null, 'UTF-8')
		);
		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $this->head->getMetaTags());

		$tag3 = $this->factory->createMetaTag(
			null, 
			'text/html', 
			'Content-Type', 
			'UTF-8'
		);
		$this->assertSame(
			$this->head, 
			$this->head->addMeta(null,'text/html','Content-Type', 'UTF-8')
		);
		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $this->head->getMetaTags());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddMetaTag()
	{
		$tag1 = $this->factory->createMetaTag('author', 'robert');

		$this->assertEquals(array(), $this->head->getMetaTags());
		$this->assertSame($this->head, $this->head->addMetaTag($tag1));

		$expected = array($tag1);
		$this->assertEquals($expected, $this->head->getMetaTags());
		
		$tag2 = $this->factory->createMetaTag(null, null, null, 'UTF-8');
		$this->assertSame($this->head, $this->head->addMetaTag($tag2));
		
		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $this->head->getMetaTags());

		$tag3 = $this->factory->createMetaTag(
			null, 
			'text/html', 
			'Content-Type', 
			'UTF-8'
		);
		$this->assertSame($this->head, $this->head->addMetaTag($tag3));
		
		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $this->head->getMetaTags());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetBaseTag()
	{
		$this->assertNull($this->head->getBaseTag());

		$tag = $this->factory->createBaseTag('my-href');
		$this->assertSame($this->head, $this->head->setBaseTag($tag));
		$this->assertSame($tag, $this->head->getBaseTag());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetBase()
	{
		$this->assertNull($this->head->getBaseTag());
		$this->assertSame($this->head, $this->head->setBase('my-ref'));

		$tag = $this->factory->createBaseTag('my-ref');
		$this->assertEquals($tag, $this->head->getBaseTag());

		$this->assertSame($this->head, $this->head->setBase(null, 'my-target'));
	
		$tag = $this->factory->createBaseTag(null, 'my-target');
		$this->assertEquals($tag, $this->head->getBaseTag());

		$this->assertSame($this->head, $this->head->setBase('ref', 'target'));

		$tag = $this->factory->createBaseTag('ref', 'target');
		$this->assertEquals($tag, $this->head->getBaseTag());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testIsEnableDisableJs()
	{
		$this->assertSame($this->head, $this->head->disableJs());		
		$this->assertFalse($this->head->isJs());

		$this->assertSame($this->head, $this->head->enableJs());
		$this->assertTrue($this->head->isJs());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testIsEnableDisableCss()
	{
		$this->assertSame($this->head, $this->head->disableCss());		
		$this->assertFalse($this->head->isCss());

		$this->assertSame($this->head, $this->head->enableCss());
		$this->assertTrue($this->head->isCss());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetInlineStyleTag()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('style'));

		$this->assertSame($this->head, $this->head->setInlineStyleTag($tag));
		$this->assertSame($tag, $this->head->getInlineStyleTag());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetInlineStyleTagNotStyle_Failure()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('title'));


		$this->head->setInlineStyleTag($tag);
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testAddGetInlineStyleContent()
	{
		$style = $this->head->getInlineStyleTag();
		$block1 = 'p{color:red}';
		$block2 = 'h1{color:blue};';

		$this->assertEquals(0, $style->getContentCount());
		$this->assertEquals(array(), $this->head->getInlineStyleContent());
		$this->assertFalse($this->head->getInlineStyleContent(0));
		$this->assertFalse($this->head->getInlineStyleContent(1));

		$this->assertSame(
			$this->head, 
			$this->head->addInlineStyleContent($block1)
		);
		$expected = array($block1);
		$this->assertEquals(1, $style->getContentCount());
		$this->assertEquals($block1, $this->head->getInlineStyleContent(0));	
		$this->assertEquals($expected, $this->head->getInlineStyleContent());

		$this->assertSame(
			$this->head, 
			$this->head->addInlineStyleContent($block2)
		);
		$expected = array($block1, $block2);
		$this->assertEquals(2, $style->getContentCount());
		$this->assertEquals($block1, $this->head->getInlineStyleContent(0));	
		$this->assertEquals($block2, $this->head->getInlineStyleContent(1));	
		$this->assertEquals($expected, $this->head->getInlineStyleContent());
	}

	/**
	 * @depends	testAddGetInlineStyleContent
	 * @return	null
	 */
	public function testGetInlineStyleContentString()
	{
		$style = $this->head->getInlineStyleTag();
		$sep    = $style->getContentSeparator();

		$block1 = 'p{color:red}';
		$block2 = 'h1{color:blue}';
		$this->assertEquals('', $this->head->getInlineStyleContentString());

		
		$this->head->addInlineStyleContent($block1);
		$this->assertEquals(
			$block1,
			$this->head->getInlineStyleContentString()
		);
		
		$this->head->addInlineStyleContent($block2);
		$this->assertEquals(
			"$block1{$sep}$block2",
			$this->head->getInlineStyleContentString()
		);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddGetCssTagAsStrings()
	{
		$file1 = 'myfile.css';
		$file2 = 'myOtherfile.css';
		$file3 = 'yourfile.css';

		$tag1 = $this->factory->createLinkTag($file1);
		$tag2 = $this->factory->createLinkTag($file2);
		$tag3 = $this->factory->createLinkTag($file3);
		
		$this->assertEquals(0, $this->head->getCssTagCount());

		$this->assertSame($this->head, $this->head->addCssTag($file1));
		$this->assertEquals(1, $this->head->getCssTagCount());

		$expected = array($tag1);
		$this->assertEquals($expected, $this->head->getCssTags());	
		
		$this->assertSame($this->head, $this->head->addCssTag($file2));
		$this->assertEquals(2, $this->head->getCssTagCount());

		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $this->head->getCssTags());	

		$this->assertSame($this->head, $this->head->addCssTag($file3));
		$this->assertEquals(3, $this->head->getCssTagCount());

		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $this->head->getCssTags());	
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetInlineScriptTag()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('script'));

		$tag->expects($this->once())
			   ->method('isAttribute')
			   ->will($this->returnValue(false));

		$this->assertSame($this->head, $this->head->setInlineScriptTag($tag));
		$this->assertSame($tag, $this->head->getInlineScriptTag());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetInlineScriptTagNotScript_Failure()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('link'));


		$this->head->setInlineScriptTag($tag);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetInlineScriptTagScriptWithSrc_Failure()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('script'));

		$tag->expects($this->once())
			   ->method('isAttribute')
			   ->will($this->returnValue(true));

		$this->head->setInlineScriptTag($tag);
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testAddGetInlineScriptContent()
	{
		$script = $this->head->getInlineScriptTag();
		$block1 = 'alert("i am block 1");';
		$block2 = 'var myvar="blah";';
		$block3 = 'var myfun=function(){alert("my funct");};';

		$this->assertEquals(0, $script->getContentCount());
		$this->assertEquals(array(), $this->head->getInlineScriptContent());
		$this->assertFalse($this->head->getInlineScriptContent(0));
		$this->assertFalse($this->head->getInlineScriptContent(1));
		$this->assertFalse($this->head->getInlineScriptContent(2));

		$this->assertSame(
			$this->head, 
			$this->head->addInlineScriptContent($block1)
		);
		$expected = array($block1);
		$this->assertEquals(1, $script->getContentCount());
		$this->assertEquals($block1, $this->head->getInlineScriptContent(0));	
		$this->assertEquals($expected, $this->head->getInlineScriptContent());

		$this->assertSame(
			$this->head, 
			$this->head->addInlineScriptContent($block2)
		);
		$expected = array($block1, $block2);
		$this->assertEquals(2, $script->getContentCount());
		$this->assertEquals($block1, $this->head->getInlineScriptContent(0));	
		$this->assertEquals($block2, $this->head->getInlineScriptContent(1));	
		$this->assertEquals($expected, $this->head->getInlineScriptContent());

		$this->assertSame(
			$this->head, 
			$this->head->addInlineScriptContent($block3)
		);
		$expected = array($block1, $block2, $block3);
		$this->assertEquals(3, $script->getContentCount());
		$this->assertEquals($block1, $this->head->getInlineScriptContent(0));	
		$this->assertEquals($block2, $this->head->getInlineScriptContent(1));	
		$this->assertEquals($block3, $this->head->getInlineScriptContent(2));	
		$this->assertEquals($expected, $this->head->getInlineScriptContent());
	}

	/**
	 * @depends	testAddGetInlineScriptContent
	 * @return	null
	 */
	public function testGetInlineScriptContentString()
	{
		$script = $this->head->getInlineScriptTag();
		$sep    = $script->getContentSeparator();

		$block1 = 'alert("i am block 1");';
		$block2 = 'var myvar="blah";';
		$block3 = 'var myfun=function(){alert("my funct");};';
		$this->assertEquals('', $this->head->getInlineScriptContentString());

		
		$this->head->addInlineScriptContent($block1);
		$this->assertEquals(
			$block1,
			$this->head->getInlineScriptContentString()
		);
		
		$this->head->addInlineScriptContent($block2);
		$this->assertEquals(
			"$block1{$sep}$block2",
			$this->head->getInlineScriptContentString()
		);

		$this->head->addInlineScriptContent($block3);
		$this->assertEquals(
			"$block1{$sep}$block2{$sep}$block3",
			$this->head->getInlineScriptContentString()
		);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddScriptGetScriptsAsStrings()
	{
		$script1 = 'myfile.js';
		$script2 = 'myOtherfile.js';
		$script3 = 'yourfile.js';

		$tag1 = $this->factory->createScriptTag($script1);
		$tag2 = $this->factory->createScriptTag($script2);
		$tag3 = $this->factory->createScriptTag($script3);
		
		$this->assertEquals(0, $this->head->getScriptCount());

		$this->assertSame($this->head, $this->head->addScript($script1));
		$this->assertEquals(1, $this->head->getScriptCount());

		$expected = array($tag1);
		$this->assertEquals($expected, $this->head->getScripts());	
		
		$this->assertSame($this->head, $this->head->addScript($script2));
		$this->assertEquals(2, $this->head->getScriptCount());

		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $this->head->getScripts());	

		$this->assertSame($this->head, $this->head->addScript($script3));
		$this->assertEquals(3, $this->head->getScriptCount());

		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $this->head->getScripts());	
	}
}
