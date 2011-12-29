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

use Appfuel\View\Html\Tag\LinkTag,
	TestFuel\TestCase\BaseTestCase;

/**
 * The link tag exposes the valid attributes for html5. The constructor takes
 * a single argument for the href. the type attribute is automatically added
 * with with text/css and rel with stylesheet. 
 */
class LinkTagTest extends BaseTestCase
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
        $this->link = new LinkTag($this->href);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
		$this->link = null;
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Tag\GenericTagInterface',
			$this->link
		);
	}

	public function provideValidLinkAttributes()
	{
		return array(
			array('hreflang'),
			array('media'),
			array('sizes'),
		);	
	}

	/**
	 * @dataProvider	provideValidLinkAttributes
	 * @return null
	 */
	public function testValidAttributes($attr)
	{
		$value = 'my-value';
		$this->assertFalse($this->link->isAttribute($attr));
		$this->assertSame(
			$this->link,
			$this->link->addAttribute($attr, $value)
		);
		$this->assertTrue($this->link->isAttribute($attr));
		$this->assertEquals($value, $this->link->getAttribute($attr));
	}

	/**
	 * @expectedException	RunTimeException
	 * @return				null
	 */
	public function testInvalidAttrubute()
	{
		$this->link->addAttribute('not-listed', 'value');
	}

	/**
	 * @return null
	 */
	public function testBuild()
	{
		$expected = '<link rel="stylesheet" type="text/css" href="' .
					$this->href . '"/>';

		$this->assertEquals($expected, $this->link->build());
	}
}
