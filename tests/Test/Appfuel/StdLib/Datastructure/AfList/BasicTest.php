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
namespace Test\Appfuel\StdLib\Datastructure\AfList;

/* import */
use Appfuel\StdLib\Datastructure\AfList\Basic as BasicList;

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
	protected $list = NULL;

	/**
	 * @return void
	 */
	public function setUp()
	{
		$this->list = new BasicList();
	}

	/**
	 * @return void
	 */
	public function tearDown()
	{
		unset($this->list);
	}

	public function testIsItem()
	{
		$this->assertEquals(0, $this->list->count());
		
		$data = array(
			'item_1'=>'value_1',
			'item_2'=>'value_2'
		);

		/* items that are in the list must be found */
		$this->list->load($data);
		$this->assertTrue($this->list->isItem('item_1'));
		$this->assertTrue($this->list->isItem('item_2'));

		/* items that are not in the list are not found */
		$this->assertFalse($this->list->isItem('no_item'));
	}

}

