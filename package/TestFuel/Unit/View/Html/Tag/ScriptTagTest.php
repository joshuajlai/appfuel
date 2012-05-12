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

use Appfuel\View\Html\Tag\ScriptTag,
	TestFuel\TestCase\BaseTestCase;

/**
 * This implementation assumes the script tag is always javascript. If there
 * is a src attribute there should be no content when there is content there 
 * should be no src attribute
 */
class ScriptTagTest extends BaseTestCase
{
    /**
     * System under test
     * @var ScriptTag
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
        $this->tag = new ScriptTag(null, $this->content);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        $this->tag = null;
    }

	/**
	 * The src is excluded because there are rules behind it which are test
	 * separately
	 *
	 * @return	array
	 */
	public function provideScriptAttributesNoSrc()
	{
		return array(
			array('type', 'text/html', 'text/html'),
			array('charset', 'UTF-8', 'UTF-8'),
			array('defer', null, null),
			array('async', null, null)
		);
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{

		$this->assertInstanceOf(
			'\Appfuel\View\Html\Tag\GenericTagInterface', 
			$this->tag
		);
		$this->assertFalse($this->tag->isEmpty());
		$this->assertEquals($this->content, $this->tag->getContentString());
		$this->assertTrue($this->tag->isAttribute('type'));
		$this->assertEquals(
			'text/javascript', 
			$this->tag->getAttribute('type')
		);
	}

	/**
	 * @return	null
	 */
	public function testEmptyConstructor()
	{
		$script = new ScriptTag();
		$this->assertFalse($script->isAttribute('src'));
		$this->assertTrue($script->isEmpty());
	}
	
	/**
	 * @expectedException	RunTimeException
	 * @return				null
	 */
	public function testConstructorSrcAndContent()
	{
		$script = new ScriptTag('my-script.js', 'alert("blah");');
	}

	/**
	 * @dataProvider	provideScriptAttributesNoSrc
	 * @return null
	 */
	public function testAttributesWithContent($attr, $value, $expected)
	{
		$this->assertSame(
			$this->tag,
			$this->tag->addAttribute($attr, $value)
		);
		$this->assertEquals($expected, $this->tag->getAttribute($attr));
		$this->assertTrue($this->tag->isAttribute($attr));
	}

	/**
	 * @dataProvider	provideScriptAttributesNoSrc
	 * @return null
	 */
	public function testAttributesWithSrc($attr, $value, $expected)
	{
		$script = new ScriptTag('my-src.js');
		$this->assertSame(
			$script,
			$script->addAttribute($attr, $value)
		);
		$this->assertEquals($expected, $script->getAttribute($attr));
		$this->assertTrue($script->isAttribute($attr));
	}

	/**
	 * @dataProvider	provideScriptAttributesNoSrc
	 * @return null
	 */
	public function testAttributesWithEmpty($attr, $value, $expected)
	{
		$script = new ScriptTag();
		$this->assertSame(
			$script,
			$script->addAttribute($attr, $value)
		);
		$this->assertEquals($expected, $script->getAttribute($attr));
		$this->assertTrue($script->isAttribute($attr));
	}

	/**
	 * @expectedException	RunTimeException
	 * @return				null
	 */
	public function testAddSrcWithContent()
	{
		$this->tag->addAttribute('src', 'my-js');
	}

	/**
	 * @expectedException	RunTimeException
	 * @return				null
	 */
	public function testAddContentWithSrc()
	{
		$script = new ScriptTag('my-src.js');
		$script->addContent('alert("blah");');
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
		$script = new ScriptTag('/path/to/resource.js');

		$expected = '<script type="text/javascript" ' .
					'src="/path/to/resource.js"></script>';


		$this->assertEquals($expected, $script->build());
	}
}
