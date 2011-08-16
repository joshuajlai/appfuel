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
	Appfuel\View\Html\Element\Style;

/**
 * 
 */
class StyleTest extends ParentTestCase
{
    /**
     * System under test
     * @var Message
     */
    protected $tag = null;

	/**
	 * Passed into the constructor as the css content 
	 * @var string
	 */
	protected $content = null;

    /**
     * @return null
     */
    public function setUp()
    {   
		$this->content  = 'h1{color:red}';
        $this->tag = new Style($this->content);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        unset($this->tag);
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Element\Tag',
			$this->tag,
			'must extend the tag class'
		);

		/*
		 * this tag does not have any content
		 */
		$expected = array($this->content);
		$this->assertEquals($expected, $this->tag->getContent());

		$this->assertTrue($this->tag->attributeExists('type'));
		$this->assertEquals('text/css', $this->tag->getAttribute('type'));
	}

	/**
	 * @return null
	 */
	public function testValidAttributes()
	{
        $valid = array(
            'media',
            'scope',
            'type'
        );

		foreach ($valid as $attr) {
			$this->assertTrue($this->tag->isValidAttribute($attr));
		}
	}

	/**
	 * @return null
	 */
	public function testBuild()
	{
		$expected = '<style type="text/css">' . $this->content . '</style>';

		$this->assertEquals($expected, $this->tag->build());

		/* will not render without a href */
		$style = new Style();
		$this->assertEquals('', $style->build());
	}
}
