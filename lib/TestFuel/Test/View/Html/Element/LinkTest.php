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
namespace TestFuel\Test\View\Html\Element;

use Appfuel\View\Html\Element\Tag,
	Appfuel\View\Html\Element\Link,
	TestFuel\TestCase\BaseTestCase;

/**
 * The link tag exposes the valid attributes for html5. The constructor takes
 * a single argument for the href. the type attribute is automatically added
 * with with text/css and rel with stylesheet. 
 */
class Linkest extends BaseTestCase
{
    /**
     * System under test
     * @var Message
     */
    protected $link = null;

	/**
	 * Passed into the constructor as the value of the href
	 * @var string
	 */
	protected $href = null;

    /**
     * @return null
     */
    public function setUp()
    {   
		$this->href   = '/path/to/resource';
        $this->link = new Link($this->href);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        unset($this->link);
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Element\Tag',
			$this->link,
			'must extend the tag class'
		);

		/*
		 * this tag does not have any content
		 */
		$expected = array();
		$this->assertEquals($expected, $this->link->getContent());

		$this->assertTrue($this->link->attributeExists('href'));
		$this->assertEquals($this->href, $this->link->getAttribute('href'));
		$this->assertTrue($this->link->isValidHref());

		$this->assertTrue($this->link->attributeExists('rel'));
		$this->assertEquals('stylesheet', $this->link->getAttribute('rel'));
		$this->assertEquals('text/css', $this->link->getAttribute('type'));
	}

	/**
	 * @return null
	 */
	public function testValidAttributes()
	{
        $valid = array(
            'href',
            'hreflang',
            'media',
            'rel',
            'sizes',
            'type'
        );

		foreach ($valid as $attr) {
			$this->assertTrue($this->link->isValidAttribute($attr));
		}
	}

	/**
	 * This is used to determine if the href attribute is present
	 *
	 * @return null
	 */
	public function testIsValidHref()
	{
		$link = new Link();
		$this->assertFalse($link->isValidHref());

		$link->addAttribute('href', '/path/to/resource');	
		$this->assertTrue($link->isValidHref());

		$link = new Link('');
		$this->assertFalse($link->isValidHref());
	}

	/**
	 * @return null
	 */
	public function testBuild()
	{
		$expected = '<link rel="stylesheet" type="text/css" href="' .
					$this->href . '"/>';

		$this->assertEquals($expected, $this->link->build());

		/* will not render without a href */
		$link = new Link();
		$this->assertEquals('', $link->build());
	}
}
