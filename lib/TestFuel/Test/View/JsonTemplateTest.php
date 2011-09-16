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
namespace TestFuel\Test\View;

use StdClass,
	SplFileInfo,
	Appfuel\View\JsonTemplate,
	TestFuel\TestCase\BaseTestCase;

/**
 * The Html doc is the main template for any html page. Its primary 
 * responibility is to manage all the elements of the document itself like the
 * head, body and scripts that get added to the bottom of the body.
 */
class JsonTemplateTest extends BaseTestCase
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
		$this->template	= new JsonTemplate();
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
			'Appfuel\Framework\View\JsonTemplateInterface',
			$this->template
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\View\ViewTemplateInterface',
			$this->template
		);
	}
}
