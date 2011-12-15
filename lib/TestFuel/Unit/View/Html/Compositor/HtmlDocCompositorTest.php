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
namespace TestFuel\Unit\View\Compositor;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Compositor\HtmlDocCompositor;

/**
 */
class HtmlDocCompositorTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlDocCompositor
	 */
	protected $compositor = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->compositor = new HtmlDocCompositor();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->compositor = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Compositor\ViewCompositorInterface',
			$this->compositor
		);

		$this->assertInstanceOf(
			'Appfuel\View\Html\Compositor\HtmlCompositorInterface',
			$this->compositor
		);

		$this->assertInstanceOf(
			'Appfuel\View\Html\Compositor\HtmlDocCompositorInterface',
			$this->compositor
		);
	}
}
