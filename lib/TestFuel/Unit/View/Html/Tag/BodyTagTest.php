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

use Appfuel\View\Html\Tag\BodyTag,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class BodyTagTest extends BaseTestCase
{
    /**
     * System under test
     * @var BodyTag
     */
    protected $body = null;

    /**
     * @return null
     */
    public function setUp()
    {   
        $this->body = new BodyTag();
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
		$this->body = null;
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Tag\GenericTagInterface',
			$this->body
		);
		
		$this->assertEquals('body', $this->body->getTagName());
	}

	/**
	 * @return null
	 */
	public function testSetTagNameBody()
	{
		$this->assertSame(
			$this->body,
			$this->body->setTagName('body'),
			'only body is allowed to be set'
		);

		$this->assertEquals('body', $this->body->getTagName());

		$this->assertSame(
			$this->body,
			$this->body->setTagName('BODY'),
			'only body is allowed to be set'
		);
		$this->assertEquals('BODY', $this->body->getTagName());
	}

	/**
	 * @expectedException	LogicException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetTagNameNotString_Failure($name)
	{
		$this->body->setTagName($name);
	}

	/**
	 * @expectedException	LogicException
	 * @return				null
	 */
	public function testSetTagName_Failure()
	{
		$this->body->setTagName('link');
	}

	/**
	 * @return	string
	 */
	public function testBuild()
	{
		$content1 = '<h1>i am a header</h1>';
		$content2 = '<p> i an some text </p>';
		
		$list = array($content1, $content2);
		$this->body->loadContent($list)
				   ->addAttribute('id', 'my-body');

		$sep = $this->body->getContentSeparator();
		$expected = "<body id=\"my-body\">{$content1}{$sep}{$content2}</body>";
		$this->assertEquals($expected, $this->body->build());
	}
}
