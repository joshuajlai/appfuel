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

use StdClass,
	SplFileInfo,
	Appfuel\View\Html\Element\Tag,
	TestFuel\TestCase\BaseTestCase;

/**
 * The html element tag is used to automate the rendering of the html element
 * and provide a simpler interface to add data to the element
 */
class TagTest extends BaseTestCase
{
    /**
     * System under test
     * @var Message
     */
    protected $tag = null;

    /**
     * @return null
     */
    public function setUp()
    {   
        $this->tag = new Tag();
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        unset($this->tag);
    }

	/**
	 * The separator is used to during building to seperate each content item.
	 * The default separator for all tags is an empty space.
	 *
	 * @return null
	 */
	public function testGetSetSeparator()
	{
		$this->assertEquals(' ', $this->tag->getSeparator());
		
		$sep = ':';
		$this->assertSame(
			$this->tag,
			$this->tag->setSeparator($sep),
			'must use a fluent interface'
		);
	
		$this->assertEquals($sep, $this->tag->getSeparator());

		/* should be able to use an empty string */
		$sep = '';
		$this->assertSame(
			$this->tag,
			$this->tag->setSeparator($sep),
			'must use a fluent interface'
		);
		
		$this->assertEquals($sep, $this->tag->getSeparator());

		/* non scalar values are ignored */
		$this->assertSame(
			$this->tag,
			$this->tag->setSeparator(array(1,2,3)),
			'must use a fluent interface'
		);

		/* same has old value that was set */
		$this->assertEquals($sep, $this->tag->getSeparator());
			
		$this->assertSame(
			$this->tag,
			$this->tag->setSeparator(new StdClass()),
			'must use a fluent interface'
		);
		$this->assertEquals($sep, $this->tag->getSeparator());
			
	}

	/**
	 * Atrribute validation consists of testing an incoming attribute
	 * against a white list of valid attributes
	 *
	 * @return null
	 */
	public function testEnableDisableAtrributeValidation()
	{
		/* default value is enabled */
		$this->assertTrue($this->tag->isAttributeValidation());

		$this->assertSame(
			$this->tag,
			$this->tag->disableAttributeValidation()
		);
		$this->assertFalse($this->tag->isAttributeValidation());

		$this->assertSame(
			$this->tag,
			$this->tag->enableAttributeValidation()
		);
		$this->assertTrue($this->tag->isAttributeValidation());
	}

	/**
	 * Atrributes can also be turned off when not needed for a tag. Methods
	 * used are enableAttributes, disableAttributes and isAttrubutes
	 *
	 * @return null
	 */
	public function testEnableDisableAtrributes()
	{
		/* default value is enabled */
		$this->assertTrue($this->tag->isAttributesEnabled());

		$this->assertSame(
			$this->tag,
			$this->tag->disableAttributes()
		);
		$this->assertFalse($this->tag->isAttributesEnabled());

		$this->assertSame(
			$this->tag,
			$this->tag->enableAttributes()
		);
		$this->assertTrue($this->tag->isAttributesEnabled());
	}

	/**
	 * By default the white list is populated with html5 global attributes
	 * 
	 * @return null
	 */
	public function testGetClearAttributeWhiteList()
	{
		$globalAttrs = array(
			'accessKey',
			'class',
			'contextmenu',
			'dir',
			'draggable',
			'dropzone',
			'hidden',
			'id',
			'lang',
			'spellcheck',
			'style',
			'tabindex',
			'title'
		);

		$this->assertEquals($globalAttrs, $this->tag->getAttributeWhiteList());
		$this->assertSame(
			$this->tag,
			$this->tag->clearAttributeWhiteList(),
			'uses fluent interface'
		);
		$result = $this->tag->getAttributeWhiteList();
		$this->assertInternalType('array', $result);
		$this->assertEquals($globalAttrs, $result);
	}

	/**
	 * Test the ability to add valid attributes. There seems to be no
	 * reason to retreive the white list attribute because you always
	 * ask the tag isValidAttribute and it does the check for you
	 *
	 * @return null
	 */
	public function testAddRemoveIsValidAttribute()
	{
		$attr = 'some-attr';
		$this->assertFalse($this->tag->isValidAttribute($attr));
		$this->assertSame(
			$this->tag,
			$this->tag->addValidAttribute($attr),
			'uses fluent interface'
		);
		$this->assertTrue($this->tag->isValidAttribute($attr));

		$attr2 = 'some-attr2';
		$this->assertFalse($this->tag->isValidAttribute($attr2));
		$this->tag->addValidAttribute($attr2);
		$this->assertTrue($this->tag->isValidAttribute($attr2));

		$this->assertSame(
			$this->tag,
			$this->tag->removeValidAttribute($attr2),
			'uses fluent interface'
		);
		$this->assertFalse($this->tag->isValidAttribute($attr2));
	}

