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

use Appfuel\View\ViewPathFinder,
	TestFuel\TestCase\BaseTestCase;

/**
 * The view template is a basic template that uses a text formatter by default
 * to convert its data into a string
 */
class ViewPathFinderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ViewPathFinder
	 */
	protected $finder = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->finder = new ViewPathFinder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->finder = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\File\PathFinderInterface',
			$this->finder
		);
	}

	/**
	 * @return	null
	 */
	public function testFinderValues()
	{
		$this->assertTrue($this->finder->isBasePathEnabled());
		$this->assertEquals(AF_BASE_PATH, $this->finder->getBasePath());
		
		$relative = 'ui/appfuel';
		$this->assertEquals($relative, $this->finder->getRelativeRootPath());
	
		$expected = AF_BASE_PATH . '/ui/appfuel';
		$this->assertEquals($expected, $this->finder->resolveRootPath());

		$this->finder->disableBasePath();
		$this->assertEquals($relative, $this->finder->resolveRootPath());

		$this->assertEquals($relative, $this->finder->getPath());
		
		$this->finder->enableBasePath();
		$this->assertEquals($expected, $this->finder->resolveRootPath());
	}
}
