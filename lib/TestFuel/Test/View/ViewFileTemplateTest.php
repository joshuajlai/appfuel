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
	TestFuel\TestPathFinder,
	Appfuel\View\ViewFileTemplate,
	TestFuel\TestCase\BaseTestCase;

/**
 * The view file is an application file that knows where the resource
 * directory is.
 */
class ViewFileTemplateTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ViewFileTemplate
	 */
	protected $template = null;

	/**
	 * Relative path used in constructor
	 * @var string
	 */
	protected $filePath = null;

	protected $pathFinder = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->pathFinder = new TestPathFinder();
		$this->filePath = 'ui/appfuel/template.phtml';
		$this->template = new ViewFileTemplate(
			$this->filePath,
			null,
			$this->pathFinder
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->pathFinder = null;
		$this->template   = null;
		$this->filePath   = null;
	}

	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\View\ViewFileTemplateInterface',
			$this->template
		);
	}
}
