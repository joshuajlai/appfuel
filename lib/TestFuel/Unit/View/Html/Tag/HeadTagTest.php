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
	Appfuel\View\Html\Tag\LinkTag,
	Appfuel\View\Html\Tag\MetaTag,
	Appfuel\View\Html\Tag\HeadTag,
	Appfuel\View\Html\Tag\BaseTag,
	Appfuel\View\Html\Tag\StyleTag,
	Appfuel\View\Html\Tag\TitleTag,
	Appfuel\View\Html\Tag\ScriptTag,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HeadTagTest extends BaseTestCase
{
    /**
     * System under test
     * @var HeadTag
     */
    protected $head = null;

    /**
     * @return null
     */
    public function setUp()
    {   
        $this->head = new HeadTag();
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
			'\Appfuel\View\Html\Tag\GenericTagInterface',
			$this->head
		);

		$this->assertEquals('head', $this->head->getTagName());
		$this->assertTrue($this->head->isEmpty());
		$this->assertEquals('', $this->head->getContentString());
		$this->assertEquals('', $this->head->getAttributeString());
	
		$this->assertFalse($this->head->isBase());
		$this->assertFalse($this->head->isMeta());
		$this->assertFalse($this->head->isLinks());
		$this->assertFalse($this->head->isStyle());
		$this->assertFalse($this->head->isScripts());
		$this->assertFalse($this->head->isInlineScript());
	}

	/**
	 * No reason to do this but it will work
	 * 
	 * @return	null
	 */
	public function testSetTagName()
	{
		$this->assertSame($this->head, $this->head->setTagName('head'));
		$this->assertEquals('head', $this->head->getTagName());
	}

	/**
	 * @expectedException	LogicException
	 * @return				null
	 */
	public function testSetTagNameNotHead()
	{
		$this->head->setTagName('title');
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testTitle()
	{
		$this->assertTrue($this->head->isTitle());
		$result = $this->head->getTitle();
		$this->assertInstanceOf('Appfuel\View\Html\Tag\TitleTag', $result);
		$this->assertTrue($result->isEmpty());
	
		$title = new TitleTag("my title");
		$this->assertSame($this->head, $this->head->setTitle($title));
		$this->assertSame($title, $this->head->getTitle());
		$this->assertTrue($this->head->isTitle());
	}

	/**
	 * @depends	testTitle
	 * @return	null
	 */
	public function testSetTitleText()
	{
		$text1 = 'first text';
		$title = $this->head->getTitle();
		
		$this->assertTrue($title->isEmpty());
		$this->assertSame($this->head, $this->head->setTitleText($text1));
		$this->assertFalse($title->isEmpty());
		$this->assertEquals(1, $title->getContentCount());

		$text2 = 'second text';
		$this->assertSame($this->head, $this->head->setTitleText($text2));
		$this->assertEquals(2, $title->getContentCount());

		$expected = array($text1, $text2);
		$this->assertEquals($expected, $title->getContent());
	}

	public function testTitleSeparator()
	{
		$char  = ':';
		$title = $this->head->getTitle();
		$this->assertEquals(' ', $title->getContentSeparator());
		$this->assertSame($this->head, $this->head->setTitleSeparator($char));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testBase()
	{
		$base = new BaseTag("someurl.com");
		$this->assertSame($this->head, $this->head->setBase($base));
		$this->assertSame($base, $this->head->getBase());
		$this->assertTrue($this->head->isBase());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testMeta()
	{
		$meta1 = new MetaTag('author', 'robert');
		$meta2 = new MetaTag('framework', 'appfuel');
		$meta3 = new MetaTag(null, 'text/html', 'Content-Type', 'EUC-JP');
		$this->assertSame(
			$this->head,
			$this->head->addMeta($meta1)
		);

		$expected = array($meta1);
		$this->assertEquals($expected, $this->head->getMeta());

		$this->assertSame(
			$this->head,
			$this->head->addMeta($meta2)
		);

		$expected = array($meta1, $meta2);
		$this->assertEquals($expected, $this->head->getMeta());

		$this->assertSame(
			$this->head,
			$this->head->addMeta($meta3)
		);

		$expected = array($meta1, $meta2, $meta3);
		$this->assertEquals($expected, $this->head->getMeta());

		$this->assertTrue($this->head->isMeta());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testLinks()
	{
		$link1 = new LinkTag('reset.css');
		$link2 = new LinkTag('base.css');
		$link3 = new LinkTag('page.css');
		$this->assertSame(
			$this->head,
			$this->head->addLink($link1)
		);

		$expected = array($link1);
		$this->assertEquals($expected, $this->head->getLinks());

		$this->assertSame(
			$this->head,
			$this->head->addLink($link2)
		);

		$expected = array($link1, $link2);
		$this->assertEquals($expected, $this->head->getLinks());

		$this->assertSame(
			$this->head,
			$this->head->addLink($link3)
		);

		$expected = array($link1, $link2, $link3);
		$this->assertEquals($expected, $this->head->getLinks());

		$this->assertTrue($this->head->isLinks());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testStyle()
	{
		$style = new StyleTag("p{color:red}");
		$this->assertSame($this->head, $this->head->setStyle($style));
		$this->assertSame($style, $this->head->getStyle());
		$this->assertTrue($this->head->isStyle());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testScripts()
	{
		$script1 = new ScriptTag('file.js');
		$script2 = new ScriptTag('file1.js');
		$script3 = new ScriptTag('file3.js');
		$this->assertSame(
			$this->head,
			$this->head->addScript($script1)
		);

		$expected = array($script1);
		$this->assertEquals($expected, $this->head->getScripts());

		$this->assertSame(
			$this->head,
			$this->head->addScript($script2)
		);

		$expected = array($script1, $script2);
		$this->assertEquals($expected, $this->head->getScripts());

		$this->assertSame(
			$this->head,
			$this->head->addScript($script3)
		);

		$expected = array($script1, $script2, $script3);
		$this->assertEquals($expected, $this->head->getScripts());

		$this->assertTrue($this->head->isScripts());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddScriptNoSource_Failure()
	{
		$script = new ScriptTag(null, 'alert("this is a test");');
		$this->head->addScript($script);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testInlineScript()
	{
		$script = new ScriptTag(null, 'alert("this is a test");');
		$this->assertSame($this->head, $this->head->setInlineScript($script));
		$this->assertSame($script, $this->head->getInlineScript());
		$this->assertTrue($this->head->isInlineScript());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetInlineScriptSource_Failure()
	{
		$script = new ScriptTag('my-file.js');
		$this->head->setInlineScript($script);
	}

}
