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
namespace Test\Appfuel\StdLib\Ds\AfIterator;

/* import */
use Appfuel\StdLib\Ds\AfIterator\Basic as BasicIterator;

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
	 * Basic intial state after the object initialization 
	 */
	public function testConstructor()
	{
		$this->assertEquals(0, $this->basic->count()); 
		$this->assertNull($this->basic->key());
		$this->assertFalse($this->basic->current());
		$this->assertFalse($this->basic->next());
		$this->assertFalse($this->basic->valid());
	}
	
	/**
	 * Test count, valid, key, next, current, rewind
	 * This is the Countable and Iterator interfaces
	 */
	public function testIteratorCountable()
	{
		$label_1 = 'foo';
		$value_1 = 'bar';
		$result = $this->basic->add($label_1, $value_1);
		$this->assertSame($this->basic, $result);
		$this->assertEquals(1, $this->basic->count());
		$this->assertEquals('foo', $this->basic->key());
		$this->assertEquals('bar', $this->basic->current());
		$this->assertTrue($this->basic->valid());

		/* prove nothing is next */
		$this->assertFalse($this->basic->next());
		$this->assertFalse($this->basic->valid());

		/* add another item to the iterator */
		$label_2 = 'baz';
		$value_2 = 'biz';
		$result = $this->basic->add($label_2, $value_2);
		$this->assertSame($this->basic, $result);
		$this->assertEquals(2, $this->basic->count());
		
		/* 
		 * we already called next so the current will be pointing
		 * a the second item which is baz
		 */
		$this->assertEquals('baz', $this->basic->key());
		$this->assertEquals('biz', $this->basic->current());
		$this->assertTrue($this->basic->valid());

		/*
		 * pass the last element and test 
		 */
		$this->assertFalse($this->basic->next());
		$this->assertFalse($this->basic->valid());
		
		/* rewind goes back to first key */
		$this->assertEquals('bar', $this->basic->rewind());
	}

	/**
	 * Test add
	 * Keys can only be alpha numberic
	 * @expectedException	\Appfuel\StdLib\Ds\AfIterator\Exception
	 */
	public function testAddKeyIsArray()
	{
		$this->basic->add(array(1,2,3), 'bar');
	}

	/**
	 * Test add 
	 * Keys can only be a scalar value
	 * @expectedException	\Appfuel\StdLib\Ds\AfIterator\Exception
	 */
	public function testAddKeyIsObject()
	{
		$this->basic->add(new \stdClass(), 'bar');
	}
}

