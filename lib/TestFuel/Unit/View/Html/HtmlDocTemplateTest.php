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
	Appfuel\View\Html\Element\Meta\HttpEquiv,
	Appfuel\View\Html\Element\Meta\Tag as MetaTag,
	Appfuel\View\Html\HtmlDocTemplate;

/**
 */
class HtmlDocTemplateTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlDocTemplate
	 */
	protected $htmlDoc = null;

    /**
     * Path to template file 
     * @var string
     */
    protected $templatePath = null;

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
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\ViewTemplateInterface',
			$this->htmlDoc
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetTitleTag()
	{
		/* default value */
		$title = $this->htmlDoc->getTitleTag();
		$this->assertInstanceOf('Appfuel\View\Html\Element\Title', $title);
		$this->assertEmpty($title->getContent());

		$myTitle = $this->getMock($this->tagInterface);
		$myTitle->expects($this->once())
				->method('getTagName')
				->will($this->returnValue('title'));
		
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setTitleTag($myTitle)
		);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetTitleNotTitleTag_Failure()
	{
		$myTitle = $this->getMock($this->tagInterface);
		$myTitle->expects($this->once())
				->method('getTagName')
				->will($this->returnValue('image'));
		
		$this->htmlDoc->setTitleTag($myTitle);
	}

	/**
	 * setTitle adds text to the title tags content
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetTitleReplace()
	{
		$title = $this->htmlDoc->getTitleTag();
		$this->assertEquals(array(), $title->getContent());

		$content1 = 'my title';
		$this->assertSame(
			$this->htmlDoc, 
			$this->htmlDoc->setTitle($content1, 'replace'));

		$this->assertEquals(array($content1), $title->getContent());
		
		$content2 = 'my other title';
		$this->assertSame($this->htmlDoc, $this->htmlDoc->setTitle($content2));

		$this->assertEquals(array($content2), $title->getContent());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetTitleAppend()
	{
		$title = $this->htmlDoc->getTitleTag();
		$this->assertEquals(array(), $title->getContent());
			
		$content1 = 'my title';
		$this->assertSame(
			$this->htmlDoc, 
			$this->htmlDoc->setTitle($content1, 'append')
		);
		
		$this->assertEquals(array($content1), $title->getContent());
		
		$content2 = 'my other title';
		$this->assertSame(
				$this->htmlDoc, 
				$this->htmlDoc->setTitle($content2, 'append')
		);

		$expected = array($content1, $content2);
		$this->assertEquals($expected, $title->getContent());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetTitlePrepend()
	{
		$title = $this->htmlDoc->getTitleTag();
		$this->assertEquals(array(), $title->getContent());
			
		$content1 = 'my title';
		$this->assertSame(
			$this->htmlDoc, 
			$this->htmlDoc->setTitle($content1, 'prepend')
		);
		
		$this->assertEquals(array($content1), $title->getContent());
		
		$content2 = 'my other title';
		$this->assertSame(
				$this->htmlDoc, 
				$this->htmlDoc->setTitle($content2, 'prepend')
		);

		$expected = array($content2, $content1);
		$this->assertEquals($expected, $title->getContent());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetTitleSeparator()
	{
		$title = $this->htmlDoc->getTitleTag();
		$this->assertEquals(' ', $title->getSeparator());

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setTitleSeparator(':')
		);
		$this->assertEquals(':', $title->getSeparator());
	}

	/**
	 * @return	array
	 */
	public function provideAttrsAndBuildStr()
	{
		return array(
			array(array('id' => '12345'), 'id="12345"'),
			array(array('class' => 'my-class'), 'class="my-class"'),
			array(array('checked' => null), 'checked'),
			array(
				array('id' => '12345', 'class' => 'my-class'),
				'id="12345" class="my-class"'
			),
			array(
				array('id' => '12345', 'checked'=> null),
				'id="12345" checked'
			),
			/* does not guard against bad illogical inputs */
			array(
				array('id' => null, 'class' => null, 'checked' => null),
				'id class checked'
			),
			array(array(), ''),

			/* build is not responsible for trimming even though it trims the
			 * final string
			 */
			array(
				array("   id"=>'12345', 'class'=>"my-class "),
				'id="12345" class="my-class "'
			)
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddGetHtmlTagAttributes()
	{
		$this->assertEquals(array(), $this->htmlDoc->getHtmlAttributes());
		
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addHtmlAttribute('class', 'my-class')
		);

		$expected = array('class' => 'my-class');
		$this->assertEquals($expected, $this->htmlDoc->getHtmlAttributes());

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addHtmlAttribute('id', 'my-id')
		);

		$expected['id'] = 'my-id';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addHtmlAttribute('id', 'my-id')
		);
		$this->assertEquals($expected, $this->htmlDoc->getHtmlAttributes());


		/* this is how you might treat an enumerated attribute */
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addHtmlAttribute('checked')
		);
		$expected['checked'] = null;
		$this->assertEquals($expected, $this->htmlDoc->getHtmlAttributes());
	}

	/**
	 * This is just a wrapper that loops the given array and calls 
	 * addHtmlAttribute
	 *
	 * @depends	testAddGetHtmlTagAttributes
	 * @return	null
	 */
	public function testSetHtmlAttributes()
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => 'my-id',
			'checked' => null
		);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setHtmlAttributes($attrs)
		);

		$this->assertEquals($attrs, $this->htmlDoc->getHtmlAttributes());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideEmptyStrings
	 * @return				null
	 */
	public function testAddHtmlAttributeInvalidName_Failure($name)
	{
		$this->htmlDoc->addHtmlAttribute($name, 'my-value');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testAddHtmlAttributeInvalidValue_Failure($value)
	{
		$this->htmlDoc->addHtmlAttribute('id', $value);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideEmptyStrings
	 * @return	null
	 */
	public function testSetHtmlAttributesInvalidName_Failure($name)
	{
		$attrs = array(
			'class' => 'my-class',
			$name    => 'my-id',
			'checked' => null
		);

		$this->htmlDoc->setHtmlAttributes($attrs);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideInvalidStrings
	 * @return	null
	 */
	public function testSetHtmlAttributesInvalidValue_Failure($value)
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => $value,
			'checked' => null
		);

		$this->htmlDoc->setHtmlAttributes($attrs);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddGetHeadTagAttributes()
	{
		$this->assertEquals(array(), $this->htmlDoc->getHeadAttributes());
		
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addHeadAttribute('class', 'my-class')
		);

		$expected = array('class' => 'my-class');
		$this->assertEquals($expected, $this->htmlDoc->getHeadAttributes());

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addHeadAttribute('id', 'my-id')
		);

		$expected['id'] = 'my-id';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addHeadAttribute('id', 'my-id')
		);
		$this->assertEquals($expected, $this->htmlDoc->getHeadAttributes());


		/* this is how you might treat an enumerated attribute */
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addHeadAttribute('checked')
		);
		$expected['checked'] = null;
		$this->assertEquals($expected, $this->htmlDoc->getHeadAttributes());
	}

	/**
	 * This is just a wrapper that loops the given array and calls 
	 * addHtmlAttribute
	 *
	 * @depends	testAddGetHtmlTagAttributes
	 * @return	null
	 */
	public function testSetHeadAttributes()
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => 'my-id',
			'checked' => null
		);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setHeadAttributes($attrs)
		);

		$this->assertEquals($attrs, $this->htmlDoc->getHeadAttributes());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideEmptyStrings
	 * @return				null
	 */
	public function testAddHeadAttributeInvalidName_Failure($name)
	{
		$this->htmlDoc->addHeadAttribute($name, 'my-value');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testAddHeadAttributeInvalidValue_Failure($value)
	{
		$this->htmlDoc->addHeadAttribute('id', $value);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideEmptyStrings
	 * @return	null
	 */
	public function testSetHeadAttributesInvalidName_Failure($name)
	{
		$attrs = array(
			'class' => 'my-class',
			$name    => 'my-id',
			'checked' => null
		);

		$this->htmlDoc->setHeadAttributes($attrs);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideInvalidStrings
	 * @return	null
	 */
	public function testSetHeadAttributesInvalidValue_Failure($value)
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => $value,
			'checked' => null
		);

		$this->htmlDoc->setHtmlAttributes($attrs);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddGetBodyAttributes()
	{
		$this->assertEquals(array(), $this->htmlDoc->getBodyAttributes());
		
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addBodyAttribute('class', 'my-class')
		);

		$expected = array('class' => 'my-class');
		$this->assertEquals($expected, $this->htmlDoc->getBodyAttributes());

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addBodyAttribute('id', 'my-id')
		);

		$expected['id'] = 'my-id';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addBodyAttribute('id', 'my-id')
		);
		$this->assertEquals($expected, $this->htmlDoc->getBodyAttributes());


		/* this is how you might treat an enumerated attribute */
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addBodyAttribute('checked')
		);
		$expected['checked'] = null;
		$this->assertEquals($expected, $this->htmlDoc->getBodyAttributes());
	}

	/**
	 * This is just a wrapper that loops the given array and calls 
	 * addHtmlAttribute
	 *
	 * @depends	testAddGetHtmlTagAttributes
	 * @return	null
	 */
	public function testSetBodyAttributes()
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => 'my-id',
			'checked' => null
		);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setBodyAttributes($attrs)
		);

		$this->assertEquals($attrs, $this->htmlDoc->getBodyAttributes());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideEmptyStrings
	 * @return				null
	 */
	public function testAddBodyAttributeInvalidName_Failure($name)
	{
		$this->htmlDoc->addBodyAttribute($name, 'my-value');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testAddBodyAttributeInvalidValue_Failure($value)
	{
		$this->htmlDoc->addBodyAttribute('id', $value);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideEmptyStrings
	 * @return	null
	 */
	public function testSetBodyAttributesInvalidName_Failure($name)
	{
		$attrs = array(
			'class' => 'my-class',
			$name    => 'my-id',
			'checked' => null
		);

		$this->htmlDoc->setBodyAttributes($attrs);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddGetHtmlTagAttributes
	 * @dataProvider		provideInvalidStrings
	 * @return	null
	 */
	public function testSetBodyAttributesInvalidValue_Failure($value)
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => $value,
			'checked' => null
		);

		$this->htmlDoc->setBodyAttributes($attrs);
	}

	/**
	 * @dataProvider	provideAttrsAndBuildStr
	 * @return			null
	 */
	public function testBuildAttributeString($attrs, $expected)
	{
		$result = $this->htmlDoc->buildAttributeString($attrs);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return	null
	 */
	public function testDefaultCharsetIsUtf8()
	{
		$this->assertEquals('UTF-8', $this->htmlDoc->getCharset());
	}

	/**
	 * @return	array
	 */
	public function provideCharsets()
	{
		return array(
			array('UTF-16'),
			array('UTF-8'),
			array('ISO-8859-1'),
			array('ISO-8859-2'),
			array('ISO-8859-3'),
			array('ISO-2022-JP')
		);
	}

	/**
	 * @dataProvider	provideCharsets
	 * @return			null
	 */
	public function testSetGetCharset($encoding)
	{
		$this->assertSame(
			$this->htmlDoc, 
			$this->htmlDoc->setCharset($encoding)
		);

		$result = $this->htmlDoc->getCharset();
		$this->assertEquals($encoding, $result);
	}

    /**
     * @expectedException   InvalidArgumentException
     * @depends             testInterface
     * @dataProvider        provideEmptyStrings
     * @return  null
     */
	public function testSetCharsetEmptyString_Failure($encoding)
	{
		$this->htmlDoc->setCharset($encoding);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultBaseTagDoesNotExist()
	{
		$this->assertNull($this->htmlDoc->getBaseTag());
	}

	/**
	 * @return	null
	 */
	public function testGetSetBaseTag()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('base'));

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setBaseTag($tag)
		);
		$this->assertSame($tag, $this->htmlDoc->getBaseTag());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetBaseTagNotBase()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('title'));


		$this->htmlDoc->setBaseTag($tag);
	}

	public function testSetBaseHref()
	{
		$href = 'http://www.myurl.com/news/index.html';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setBase($href)
		);
		$base = $this->htmlDoc->getBaseTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Base',
			$base
		);
		$this->assertEquals($href, $base->getAttribute('href'));
	}

	public function testSetBaseTarget()
	{
		$target = '_self';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setBase(null, $target)
		);
		$base = $this->htmlDoc->getBaseTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Base',
			$base
		);
		$this->assertEquals($target, $base->getAttribute('target'));
	}

	public function testSetBaseHrefTarget()
	{
		$href = 'http://www.myurl.com/news/index.html';
		$target = '_self';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setBase($href, $target)
		);
		$base = $this->htmlDoc->getBaseTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Base',
			$base
		);
		$this->assertEquals($href, $base->getAttribute('href'));
		$this->assertEquals($target, $base->getAttribute('target'));
	}

	public function testSetBaseEmpty()
	{
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setBase(null, null)
		);
		$base = $this->htmlDoc->getBaseTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Base',
			$base
		);
		$this->assertNull($base->getAttribute('href'));
		$this->assertNull($base->getAttribute('target'));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultMetaTags()
	{
		$this->assertEquals(array(), $this->htmlDoc->getMetaTags());
		
	}

	public function testAddGetMetaTags()
	{
		$tag1 = new MetaTag('my-name', 'my-content');
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addMetaTag($tag1)
		);
		$expected = array($tag1);
		$this->assertEquals($expected, $this->htmlDoc->getMetaTags());

		$tag2 = new MetaTag('other-name', 'other-content');
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addMetaTag($tag2)
		);
		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $this->htmlDoc->getMetaTags());

		$tag3 = new HttpEquiv('Content-Type', 'text/html');
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addMetaTag($tag3)
		);
		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $this->htmlDoc->getMetaTags());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetMeta()
	{
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addMeta('my-name', 'my-content')
		);
		$tag1 = new MetaTag('my-name', 'my-content');
		$expected = array($tag1);
		$this->assertEquals($expected, $this->htmlDoc->getMetaTags());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsCssEnableDisableCss()
	{
		$this->assertTrue($this->htmlDoc->isCssEnabled());

		$this->assertSame($this->htmlDoc, $this->htmlDoc->disableCss());
		$this->assertFalse($this->htmlDoc->isCssEnabled());
		
		$this->assertSame($this->htmlDoc, $this->htmlDoc->enableCss());
		$this->assertTrue($this->htmlDoc->isCssEnabled());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsJsEnableDisable()
	{
		$this->assertTrue($this->htmlDoc->isJsEnabled());

		$this->assertSame($this->htmlDoc, $this->htmlDoc->disableJs());
		$this->assertFalse($this->htmlDoc->isJsEnabled());
		
		$this->assertSame($this->htmlDoc, $this->htmlDoc->enableJs());
		$this->assertTrue($this->htmlDoc->isJsEnabled());
	}
}
