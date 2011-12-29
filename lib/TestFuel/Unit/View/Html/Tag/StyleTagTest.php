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
namespace TestFuel\Style\View\Html\Element;

use Appfuel\View\Html\Tag\StyleTag,
	TestFuel\TestCase\BaseTestCase;

/**
 * 
 */
class StyleTestTag extends BaseTestCase
{
    /**
     * System under test
     * @var StyleTage
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
        $this->tag = new StyleTag($this->content);
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
			'\Appfuel\View\Html\Tag\HtmlTagInterface',
			$this->tag
		);

		/*
		 * this tag does not have any content
		 */
		$this->assertFalse($this->tag->isEmpty());
		$this->assertTrue($this->tag->isClosingTag());
		$this->assertFalse($this->tag->isRenderWhenEmpty());
		$this->assertTrue($this->tag->isAttribute('type'));
		$this->assertEquals('text/css', $this->tag->getAttribute('type'));
	}

	/**
	 * @return null
	 */
	public function testValidAttributes()
	{
		$value = 'some-value';
		$this->assertFalse($this->tag->isAttribute('scope'));
		$this->assertSame(
			$this->tag, 
			$this->tag->addAttribute('scope', $value)
		);
		$this->assertTrue($this->tag->isAttribute('scope'));
		$this->assertEquals($value, $this->tag->getAttribute('scope'));

		$this->assertFalse($this->tag->isAttribute('media'));
		$this->assertSame(
			$this->tag, 
			$this->tag->addAttribute('media', $value)
		);
		$this->assertTrue($this->tag->isAttribute('media'));
		$this->assertEquals($value, $this->tag->getAttribute('media'));
	}

	/**
	 * @return null
	 */
	public function testBuild()
	{
		$expected = '<style type="text/css">' . $this->content . '</style>';

		$this->assertEquals($expected, $this->tag->build());
	}
}
