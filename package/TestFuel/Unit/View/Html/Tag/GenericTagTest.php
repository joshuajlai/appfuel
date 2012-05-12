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
namespace TestFuel\Unit\View\Html\Tag;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Tag\TagContent,
	Appfuel\View\Html\Tag\GenericTag,
	Appfuel\View\Html\Tag\TagAttributes;

/**
 * The html element tag is used to automate the rendering of the html element
 * and provide a simpler interface to add data to the element
 */
class GenericTagTest extends BaseTestCase
{
    /**
     * System under test
     * @var Message
     */
    protected $tag = null;

	/**
	 * @var string
	 */
	protected $tagName = null;

    /**
     * @return null
     */
    public function setUp()
    { 
		$this->tagName = 'title';  
        $this->tag = new GenericTag($this->tagName, null, null, false);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        $this->tag = null;
    }

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\GenericTagInterface',
			$this->tag
		);

		$this->assertEquals($this->tagName, $this->tag->getTagName());
		$this->assertEquals('', $this->tag->getContentString());
		$this->assertEquals('', $this->tag->getAttributeString());
		$this->assertTrue($this->tag->isClosingTag());
		$this->assertTrue($this->tag->isRenderWhenEmpty());
	}
	
	/**
	 * We can prove the TagContent is being used because its passed in by 
	 * reference
	 *
	 * @return	null
	 */
	public function testConstructorTagContent()
	{
		$content = new TagContent();
		$tag = new GenericTag('title', $content);

		$text1 = 'text block 1';
		$this->assertSame($tag, $tag->addContent($text1));
		$this->assertEquals($text1, $content->get(0));

		$text2 = 'text block 2';
		$this->assertSame($tag, $tag->addContent($text2));
		$this->assertEquals($text2, $content->get(1));	
	}

	/**
	 * @return	null
	 */
	public function testConstructorTagAttributes()
	{
		$attrs = new TagAttributes(array('my-attr','your-attr'));
		$tag = new GenericTag('title', null, $attrs);
		
		$attr1 = 'my-value';
		$this->assertFalse($attrs->exists('my-attr'));
		$this->assertSame($tag, $tag->addAttribute('my-attr', $attr1));
		$this->assertTrue($attrs->exists('my-attr'));
		$this->assertEquals($attr1, $attrs->get('my-attr'));

		$attr2 = 'my-other-value';
		$this->assertFalse($attrs->exists('your-attr'));
		$this->assertSame($tag, $tag->addAttribute('your-attr', $attr2));
		$this->assertTrue($attrs->exists('your-attr'));
		$this->assertEquals($attr2, $attrs->get('your-attr'));
	}

	/**
	 * @return	null
	 */
	public function testConstructorContentAndAttrs()
	{
		$content = new TagContent();
		$attrs = new TagAttributes(array('my-attr'));
		$tag = new GenericTag('title', $content, $attrs);

		$attr1 = 'my-value';
		$this->assertFalse($attrs->exists('my-attr'));
		$this->assertSame($tag, $tag->addAttribute('my-attr', $attr1));
		$this->assertTrue($attrs->exists('my-attr'));
		$this->assertEquals($attr1, $attrs->get('my-attr'));

		$text1 = 'text block 1';
		$this->assertSame($tag, $tag->addContent($text1));
		$this->assertEquals($text1, $content->get(0));
	}

	/**
	 * @return
	 */
	public function testContstructorAllParams()
	{
		$tagName = 'title';
		$content = new TagContent();
		$attrs = new TagAttributes(array('my-attr'));
		$lockTagName = false;
		$tag = new GenericTag($tagName, $content, $attrs, $lockTagName);

		$attr1 = 'my-value';
		$this->assertFalse($attrs->exists('my-attr'));
		$this->assertSame($tag, $tag->addAttribute('my-attr', $attr1));
		$this->assertTrue($attrs->exists('my-attr'));
		$this->assertEquals($attr1, $attrs->get('my-attr'));

		$text1 = 'text block 1';
		$this->assertSame($tag, $tag->addContent($text1));
		$this->assertEquals($text1, $content->get(0));

		/* should be able to change the tag name now */
		$this->assertSame($tag, $tag->setTagName('link'));
		$this->assertEquals('link', $tag->getTagName());
	}

	/**
	 * By default when create a generic tag the tag name is locked. Any 
	 * attempt to change it will result in a LogicException
	 *
	 * @expectedException	LogicException
	 * @return				null
	 */
	public function testSetTagNameWhenLocked_Failure()
	{
		$tag = new GenericTag('title');
		$tag->setTagName('link');
	}

	/**
	 * @depends			testInitialState
	 * @dataProvider	provideNonEmptyStringsNoNumbers
	 * @param			string	$name
	 * @return			null
	 */
	public function testSetTagName($name)
	{
		$this->assertSame($this->tag, $this->tag->setTagName($name));
		$this->assertEquals($name, $this->tag->getTagName());
	}
	
	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @dataProvider		provideEmptyStrings
	 * @param				string	$name
	 * @return				null
	 */
	public function testSetTagNameEmptyString_Failure($name)
	{
		$this->tag->setTagName($name);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @param				string	$name
	 * @return				null
	 */
	public function testSetTagNameInvalidString_Failure($name)
	{
		$this->tag->setTagName($name);
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testEnableDisableClosingTag()
	{
		$this->assertSame($this->tag, $this->tag->disableClosingTag());
		$this->assertFalse($this->tag->isClosingTag());

		$this->assertSame($this->tag, $this->tag->enableClosingTag());
		$this->assertTrue($this->tag->isClosingTag());
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testEnableDisableRenderWhenEmpty()
	{
		$this->assertSame(
			$this->tag,
			$this->tag->disableRenderWhenEmpty()
		);
		$this->assertFalse($this->tag->isRenderWhenEmpty());

		$this->assertSame(
			$this->tag,
			$this->tag->enableRenderWhenEmpty()
		);
		$this->assertTrue($this->tag->isRenderWhenEmpty());
	}

	/**
	 * @return	null
	 */
	public function testIsEmpty()
	{
		$this->assertEquals('', $this->tag->getContentString());
		$this->assertTrue($this->tag->isEmpty());

		$this->tag->addContent('i am a title');
		$this->assertFalse($this->tag->isEmpty());
	}

	/**
	 * @return	null
	 */
	public function testBuildWithIsRenderWhenEmpty()
	{
		$this->assertTrue($this->tag->isEmpty());
		$this->assertTrue($this->tag->isClosingTag());
		$this->assertTrue($this->tag->isRenderWhenEmpty());

		$expected='<title></title>';
		$this->assertEquals($expected, $this->tag->build());

		$this->tag->disableRenderWhenEmpty();
		$this->assertEquals('', $this->tag->build());
	}

	/**
	 * @return	null
	 */
	public function testBuild()
	{
		$title = 'i am a title';
		$this->tag->addContent($title)
				  ->addAttribute('id', '1234');

		$result = $this->tag->build();
		$expected = '<title id="1234">i am a title</title>';
		$this->assertEquals($expected, $result);
	}
	
	/**
	 * @return	null
	 */
	public function testBuildTag()
	{
		$content = 'i am a title';
		$attr  = 'id="1234"';
		$result = $this->tag->buildTag($content, $attr);
		$expected = '<title id="1234">i am a title</title>';
		$this->assertEquals($expected, $result);
	}

	public function testBuildRenderWhenEmpty()
	{
		$this->assertTrue($this->tag->isEmpty());
		$this->assertTrue($this->tag->isClosingTag());
		$this->tag->enableRenderWhenEmpty();
		
		$tagName  = $this->tag->getTagName();
		$expected = "<$tagName></$tagName>";
		$this->assertEquals($expected, $this->tag->build());
		
		$this->tag->disableRenderWhenEmpty();
		$this->assertEquals('', $this->tag->build());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @erturn				null
	 */
	public function testBuildTagContentNotString($content)
	{
		$this->tag->buildTag($content, 'id="1234"');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @erturn				null
	 */
	public function testBuildTagAttrNotString($attr)
	{
		$this->tag->buildTag('i am content', $attr);
	}
}
