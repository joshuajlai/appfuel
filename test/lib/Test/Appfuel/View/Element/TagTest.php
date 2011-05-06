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
namespace Test\Appfuel\View\Html\Element;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\View\Html\Element\Tag,
	StdClass;

/**
 * The html element tag is used to automate the rendering of the html element
 * and provide a simpler interface to add data to the element
 */
class TagTest extends ParentTestCase
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
			'class' => 'my-class',
			'id'    => 'my-id',
			'name'  => 'my-name'
		);
		
		/* we have not added any valid attributes so all these attrs
		 * will not be added 
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




}

