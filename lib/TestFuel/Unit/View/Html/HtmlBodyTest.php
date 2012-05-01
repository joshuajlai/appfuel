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
	Appfuel\View\Html\HtmlBody,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Tag\HtmlTagFactory;

/**
 */
class HtmlBodyTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlBody
	 */
	protected $body = null;

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
		$this->body = new HtmlBody($this->factory);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->body = null;
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlBodyInterface',
			$this->body
		);
		
		$body = $this->body->getBodyTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\BodyTag',
			$body
		);
		$this->assertTrue($body->isEmpty());
		$this->assertTrue($this->body->isJs());

		$script = $this->body->getInlineScriptTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\ScriptTag',
			$script
		);
		$this->assertTrue($script->isEmpty());
		
		$this->assertEquals(array(), $this->body->getScripts());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetBodyTag()
	{
		$body = $this->getMock($this->tagInterface);
		$body->expects($this->any())
			 ->method('getTagName')
			 ->will($this->returnValue('body'));

		$this->assertSame($this->body, $this->body->setBodyTag($body));
		$this->assertSame($body, $this->body->getBodyTag());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testIsEnableDisableJs()
	{
		$this->assertSame($this->body, $this->body->disableJs());		
		$this->assertFalse($this->body->isJs());

		$this->assertSame($this->body, $this->body->enableJs());
		$this->assertTrue($this->body->isJs());
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

		$this->assertSame($this->body, $this->body->setInlineScriptTag($tag));
		$this->assertSame($tag, $this->body->getInlineScriptTag());
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


		$this->body->setInlineScriptTag($tag);
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

		$this->body->setInlineScriptTag($tag);
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testAddGetInlineScriptContent()
	{
		$script = $this->body->getInlineScriptTag();
		$block1 = 'alert("i am block 1");';
		$block2 = 'var myvar="blah";';
		$block3 = 'var myfun=function(){alert("my funct");};';

		$this->assertEquals(0, $script->getContentCount());
		$this->assertEquals(array(), $this->body->getInlineScriptContent());
		$this->assertFalse($this->body->getInlineScriptContent(0));
		$this->assertFalse($this->body->getInlineScriptContent(1));
		$this->assertFalse($this->body->getInlineScriptContent(2));

		$this->assertSame(
			$this->body, 
			$this->body->addInlineScriptContent($block1)
		);
		$expected = array($block1);
		$this->assertEquals(1, $script->getContentCount());
		$this->assertEquals($block1, $this->body->getInlineScriptContent(0));	
		$this->assertEquals($expected, $this->body->getInlineScriptContent());

		$this->assertSame(
			$this->body, 
			$this->body->addInlineScriptContent($block2)
		);
		$expected = array($block1, $block2);
		$this->assertEquals(2, $script->getContentCount());
		$this->assertEquals($block1, $this->body->getInlineScriptContent(0));	
		$this->assertEquals($block2, $this->body->getInlineScriptContent(1));	
		$this->assertEquals($expected, $this->body->getInlineScriptContent());

		$this->assertSame(
			$this->body, 
			$this->body->addInlineScriptContent($block3)
		);
		$expected = array($block1, $block2, $block3);
		$this->assertEquals(3, $script->getContentCount());
		$this->assertEquals($block1, $this->body->getInlineScriptContent(0));	
		$this->assertEquals($block2, $this->body->getInlineScriptContent(1));	
		$this->assertEquals($block3, $this->body->getInlineScriptContent(2));	
		$this->assertEquals($expected, $this->body->getInlineScriptContent());
	}

	/**
	 * @depends	testAddGetInlineScriptContent
	 * @return	null
	 */
	public function testGetInlineScriptContentString()
	{
		$script = $this->body->getInlineScriptTag();
		$sep    = $script->getContentSeparator();

		$block1 = 'alert("i am block 1");';
		$block2 = 'var myvar="blah";';
		$block3 = 'var myfun=function(){alert("my funct");};';
		$this->assertEquals('', $this->body->getInlineScriptContentString());

		
		$this->body->addInlineScriptContent($block1);
		$this->assertEquals(
			$block1,
			$this->body->getInlineScriptContentString()
		);
		
		$this->body->addInlineScriptContent($block2);
		$this->assertEquals(
			"$block1{$sep}$block2",
			$this->body->getInlineScriptContentString()
		);

		$this->body->addInlineScriptContent($block3);
		$this->assertEquals(
			"$block1{$sep}$block2{$sep}$block3",
			$this->body->getInlineScriptContentString()
		);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddGetMarkup()
	{
		$sep    = PHP_EOL;
		$block1 = '<h1>this is a title</h1>';
		$block2 = '<p>some text</p>';
		$block3 = '<div>some stuff<a href="blah.com">more blah</a></div>';

		$this->assertEquals(array(), $this->body->getMarkup());
		$this->assertFalse($this->body->getMarkup(0));
		$this->assertFalse($this->body->getMarkup(1));
		$this->assertFalse($this->body->getMarkup(2));

		$this->assertSame(
			$this->body,
			$this->body->addMarkup($block1)
		);

		$expected = array($block1);
		$this->assertEquals($expected, $this->body->getMarkup());
		$this->assertEquals($block1, $this->body->getMarkup(0));
		$this->assertEquals($block1, $this->body->getMarkupString());

		$this->assertSame(
			$this->body,
			$this->body->addMarkup($block2)
		);
		
		$expected = array($block1, $block2);
		$expectedString = $block1 . PHP_EOL . $block2;
		$this->assertEquals($expected, $this->body->getMarkup());
		$this->assertEquals($block1, $this->body->getMarkup(0));
		$this->assertEquals($block2, $this->body->getMarkup(1));
		$this->assertEquals($expectedString, $this->body->getMarkupString());

		$this->assertSame(
			$this->body,
			$this->body->addMarkup($block3)
		);
		
		$expected = array($block1, $block2, $block3);
		$expectedString = $block1 . PHP_EOL . $block2 . PHP_EOL . $block3;
		$this->assertEquals($expected, $this->body->getMarkup());
		$this->assertEquals($block1, $this->body->getMarkup(0));
		$this->assertEquals($block2, $this->body->getMarkup(1));
		$this->assertEquals($block3, $this->body->getMarkup(2));
		$this->assertEquals($expectedString, $this->body->getMarkupString());
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
		
		$this->assertEquals(0, $this->body->getScriptCount());

		$this->assertSame($this->body, $this->body->addScript($script1));
		$this->assertEquals(1, $this->body->getScriptCount());

		$expected = array($tag1);
		$this->assertEquals($expected, $this->body->getScripts());	
		
		$this->assertSame($this->body, $this->body->addScript($script2));
		$this->assertEquals(2, $this->body->getScriptCount());

		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $this->body->getScripts());	

		$this->assertSame($this->body, $this->body->addScript($script3));
		$this->assertEquals(3, $this->body->getScriptCount());

		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $this->body->getScripts());	
	}

	/**
	 * @depend	testInitialState
	 * @return	null
	 */
	public function testBuildEmpty()
	{
		$bodyTag = $this->body->getBodyTag();
		$result = $this->body->build();
		$this->assertEquals($bodyTag->build(), $result);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testBuildEmptyWithAttributes()
	{
		$bodyTag = $this->body->getBodyTag();
		$this->body->addAttribute('class', 'my-class');
		$result = $this->body->build();
	
		$this->assertEquals($bodyTag->build(), $result);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testBuildMarkupOnly()
	{
		$bodyTag = $this->body->getBodyTag();
		$block1 = '<h1>this is a title</h1>';
		$block2 = '<div>this is some text <a href="blah.com">text</a></div>';
		
		$this->body->addAttribute('class', 'my-class')
				   ->addMarkup($block1)
				   ->addMarkup($block2);

		$result = $this->body->build();
		$this->assertEquals($bodyTag->build(), $result);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testBuildWithAll()
	{
		$bodyTag = $this->body->getBodyTag();
		$block1 = '<h1>this is a title</h1>';
		$block2 = '<div>this is some text <a href="blah.com">text</a></div>';
		$block3 = 'alert("my js block");';
		$this->body->addAttribute('class', 'my-class')
				   ->addMarkup($block1)
				   ->addMarkup($block2)
				   ->addScript('myfile.js')
				   ->addScript('myotherfile.js')
				   ->addInlineScriptContent($block3)
				   ->addScript('mylastfile.js');

		$result = $this->body->build();
		$this->assertEquals($bodyTag->build(), $result);
	}



}
