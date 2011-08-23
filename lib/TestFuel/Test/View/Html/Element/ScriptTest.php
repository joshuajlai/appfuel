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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Element\Script;

/**
 * This implementation assumes the script tag is always javascript. If there
 * is a src attribute there should be no content when there is content there 
 * should be no src attribute
 */
class ScriptTest extends BaseTestCase
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
		$this->content  = 'alert("hello world!");';
        $this->tag = new Script($this->content);
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
		$this->assertEquals(
			'text/javascript', 
			$this->tag->getAttribute('type')
		);
	}

	/**
	 * @return null
	 */
	public function testValidAttributes()
	{
        $valid = array(
            'async',
            'charset',
			'defer',
			'src',
            'type'
        );

		foreach ($valid as $attr) {
			$this->assertTrue($this->tag->isValidAttribute($attr));
		}
	}

	/**
	 * Test build with content
	 * @return null
	 */
	public function testBuildWithContent()
	{
		$expected = '<script type="text/javascript">' . 
					$this->content . 
					'</script>';

		$this->assertEquals($expected, $this->tag->build());
	}

	/**
	 * Script tag with no content
	 * 
	 * @return null
	 */
	public function testBuildWithSrcAttribute()
	{
		$script = new Script();
		$script->addAttribute('src', '/path/to/resource.js');

		$expected = '<script type="text/javascript" ' .
					'src="/path/to/resource.js"></script>';


		$this->assertEquals($expected, $script->build());
	}
}
