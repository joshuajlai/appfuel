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
namespace TestFuel\Unit\View\Html;

use StdClass,
	SplFileInfo,
	Appfuel\View\Html\HtmlBody,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HtmlBodyTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlBody
	 */
	protected $body = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->body = new HtmlBody();
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
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlBodyInterface',
			$this->body
		);
		
		$body = $this->body->getBodyTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\BodyTag',
			$body
		);
		$this->assertTrue($body->isEmpty());
		$this->assertTrue($this->body->isJs());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetBodyTag()
	{
		$body = $this->getMock('Appfuel\View\Html\Tag\GenericTagInterface');
		$body->expects($this->any())
			 ->method('getTagName')
			 ->will($this->returnValue('body'));

		$this->assertSame($this->body, $this->body->setBodyTag($body));
		$this->assertSame($body, $this->body->getBodyTag());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testIsEnableDisableJs()
	{
		$this->assertSame($this->body, $this->body->disableJs());		
		$this->assertFalse($this->body->isJs());

		$this->assertSame($this->body, $this->body->enableJs());
		$this->assertTrue($this->body->isJs());
	}
}
