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
namespace TestFuel\Unit\View\Html\Element;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Tag\TagContent,
	Appfuel\View\Html\Tag\TagAttributes,
	Appfuel\View\Html\Tag\HtmlTagFactory;

/**
 * Test the ability of the factory to create html tag objects
 */
class HtmlTagFactoryTest extends BaseTestCase
{
    /**
     * System under test
     * @var HtmlTag
     */
    protected $factory = null;

	/**
	 * Used to create mock objects and check object types
	 * @var string
	 */
	protected $tagInterface = 'Appfuel\View\Html\Tag\GenericTagInterface';
	
	/**
     * @return null
     */
    public function setUp()
    {   
        $this->factory = new HtmlTagFactory();
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
		$this->factory = null;
    }

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagFactoryInterface', 
			$this->factory
		);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateGenericTagNameOnly()
	{
		$name = 'title';
		$tag  = $this->factory->createGenericTag($name);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals($name, $tag->getTagName());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateGenericWithContent()
	{
		$name = 'title';
		$content = new TagContent();
		$tag  = $this->factory->createGenericTag($name, $content);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals($name, $tag->getTagName());
		$this->assertEquals(0, $content->count());

		$block = 'content block1';
		$tag->addContent($block);	
		$this->assertEquals(1, $content->count());
		$this->assertEquals($block, $content->get(0));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateGenericWithAttributes()
	{
		$name = 'title';
		$content = null;
		$attrs   = new TagAttributes(array('my-attr'));
		$tag  = $this->factory->createGenericTag($name, $content, $attrs);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals($name, $tag->getTagName());
		$this->assertEquals(0, $attrs->count());

		$attr = 'my-attr';
		$attrValue = 'my-attr-value';
		$tag->addAttribute($attr, $attrValue);	
		$this->assertEquals(1, $attrs->count());
		$this->assertEquals($attrValue, $attrs->get($attr));
	}
	/**
	 * The last parameter is true by default. This test will prove the tag name
	 * is locked
	 *
	 * @expectedException	LogicException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testCreateGenericTagIsTagNameLockedTrue()
	{
		$name = 'title';
		$tag  = $this->factory->createGenericTag($name);
		$tag->setTagName('link');
	}

	/**
	 * The last parameter is true by default. This test will use the last
	 * parameter as false which allows you to change the tag name
	 *
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateGenericTagIsTagNameLockedFalse()
	{
		$name = 'title';
		$tag  = $this->factory->createGenericTag($name, null, null, false);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals($name, $tag->getTagName());

		$newName = 'link';
		$this->assertSame($tag, $tag->setTagName($newName));
		$this->assertEquals($newName, $tag->getTagName());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHeadTagDefaultSeparator()
	{
		$tag = $this->factory->createHeadTag();
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HeadTagInterface',
			$tag
		);
		$this->assertEquals('head', $tag->getTagName());
		$this->assertEquals(PHP_EOL, $tag->getContentSeparator());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHeadTagWithContentSeparator()
	{
		$sep = ' ';
		$tag = $this->factory->createHeadTag($sep);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HeadTagInterface',
			$tag
		);
		$this->assertEquals('head', $tag->getTagName());
		$this->assertEquals($sep, $tag->getContentSeparator());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHeadTagWithContentSepIsNull()
	{
		$sep = null;
		$tag = $this->factory->createHeadTag($sep);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HeadTagInterface',
			$tag
		);
		$this->assertEquals('head', $tag->getTagName());
		$this->assertEquals(PHP_EOL, $tag->getContentSeparator());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateBodyTagDefaults()
	{
		$tag = $this->factory->createBodyTag();
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals('body', $tag->getTagName());
		$this->assertTrue($tag->isEmpty());
		$this->assertEquals(PHP_EOL, $tag->getContentSeparator());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateBodyTagWithContent()
	{
		$content = '<h1>this is a title</h1>';
		$tag = $this->factory->createBodyTag($content);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals('body', $tag->getTagName());
		$this->assertFalse($tag->isEmpty());
		$this->assertEquals($content, $tag->getContent(0));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateBodyTagWithContentObject()
	{
		$content = $this->factory->createGenericTag('title');
		$content->addContent('this is a title');
		$expectedContent = $content->build();

		$tag = $this->factory->createBodyTag($content);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals('body', $tag->getTagName());
		$this->assertFalse($tag->isEmpty());
		$this->assertEquals($expectedContent, $tag->getContent(0));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateBodyTagWithContentArrayOfStrings()
	{
		$list  = array(
			'<h1>this is a title</h1>',
			'<h2>this is another title</h2>'
		);

		$tag = $this->factory->createBodyTag($list);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals('body', $tag->getTagName());
		$this->assertFalse($tag->isEmpty());
		$this->assertEquals($list[0], $tag->getContent(0));
		$this->assertEquals($list[1], $tag->getContent(1));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateBodyTagWithContentArrayOfStringsAndObjects()
	{
		$content1 = '<h1>this is a title</h1>';
		$content2 = $this->factory->createGenericTag('title');
		$content2->addContent('this is another title');
		$expectedContent2 = $content2->build();


		$list  = array($content1, $content2);

		$tag = $this->factory->createBodyTag($list);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertEquals('body', $tag->getTagName());
		$this->assertFalse($tag->isEmpty());
		$this->assertEquals($list[0], $tag->getContent(0));
		$this->assertEquals($expectedContent2, $tag->getContent(1));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHtmlDefaults()
	{
		$tag = $this->factory->createHtmlTag();
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagInterface', 
			$tag
		);
	
		$head = $tag->getHead();
		$this->assertInstanceOf('Appfuel\View\Html\Tag\HeadTag', $head);
		$this->assertTrue($head->isEmpty());

		$body = $tag->getBody();
		$this->assertInstanceOf('Appfuel\View\Html\Tag\BodyTag', $body);
		$this->assertEquals('body', $body->getTagName());
		$this->assertTrue($body->isEmpty());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHtmlTagWithHtmlHead()
	{
		$head = $this->factory->createHeadTag();
		$tag  = $this->factory->createHtmlTag($head);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagInterface', 
			$tag
		);
	
		$this->assertSame($head, $tag->getHead());

		$body = $tag->getBody();
		$this->assertInstanceOf('Appfuel\View\Html\Tag\BodyTag', $body);
		$this->assertEquals('body', $body->getTagName());
		$this->assertTrue($body->isEmpty());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHtmlTagWithBodyTag()
	{
		$body = $this->factory->createBodyTag();
		$tag  = $this->factory->createHtmlTag(null, $body);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagInterface', 
			$tag
		);
	
		$this->assertSame($body, $tag->getBody());

		$head = $tag->getHead();
		$this->assertInstanceOf('Appfuel\View\Html\Tag\HeadTag', $head);
		$this->assertTrue($head->isEmpty());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHtmlTagWithBodyAndHeadTag()
	{
		$head = $this->factory->createHeadTag();
		$body = $this->factory->createBodyTag();
		$tag  = $this->factory->createHtmlTag($head, $body);
		$this->assertInstanceOf($this->tagInterface, $tag);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagInterface', 
			$tag
		);
	
		$this->assertSame($head, $tag->getHead());
		$this->assertSame($body, $tag->getBody());
	}
}
