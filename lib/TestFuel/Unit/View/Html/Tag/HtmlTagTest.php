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

use Appfuel\View\Html\Tag\HtmlTag,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HtmlTagTest extends BaseTestCase
{
    /**
     * System under test
     * @var HtmlTag
     */
    protected $html = null;

    /**
     * @return null
     */
    public function setUp()
    {   
        $this->html = new HtmlTag();
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
		$this->html = null;
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Tag\GenericTagInterface',
			$this->html
		);
		
		$this->assertEquals('html', $this->html->getTagName());
	}

	/**
	 * The html tag has locked the tag name so any attempt to change it will
	 * result it a LogicException
	 *
	 * @expectedException	LogicException
	 * @return				null
	 */
	public function testSetTagName_Failure()
	{
		$this->html->setTagName('link');
	}
}
