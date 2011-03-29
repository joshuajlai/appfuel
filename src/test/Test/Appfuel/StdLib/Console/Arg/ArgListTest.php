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
namespace Test\Appfuel\Stdlib\Console\Arg;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\Stdlib\Console\Arg\ArgList,
	Appfuel\Stdlib\BagInterface,
	Appfuel\Stdlib\Data\Bag,
	StdClass;

/**
 * Arglist is a datastructure that holds long, short options for commandline
 * as well as the commandline arguments.
 */
class ArgListTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Stdlib\Console\Arg\ArgList
	 */
	protected $arglist = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->arglist = new ArgList();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->arglist);
	}

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		/* an constructor with no parameters passed returns a count of 0 */
		$this->assertEquals(0, $this->arglist->count());
		$this->assertEquals(0, $this->arglist->countLong());	
		$this->assertEquals(0, $this->arglist->countShort());	
		$this->assertEquals(0, $this->arglist->countArg());	
	}

	/**
	 * Test the ability to add get and detect the existence of long options
	 * 
	 * @return null
	 */
	public function testAddGetIsLongOption()
	{
		/* add key value pair (--mylongopt=mylongvalue) */
		$key   = 'mylongopt';
		$value = 'mylongvalue';
		$result = $this->arglist->addLongOption($key, $value);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(1, $this->arglist->countLong());
		$this->assertTrue($this->arglist->isLongOption($key)); 
		$this->assertEquals($value, $this->arglist->getLongOption($key));

		/* add key only (--enable-test) */
		$key2 = 'enable-test';
		$result = $this->arglist->addLongOption($key2);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(2, $this->arglist->countLong());
		$this->assertTrue($this->arglist->isLongOption($key2)); 
		$this->assertNull($this->arglist->getLongOption($key2));

		/* add third option for good measure */
		$key3   = 'final-opt';
		$value3 = 'final-value';
		$result = $this->arglist->addLongOption($key3, $value3);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countLong());
		$this->assertTrue($this->arglist->isLongOption($key3)); 
		$this->assertEquals($value3, $this->arglist->getLongOption($key3));
	
		/* total overall count should be 3 */
		$this->assertEquals(3, $this->arglist->count());


		$result = $this->arglist->addLongOption(array(1,2));
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countLong());

		$result = $this->arglist->addLongOption(new stdClass());
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countLong());
	}

	/**
	 * Test the ability to add get and detect the existance of short options
	 * Also test to ensure of single characters can be added
	 * @return null
	 */
	public function testAddGetIsShortOption()
	{
		/* add key value pair (-a=myshortvalue) */
		$key   = 'a';
		$value = 'myshortvalue';
		$result = $this->arglist->addShortOption($key, $value);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(1, $this->arglist->countShort());
		$this->assertTrue($this->arglist->isShortOption($key)); 
		$this->assertEquals($value, $this->arglist->getShortOption($key));

		/* add key only (-b) */
		$key2 = 'b';
		$result = $this->arglist->addShortOption($key2);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(2, $this->arglist->countShort());
		$this->assertTrue($this->arglist->isShortOption($key2)); 
		$this->assertNull($this->arglist->getShortOption($key2));

		/* add third option for good measure */
		$key3   = 'c';
		$value3 = 'final-value';
		$result = $this->arglist->addShortOption($key3, $value3);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countShort());
		$this->assertTrue($this->arglist->isShortOption($key3)); 
		$this->assertEquals($value3, $this->arglist->getShortOption($key3));
	
		/* total overall count should be 3 */
		$this->assertEquals(3, $this->arglist->count());

		/* add third option invalid string length */
		$key4   = 'casdasd';
		$value4 = 'final-value';
		$result = $this->arglist->addShortOption($key4, $value4);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countShort());
		$this->assertFalse($this->arglist->isShortOption($key4)); 
		$this->assertNull($this->arglist->getShortOption($key4));
	
		/* total overall count should be 3 */
		$this->assertEquals(3, $this->arglist->count());

		$result = $this->arglist->addShortOption(array(1,2));
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countShort());

		$result = $this->arglist->addShortOption(new stdClass());
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countShort());



	}


	/**
	 * Test adding and detecting the existance of regular arguments. Regular
	 * arguments do not have the shape of key value pairs they are only a list
	 * of scalar items.
	 * 
	 * @return null
	 */
	public function testAddIsGetAllArgs()
	{
		$args = array(
			'myarg',
			'yourArg',
			'out-arg'
		);

		$result = $this->arglist->addArg($args[0]);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(1, $this->arglist->countArg());
		$this->assertTrue($this->arglist->isArg($args[0]));
		$this->assertEquals(array('myarg'), $this->arglist->getArgs());


		$result = $this->arglist->addArg($args[1]);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(2, $this->arglist->countArg());
		$this->assertTrue($this->arglist->isArg($args[0]));
		$this->assertTrue($this->arglist->isArg($args[1]));
		$this->assertEquals(
			array('myarg','yourArg'), 
			$this->arglist->getArgs()
		);

		$result = $this->arglist->addArg($args[2]);
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countArg());
		$this->assertTrue($this->arglist->isArg($args[0]));
		$this->assertTrue($this->arglist->isArg($args[1]));
		$this->assertTrue($this->arglist->isArg($args[2]));
		$this->assertEquals($args, $this->arglist->getArgs());

		$this->assertEquals(3, $this->arglist->count());

		/* try adding bad datatypes */
		$result = $this->arglist->addArg(array(1,2,3));
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countArg());

		$result = $this->arglist->addArg(new stdClass());
		$this->assertSame($this->arglist, $result);
		$this->assertEquals(3, $this->arglist->countArg());
	}
}
