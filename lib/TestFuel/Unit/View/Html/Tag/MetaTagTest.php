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

use TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Tag\MetaTag;

/**
 * Test the meta tag
 */
class MetaTest extends BaseTestCase
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
			'\Appfuel\View\Html\Tag\HtmlTag',
			$this->meta
		);

		/*
		 * no content should be added to this tag  
		 */
		$this->assertTrue($this->meta->isEmpty());
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
		$this->assertTrue($meta->isAttribute('name'));
		$this->assertFalse($meta->isAttribute('content'));
		$this->assertEquals('<meta name="my-name"/>', $meta->build());
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
