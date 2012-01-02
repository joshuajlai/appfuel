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
	Appfuel\View\Html\Tag\HeadTag,
	Appfuel\View\Html\Tag\BodyTag,
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
	 * Used to create mock objects and check object types
	 * @var string
	 */
	protected $tagInterface = 'Appfuel\View\Html\Tag\GenericTagInterface';

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
		$this->assertInstanceOf($this->tagInterface, $this->html);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagInterface', 
			$this->html
		);
		
		$this->assertEquals('html', $this->html->getTagName());
		
		$head = $this->html->getHead();
		$this->assertInstanceOf($this->tagInterface, $head);
		$this->assertEquals('head', $head->getTagName());
		
		$body = $this->html->getBody();
		$this->assertInstanceOf($this->tagInterface, $body);
		$this->assertEquals('body', $body->getTagName());
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

	/**
	 * @return	null
	 */
	public function testGetSetHead()
	{
		$head = $this->getMock($this->tagInterface);
		$head->expects($this->once())
			 ->method('getTagName')
			 ->will($this->returnValue('head'));

		$this->assertSame($this->html, $this->html->setHead($head));
		$this->assertSame($head, $this->html->getHead());	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetHeadWrongTagName_Failure()
	{
		$head = $this->getMock($this->tagInterface);
		$head->expects($this->once())
			 ->method('getTagName')
			 ->will($this->returnValue('link'));

		$this->html->setHead($head);
	}

	/**
	 * @return	null
	 */
	public function testGetSetBody()
	{
		$body = $this->getMock($this->tagInterface);
		$body->expects($this->once())
			 ->method('getTagName')
			 ->will($this->returnValue('body'));

		$this->assertSame($this->html, $this->html->setBody($body));
		$this->assertSame($body, $this->html->getBody());	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetBodyTagName_Failure()
	{
		$body = $this->getMock($this->tagInterface);
		$body->expects($this->once())
			 ->method('getTagName')
			 ->will($this->returnValue('link'));

		$this->html->setBody($body);
	}


}
