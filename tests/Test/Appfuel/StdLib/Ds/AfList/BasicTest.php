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
namespace Test\Appfuel\StdLib\Ds\AfList;

/* import */
use Appfuel\StdLib\Ds\AfList\Basic as BasicList;

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

	/**
	 * Test isItem
	 * prove items that exist return TRUE and those that don't return FALSE	
	 */
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

	/**
	 * Test get
	 * Prove that items that exist can be retrieved and items that don't
	 * get will return whatever value is given in the second parameter
	 */
	public function testGet()
	{
		$this->assertEquals(0, $this->list->count());
		$data = array(
			'item_1'=>'value_1',
			'item_2'=>'value_2'
		);
		$this->list->load($data);

		$this->assertTrue($this->list->isItem('item_1'));
		$this->assertEquals($data['item_1'], $this->list->get('item_1'));
		
		$this->assertTrue($this->list->isItem('item_2'));
		$this->assertEquals($data['item_2'], $this->list->get('item_2'));

		/* test default value returned when item is not found */
		$this->assertFalse($this->list->isItem('item_not_there'));
		$this->assertNull($this->list->get('item_not_there'));
	
		$default = FALSE;	
		$this->assertFalse($this->list->get('item_not_there', $default));
	
		$default = TRUE;	
		$this->assertTrue($this->list->get('item_not_there', $default));
	
		$default = 'custom';	
		$this->assertEquals(
			$default, 
			$this->list->get('item_not_there', $default)
		);
		
	}
}

