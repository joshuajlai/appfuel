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
namespace Test\Appfuel\View\Html\Element\Meta;

use Test\AfTestCase						as ParentTestCase,
	Appfuel\View\Html\Element\Meta\Tag  as MetaTag,
	Appfuel\View\Html\Element\Tag;

/**
 * Test the meta tag
 */
class MetaTest extends ParentTestCase
{
    /**
     * System under test
     * @var Base
     */
    protected $meta = null;

	/**
	 * Passed into the constructor name attribute value
	 * @var string
	 */
	protected $name = null;

	/**
	 * Passed into the constructor content attribute value
	 * @var string
	 */
	protected $content = null;

    /**
     * @return null
     */
    public function setUp()
    {   
		$this->name    = 'author';
		$this->content = 'Robert Scott-Buccleuch';
        $this->meta    = new MetaTag($this->name, $this->content);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        unset($this->base);
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Element\Tag',
			$this->meta,
			'must extend the tag class'
		);

		/*
		 * no content should be added to this tag  
		 */
		$expected = array();
		$this->assertEquals($expected, $this->meta->getContent());
	}

	/**
	 * The constructor will not add the name attribute unless the content
	 * is provided.
	 *
	 * @return null
	 */
	public function testConstructorNoContent()
	{
		$meta = new MetaTag('my-name');

		$this->assertFalse($meta->attributeExists('name'));
		$this->assertFalse($meta->attributeExists('content'));
		$this->assertEquals('', $meta->build());
	}

	/**
	 * @return null
	 */
	public function testIsValidCharset()
	{
		$meta = new MetaTag();
		$this->assertFalse($meta->isValidCharset());
		
		$meta->addAttribute('charset', 'UTF8');
		$this->assertTrue($meta->isValidCharset());

		/* 
		 * we have to create a new meta because after the charset was added
		 * it wont let you add http-equiv or name
		 */
		$meta = new MetaTag();
		$meta->addAttribute('http-equiv', 'refresh');
		$this->assertFalse($meta->isValidCharset());

		/* 
		 * we have to create a new meta because after the charset was added
		 * it wont let you add http-equiv or name
		 */
		$meta = new MetaTag();
		$meta->addAttribute('name', 'author');
		$this->assertFalse($meta->isValidCharset());
	}

	/**
	 * @return null
	 */
	public function testIsValidHttpEquiv()
	{
		$meta = new MetaTag();
		$this->assertFalse($meta->isValidHttpEquiv());
		
		$meta->addAttribute('http-equiv', 'refresh');
		
		/* false because it requires content to be available */
		$this->assertFalse($meta->isValidHttpEquiv());
		
		$meta->addAttribute('content', '5');
		$this->assertTrue($meta->isValidHttpEquiv());

		$meta = new MetaTag('author', 'Robert');
		$this->assertFalse($meta->isValidHttpEquiv());
		
		$meta = new MetaTag();
		$meta->addAttribute('charset', 'UTF8');
		$this->assertFalse($meta->isValidHttpEquiv());
	}

	/**
	 * @return null
	 */
	public function testIsValidName()
	{
		$meta = new MetaTag();
		$this->assertFalse($meta->isValidName());
		
		$meta->addAttribute('name', 'author');
		
		/* false because it requires content to be available */
		$this->assertFalse($meta->isValidName());
		
		$meta->addAttribute('content', 'Robert');
		$this->assertTrue($meta->isValidName());

		$meta = new MetaTag('author', 'Robert');
		$this->assertTrue($meta->isValidName());
		
		$meta = new MetaTag();
		$meta->addAttribute('charset', 'UTF8');
		$this->assertFalse($meta->isValidName());
	}

	/**
	 * @return null
	 */
	public function testBuild()
	{
		$expected = '<meta name="' . $this->name   . 
					'" content="'   . $this->content . '"/>';
		
		$this->assertEquals($expected, $this->meta->build()); 
	}
}
