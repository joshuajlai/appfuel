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
namespace TestFuel\Test\View\Html;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\HtmlDocTemplate;

/**
 * The Html doc is the main template for any html page. Its primary 
 * responibility is to manage all the elements of the document itself like the
 * head, body and scripts that get added to the bottom of the body.
 */
class HtmlDocTemplateTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $template = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->template	= new HtmlDocTemplate();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->template = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlDocTemplateInterface',
			$this->template
		);

		$this->assertInstanceOf(
			'Appfuel\View\ViewCompositeTemplateInterface',
			$this->template
		);

		$this->assertInstanceOf(
			'Appfuel\View\ViewTemplateInterface',
			$this->template
		);
	}
}
