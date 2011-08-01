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
namespace Test\Appfuel\View;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Validate\Coordinator;

/**
 * Test the coordinator's ability to move raw and clean data aswell as add error text
 */
class CoordinatorTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Coordinator
	 */
	protected $coord = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->coord = new Coordinator();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->coord);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Validate\CoordinatorInterface',
			$this->coord
		);
	}

	/**
	 * You can set the raw source with setSource of by passing it into the
	 * the constructor
	 *
	 * @return	null
	 */
	public function testGetSetSource()
	{
		/* 
		 * when nothing is passed into the constructor the default source is
		 * an empty array
		 */
		$this->assertEquals(array(), $this->coord->getSource());

		$source = array('name' => 'value');
		$this->assertSame(
			$this->coord,
			$this->coord->setSource($source),
			'Must use a fluent interface'
		);
		$this->assertEquals($source, $this->coord->getSource());

		/* 
		 * can also set an empty array which has the effect of resetting the
		 * source
		 */
		$this->assertSame(
			$this->coord,
			$this->coord->setSource(array()),
			'Must use a fluent interface'
		);
		$this->assertEquals(array(), $this->coord->getSource());


		/* use the constructor to set the source */
		$coord = new Coordinator($source);
		$this->assertEquals($source, $coord->getSource());

		/* dictionary is a valid source */
		$this->assertSame(
			$this->coord,
			$this->coord->setSource(new Dictionary($source)),
			'Must use a fluent interface'
		);
		$this->assertEquals($source, $this->coord->getSource());
	}

	/**
	 * The Test class adds uses addClean while the Controller uses 
	 * getClean and GetAllClean
	 *
	 * @return null
	 */
	public function testGetGetAllAddClean()
	{
		/* default value is an empty array */
		$this->assertEquals(array(), $this->coord->getAllClean());

		$this->assertSame(
			$this->coord,
			$this->coord->addClean('key', 'value'),
			'must expose a fluent interface'
		);

		$this->assertEquals('value', $this->coord->getClean('key'));
		$this->assertEquals(array('key'=>'value'),$this->coord->getAllClean());
			

		$this->assertSame(
			$this->coord,
			$this->coord->addClean('foo', 'bar'),
			'must expose a fluent interface'
		);

		$expected = array(
			'key' => 'value',
			'foo' => 'bar'
		);
	
		$this->assertEquals($expected, $this->coord->getAllClean());
		$this->assertEquals('value', $this->coord->getClean('key'));
		$this->assertEquals('bar', $this->coord->getClean('foo'));

		/* default is ignored when key is found */	
		$this->assertEquals('bar', $this->coord->getClean('foo', 'my-value'));
			
		/* default is used  when key is not found */	
		$this->assertEquals(
			'default-value', 
			$this->coord->getClean('does-not-exist', 'default-value')
		);

		/* key can be a scalar value */
		$this->assertSame(
			$this->coord,
			$this->coord->addClean(123, 'value_123'),
			'must expose a fluent interface'
		);

		$expected = array(
			'key' => 'value',
			'foo' => 'bar',
			123   => 'value_123'
		);
		$this->assertEquals($expected, $this->coord->getAllClean());
		$this->assertEquals('value', $this->coord->getClean('key'));
		$this->assertEquals('bar', $this->coord->getClean('foo'));
		$this->assertEquals('value_123', $this->coord->getClean(123));

		/* default value returned when key is not found is null */
		$this->assertNull($this->coord->getClean('does-not-exist'));
		
		/* invalid keys always return default value */
		$this->assertEquals(
			'bad-key', 
			$this->coord->getClean(array(), 'bad-key'),
			'array is not a valid key'
		);

		$this->assertEquals(
			'bad-key', 
			$this->coord->getClean('', 'bad-key'),
			'empty string is not a valid key'
		);

		$this->assertEquals(
			'bad-key', 
			$this->coord->getClean(new StdClass(), 'bad-key'),
			'object is not a valid key'
		);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddCleanBadKeyEmptyString()
	{
		$this->coord->addClean('', 'some-value');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddCleanBadKeyArray()
	{
		$this->coord->addClean(array(1,2,3), 'some-value');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddCleanBadKeyObject()
	{
		$this->coord->addClean(new StdClass(), 'some-value');
	}

	/**
	 * This is used once the source has been set and looks for a key in the
	 * source array. If a key can not be found it returns a special token 
	 * string used to indicate the key was not found. This removed the 
	 * ambiguity associated with using null or false as values. The special
	 * token is returned via the function rawKeyNotFound
	 * 
	 * @return null
	 */
	public function testGetRawRawKeyNotFound()
	{
		$source = array(
			'foo' => 'bar',
			'baz' => false,
			'biz' => null,
			'fiz' => 'fiz_value'
		);
		$this->coord->setSource($source);
		$this->assertEquals($source['foo'], $this->coord->getRaw('foo'));
		$this->assertEquals($source['baz'], $this->coord->getRaw('baz'));
		$this->assertEquals($source['biz'], $this->coord->getRaw('biz'));
		$this->assertEquals($source['fiz'], $this->coord->getRaw('fiz'));

		/* try to get key that does not exist */
		$this->assertEquals(
			$this->coord->rawKeyNotFound(),
			$this->coord->getRaw('key-does-not-exist'),
			'special token is used to indicate that key was not found'
		);

		$this->assertEquals(
			$this->coord->rawKeyNotFound(),
			$this->coord->getRaw(''),
			'same token is used with invalid keys'
		);

		$this->assertEquals(
			$this->coord->rawKeyNotFound(),
			$this->coord->getRaw(array(1,2,3)),
			'same token is used with invalid keys'
		);

		$this->assertEquals(
			$this->coord->rawKeyNotFound(),
			$this->coord->getRaw(new StdClass()),
			'same token is used with invalid keys'
		);
	}

}
