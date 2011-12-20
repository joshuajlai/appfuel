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
	Appfuel\View\Html\HtmlPage,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HtmlPageTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlCompositor
	 */
	protected $compositor = null;

    /**
     * Path to template file 
     * @var string
     */
    protected $templatePath = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->page = new HtmlPage();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->page = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\ViewTemplateInterface',
			$this->page
		);
	}
}