	/**
	 * @return null
	 */
	public function testAddValidAttributeNonString()
	{
		$attr = 'some-attr';
		$this->assertFalse($this->tag->isValidAttribute($attr));
		$this->tag->addValidAttribute($attr);
		$this->assertTrue($this->tag->isValidAttribute($attr));

		$this->assertSame(
			$this->tag,
			$this->tag->addValidAttribute(array(1,2,3)),
			'will not throw an execption'
		);
		$this->assertFalse($this->tag->isValidAttribute(array(1,2,3)));
	
		$obj = new StdClass();
		$this->assertSame(
			$this->tag,
			$this->tag->addValidAttribute($obj),
			'will not throw an execption'
		);
		$this->assertFalse($this->tag->isValidAttribute($obj));
		
	}

	/**
	 * @return null
	 */
	public function testRemoveWithBadKey()
	{
		$attr = 'some-attr';
		$this->tag->addValidAttribute($attr);
		$this->assertTrue($this->tag->isValidAttribute($attr));

		$this->assertSame(
			$this->tag,
			$this->tag->removeValidAttribute(array(1,2,3)),
			'never returns errors always fluent interface'
		);
		$this->assertTrue($this->tag->isValidAttribute($attr));

		$this->assertSame(
			$this->tag,
			$this->tag->removeValidAttribute(new StdClass()),
			'never returns errors always fluent interface'
		);
		$this->assertTrue($this->tag->isValidAttribute($attr));
	}

	/**
	 * @return null
	 */
	public function testaddValidAttributes()
	{
		$attrs = array(
			'my-attr',
			'your-attr',
			'their-attr',
			'our-attr'
		);

		foreach ($attrs as $attr) {
			$this->assertFalse($this->tag->isValidAttribute($attr));
		}

		$this->assertSame(
			$this->tag,
			$this->tag->addValidAttributes($attrs),
			'uses fluent interface'
		);

		foreach ($attrs as $attr) {
			$this->assertTrue($this->tag->isValidAttribute($attr));
		}
	}

	/**
	 * @return null
	 */
	public function testAddAttributesBadStrings()
	{
		$obj   = new StdClass();
		$array = array();
		$attrs = array(
			$obj,
			$array,
		);

		foreach ($attrs as $attr) {
			$this->assertFalse($this->tag->isValidAttribute($attr));
		}

		$this->assertSame(
			$this->tag,
			$this->tag->addValidAttributes($attrs),
			'uses fluent interface'
		);

		foreach ($attrs as $attr) {
			$this->assertFalse($this->tag->isValidAttribute($attr));
		}	
	}

	/**
	 * @return null
	 */
	public function testaddGoodAndBadStringsValidAttributes()
	{

		$obj   = new StdClass();
		$array = array();
		$attrs = array(
			'my-attr',
			$obj,
			$array,
			'our-attr'
		);

		foreach ($attrs as $attr) {
			$this->assertFalse($this->tag->isValidAttribute($attr));
		}

		$this->assertSame(
			$this->tag,
			$this->tag->addValidAttributes($attrs),
			'uses fluent interface'
		);

		$this->assertFalse($this->tag->isValidAttribute($obj));
		$this->assertFalse($this->tag->isValidAttribute($array));
		
		$this->assertTrue($this->tag->isValidAttribute('my-attr'));
		$this->assertTrue($this->tag->isValidAttribute('our-attr'));
	}

