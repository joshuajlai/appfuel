<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @category 	Tests
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Test\Appfuel\StdLib\AfIterator;

/* import */
use Appfuel\StdLib\AfIterator\Basic as BasicIterator;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class BasicTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Auto Loader
	 * System under test
	 * @var BasicIterator
	 */
	protected $basic = NULL;

	/**
	 * @return void
	 */
	public function setUp()
	{
		$this->basic = new BasicIterator();
	}

	/**
	 * @return void
	 */
	public function tearDown()
	{
		unset($this->basic);
	}

	/**
	 * Test __construct
	 * 
	 * Assert:	count returns 0
	 */
	public function testConstructor()
	{
		$this->assertEquals(0, $this->basic->count());
	}
}