	/**
	 * Test adding getting checking if it exists and removing an attribute. 
	 * The default state of isAttributesEnabled is true
	 *
	 * @return null
	 */
	public function testAddGetExists()
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => 'my-id',
			'name'  => 'my-name'
		);
		
		$this->tag->addValidAttributes(array_keys($attrs));
		foreach ($attrs as $attr => $value) {
			$this->assertFalse($this->tag->attributeExists($attr));

			$this->assertSame(
				$this->tag,
				$this->tag->addAttribute($attr, $value),
				'uses fluent interface'
			);
			$this->assertTrue($this->tag->attributeExists($attr));
			$this->assertEquals($value, $this->tag->getAttribute($attr));

		}

		/* test using the default value of getAttribute */
		$this->assertEquals(
			'my-value', 
			$this->tag->getAttribute('not-here', 'my-value'),
			'not-here does not exist so my-value will be returned'
		);	
	}

	/**
	 *
	 * @return null
	 */
	public function testAddAttributes()
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => 'my-id',
			'name'  => 'my-name'
		);
		
		$this->tag->addValidAttributes(array_keys($attrs));
		foreach ($attrs as $attr => $value) {
			$this->assertFalse($this->tag->attributeExists($attr));
		}

		$this->assertSame(
			$this->tag,
			$this->tag->addAttributes($attrs),
			'uses fluent interface'
		);
		foreach ($attrs as $attr => $value) {
			$this->assertTrue($this->tag->attributeExists($attr));
			$this->assertEquals($value, $this->tag->getAttribute($attr));
		}
	}


	/**
	 * This test covers the use case of trying to add attributes when 
	 * attributes have been disabled
	 *
	 * @return null
	 */
	public function testAddGetExistsWhenAttributesDisabled()
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => 'my-id',
			'name'  => 'my-name'
		);

		$this->tag->addValidAttributes(array_keys($attrs));
		$this->tag->disableAttributes();

		$this->assertFalse($this->tag->isAttributesEnabled());	
	
		/* nothing will be added because attributes are disabled */	
		foreach ($attrs as $attr => $value) {
			$this->assertFalse($this->tag->attributeExists($attr));

			$this->assertSame(
				$this->tag,
				$this->tag->addAttribute($attr, $value),
				'uses fluent interface'
			);
			$this->assertFalse($this->tag->attributeExists($attr));
			$this->assertNull($this->tag->getAttribute($attr));
		}
	}

	/**
	 * This test covers the use case of trying to add attributes that are 
	 * not in the white list (not valid attributes).
	 *
	 * @return null
	 */
	public function testAddGetExistsWhenAttributesNotValid()
	{
		$attrs = array(
			'my-class' => 'my-class',
			'my-id'    => 'my-id',
			'my-name'  => 'my-name'
		);
		/*
		 * for this test we will need to remove the list of global attributes
		 */
		$this->tag->clearAttributeWhiteList();

		/*
		 * because there are not valid attributes, because we removed them
		 * then with attribute validation enabled no attribute in this loop
		 * will be added to the list
		 */
		$this->tag->enableAttributeValidation();
		$this->assertTrue($this->tag->isAttributeValidation());	
		foreach ($attrs as $attr => $value) {
			$this->assertFalse($this->tag->attributeExists($attr));

			$this->assertSame(
				$this->tag,
				$this->tag->addAttribute($attr, $value),
				'uses fluent interface'
			);
			$this->assertFalse($this->tag->attributeExists($attr));
			$this->assertNull($this->tag->getAttribute($attr));
		}

		/* add the valid attributes. now all these attributes can be added
		 */
		$this->tag->addValidAttributes(array_keys($attrs));
		foreach ($attrs as $attr => $value) {
			$this->assertFalse($this->tag->attributeExists($attr));

			$this->tag->addAttribute($attr, $value);

			$this->assertTrue($this->tag->attributeExists($attr));
			$this->assertEquals($value, $this->tag->getAttribute($attr));
		}
	}

	/**
	 * @return null
	 */
	public function testRemoveAttributes()
	{
		$attrs = array(
			'class' => 'my-class',
			'id'    => 'my-id',
			'name'  => 'my-name'
		);
		
		/* we have not added any valid attributes so all these attrs
		 * will not be added 
		 */
		$this->tag->disableAttributeValidation();
		$this->tag->addAttributes($attrs);
		foreach ($attrs as $attr => $value) {
			$this->assertTrue($this->tag->attributeExists($attr));
			$this->assertSame(
				$this->tag,
				$this->tag->removeAttribute($attr),
				'this is a fluent interface'
			);
			$this->assertFalse($this->tag->attributeExists($attr));
			$this->assertNull($this->tag->getAttribute($attr));
		}
	}

	/**
	 * Tag name is used in building the html tag string. Currently there
	 * is no validation on setting a valid html tag name.
	 *
	 * @return null
	 */
	public function testGetSetTagName()
	{
		/* initial value is null */
		$this->assertNull($this->tag->getTagName());

		$this->assertSame(
			$this->tag,
			$this->tag->setTagName('table'),
			'this is a fluent interface'
		);

		$this->assertEquals('table', $this->tag->getTagName());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTagNameArray()
	{
		$this->tag->setTagName(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTagNameObject()
	{
		$this->tag->setTagName(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTagNameInt()
	{
		$this->tag->setTagName(12345);
	}

	/**
	 * These methods are used to determine how to build the html tag
	 * string. We are testing the default value is true and the
	 * enable\disable are working as expected
	 *
	 * @return null
	 */
	public function testEnableDisableIsClosingTag()
	{
		$this->assertTrue($this->tag->isClosingTag());
		$this->assertSame(
			$this->tag,
			$this->tag->disableClosingTag(),
			'uses a fluent interface'
		);
		$this->assertFalse($this->tag->isClosingTag());
			
		$this->assertSame(
			$this->tag,
			$this->tag->enableClosingTag(),
			'uses a fluent interface'
		);
		$this->assertTrue($this->tag->isClosingTag());
	}

	/**
	 * @return null
	 */
	public function testBuildAttributesDisabledAttrs()
	{
		/* so we can add any attributes */
		$this->tag->disableAttributeValidation();

		$this->tag->addAttribute('id', 333)
				  ->addAttribute('class', 'my-class');

		/* when attributes disabled an empty string is returned */
		$this->tag->disableAttributes();

		$this->assertEquals('', $this->tag->buildAttributes());	
	}

	/**
	 * @return null
	 */
	public function testBuildAttributes()
	{
		/* so we can add any attributes */
		$this->tag->disableAttributeValidation();

		$this->tag->addAttribute('id', 333)
				  ->addAttribute('class', 'my-class');

		$result = $this->tag->buildAttributes();
		$expected = 'id="333" class="my-class"';
		$this->assertEquals($expected, $result);
	}

	/**	
	 * When addContent is used with no second parameter then the assumed
	 * action is append. This will append the content onto the content array
	 *
	 * @return null
	 */
	public function testAddGetContentDefaultAction()
	{
		/* default value is an empty array */
		$result = $this->tag->getContent();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);

		$block1 = 'i am block one';
		$block2 = 'i am block two';
		$block3 = 'i am block three';
		$this->assertSame(
			$this->tag,
			$this->tag->addContent($block1),
			'this is a fluent interface'
		);
		$expected = array($block1);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->tag->addContent($block2);
		$expected = array($block1, $block2);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->tag->addContent($block3);
		$expected = array($block1, $block2, $block3);
		$this->assertEquals($expected, $this->tag->getContent());
	}

	/**	
	 * Same as the test above but with the explicit call
	 *
	 * @return null
	 */
	public function testAddGetContentAppend()
	{
		$block1 = 'i am block one';
		$block2 = 'i am block two';
		$block3 = 'i am block three';
			
		$this->tag->addContent($block1, 'append');
		$expected = array($block1);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->tag->addContent($block2, 'append');
		$expected = array($block1, $block2);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->tag->addContent($block3, 'append');
		$expected = array($block1, $block2, $block3);
		$this->assertEquals($expected, $this->tag->getContent());
	}

	/**	
	 * Test prepending content
	 *
	 * @return null
	 */
	public function testAddGetContentPrepend()
	{
		$block1 = 'i am block one';
		$block2 = 'i am block two';
		$block3 = 'i am block three';
			
		$this->tag->addContent($block1, 'prepend');
		$expected = array($block1);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->tag->addContent($block2, 'prepend');
		$expected = array($block2, $block1);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->tag->addContent($block3, 'prepend');
		$expected = array($block3, $block2, $block1);
		$this->assertEquals($expected, $this->tag->getContent());
	}

	/**	
	 * Test replacing content
	 *
	 * @return null
	 */
	public function testAddGetContentReplace()
	{
		$block1 = 'i am block one';
		$block2 = 'i am block two';
		$block3 = 'i am block three';
			
		$this->tag->addContent($block1, 'replace');
		$expected = array($block1);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->tag->addContent($block2, 'replace');
		$expected = array($block2);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->tag->addContent($block3, 'replace');
		$expected = array($block3);
		$this->assertEquals($expected, $this->tag->getContent());
	}

	/**
	 * Content is stored as an array of blocks. Each block is concatenated
	 * togather with the separator character from getSeparator. We will
	 * test building content with the default separator
	 *
	 * @return null
	 */
	public function testBuildContentDefaultSeparator()
	{
		$block1 = 'i am block one';
		$block2 = 'i am block two';
		$block3 = 'i am block three';
			
		$this->tag->addContent($block1, 'append')
				  ->addContent($block2, 'append')
				  ->addContent($block3, 'append');

		$expected = $block1 . ' ' . $block2 . ' ' . $block3;
		$this->assertEquals($expected, $this->tag->buildContent());
	}

	/**
	 * Same as above with a different separator
	 * @return null
	 */
	public function testBuildContentDefaultSetSeparator()
	{
		$sep = ':';
		$this->tag->setSeparator($sep);

		$block1 = 'i am block one';
		$block2 = 'i am block two';
		$block3 = 'i am block three';
			
		$this->tag->addContent($block1, 'append')
				  ->addContent($block2, 'append')
				  ->addContent($block3, 'append');

		$expected = $block1 . $sep . $block2 . $sep . $block3;
		$this->assertEquals($expected, $this->tag->buildContent());
	}

	/**
	 * Same as above with scalar content types integers
	 * @return null
	 */
	public function testBuildContentIntegers()
	{
		$sep = ':';
		$this->tag->setSeparator($sep);

		$block1 = 123;
		$block2 = 456;
		$block3 = 789;
			
		$this->tag->addContent($block1, 'append')
				  ->addContent($block2, 'append')
				  ->addContent($block3, 'append');

		$expected = $block1 . $sep . $block2 . $sep . $block3;
		$this->assertEquals($expected, $this->tag->buildContent());
	}


	/**
	 * Same as above with scalar content types arrays.  Each array will 
	 * be expoded into a string an each item will be glued togather with
	 * the default separator.
	 *
	 * @return null
	 */
	public function testBuildContentArrays()
	{
		$sep = ':';
		$this->tag->setSeparator($sep);

		$block1 = array(1,2,3);
		$block2 = array(4,5, 6);
		$block3 = array(7, 8, 9);
			
		$this->tag->addContent($block1, 'append')
				  ->addContent($block2, 'append')
				  ->addContent($block3, 'append');

		$expected = '1:2:3:4:5:6:7:8:9';
		$this->assertEquals($expected, $this->tag->buildContent());
	}

	/**
	 * @return null
	 */
	public function testBuildContentObjectsWithToString()
	{
		$sep = ':';
		$this->tag->setSeparator($sep);

		$block1 = new SplFileInfo('path1');
		$block2 = new SplFileInfo('path2');
		$block3 = new SplFileInfo('path3');
			
		$this->tag->addContent($block1, 'append')
				  ->addContent($block2, 'append')
				  ->addContent($block3, 'append');

		$expected = 'path1:path2:path3';
		$this->assertEquals($expected, $this->tag->buildContent());
	}

	/**
	 * This test will use the interface to build a link tag and check that
	 * the string produced by the build is as expected. This tag has no
	 * close tag and uses only attributes
	 *
	 * @return null
	 */
	public function testBuildLinkTag()
	{
		/* so we can add any attributes */
		$this->tag->disableAttributeValidation();
		$this->tag->setTagName('link')
				  ->disableClosingTag()
				  ->addAttribute('rel', 'stylesheet')
				  ->addAttribute('type', 'text/css')
				  ->addAttribute('href', 'style.css');

		$expected = '<link rel="stylesheet" type="text/css" href="style.css"/>';
		$this->assertEquals($expected, $this->tag->build());
	}

	/**
	 * Test building a tag that uses content and therfore has a closing tag
	 *
	 * @return null
	 */
	public function testBuildAnchorTag()
	{
		/* so we can add any attributes */
		$this->tag->disableAttributeValidation();
		$this->tag->setTagName('a')
				  ->enableClosingTag()
				  ->addAttribute('id', 'my-id')
				  ->addAttribute('class', 'my-class')
				  ->addAttribute('href', '/some/uri/path')
			      ->addContent('click me!');

		$expected = '<a id="my-id" class="my-class" href="/some/uri/path">' .
					'click me!</a>';
	
		$this->assertEquals($expected, $this->tag->build());
	}

	/**
	 * @return null
	 */
	public function testToString()
	{
		/* so we can add any attributes */
		$this->tag->disableAttributeValidation();
		$this->tag->setTagName('a')
				  ->enableClosingTag()
				  ->addAttribute('id', 'my-id')
				  ->addAttribute('href', '/some/uri/path')
				  ->addContent('click me!');

		$expected = '<a id="my-id" href="/some/uri/path">click me!</a>';
		ob_start();
		echo $this->tag;
		$result = ob_get_contents();
		ob_clean();

		$this->assertEquals($expected, $result);
	}
}

