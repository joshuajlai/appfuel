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
namespace TestFuel\Unit\Validate;

use StdClass,
	Appfuel\Validate\Error,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the errors ability to hold, display and retrieve errors for a single
 * field
 */
class ErrorTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Coordinator
	 */
	protected $error = null;

	/**
	 * The field these errors belong to
	 * @var string
	 */
	protected $field = null;

	/**
	 * First message added in the constructor
	 * @var string
	 */
	protected $firstMsg = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->field = "my-field";
		$this->firstMsg = "this is the first message";
		$this->error = new Error($this->field, $this->firstMsg);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->error);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Validate\ErrorInterface',
			$this->error
		);
		$this->assertInstanceOf('Countable',$this->error);
		$this->assertInstanceOf('Iterator',$this->error);
	}

	/**
	 * The field represents the field in the source that was validated and
	 * is only set by passing the string into the constructor
	 *
	 * @return null
	 */
	public function testGetField()
	{
		$this->assertEquals($this->field, $this->error->getField());

		/* 
		 * field can also be scalar values because this is also allowed
		 * in the coordinator and other subsystems
		 */
		$error = new Error(0, 'message for current field');
		$this->assertEquals(0, $error->getField());

		$error = new Error(101, 'message for current field');
		$this->assertEquals(101, $error->getField());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testSetFieldEmptyString()
	{
		$error = new Error('', 'some error message');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testSetFieldEmptyArray()
	{
		$error = new Error(array(1,2,3,4), 'some error message');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testSetFieldEmptyObj()
	{
		$error = new Error(new StdClass(), 'some error message');
	}

	/**
	 * Separator is used when using the object in the context of a string.
	 * Each message will be concatenated togather with the separator char.
	 *
	 * @return	string
	 */ 
	public function testGetSetSeparator()
	{
		/* default character is an space */
		$this->assertEquals(' ', $this->error->getSeparator());

		$char = ':';
		$this->assertSame(
			$this->error,
			$this->error->setSeparator($char),
			'must expose a fluent interface'
		);
		$this->assertEquals($char, $this->error->getSeparator());

		/* multiple chararacters are allowed */
		$char = ': :';
		$this->assertSame(
			$this->error,
			$this->error->setSeparator($char),
			'must expose a fluent interface'
		);
		$this->assertEquals($char, $this->error->getSeparator());

		/* empty strings  are allowed although no useful */
		$char = '';
		$this->assertSame(
			$this->error,
			$this->error->setSeparator($char),
			'must expose a fluent interface'
		);
		$this->assertEquals($char, $this->error->getSeparator());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testSetSeparatorBadSeparatorInt()
	{
		$this->error->setSeparator(12345);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testSetSeparatorBadSeparatorArray()
	{
		$this->error->setSeparator(array(1,2,3,4));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testSetSeparatorBadSeparatorObj()
	{
		$this->error->setSeparator(new StdClass());
	}

	/**
	 * The first message is always added when the object is created, all other
	 * message are added via add interface. This error implements both
	 * Coutable and Iterator interfaces meaning the following methods are
	 * available: count, key, current, next, rewind, valid.
	 *
	 * @return null
	 */
	public function testMessages()
	{
		$this->assertEquals(1, $this->error->count());
		
		$msg2 = 'second error message';
		$msg3 = 'third error message';
		$msg4 = 'fourth error message';
		$this->assertSame($this->error,$this->error->add($msg2));
		$this->assertEquals(2, $this->error->count());
		$this->assertSame($this->error,$this->error->add($msg3));
		$this->assertEquals(3, $this->error->count());
		$this->assertSame($this->error,$this->error->add($msg4));
		$this->assertEquals(4, $this->error->count());

		$this->assertTrue($this->error->valid());
		$this->assertEquals(0, $this->error->key());
		$this->assertEquals($this->firstMsg, $this->error->current());
		
		/* go to next message */
		$this->assertNull($this->error->next());
		$this->assertTrue($this->error->valid());
		$this->assertEquals(1, $this->error->key());
		$this->assertEquals($msg2, $this->error->current());
	
		/* go to next message */
		$this->assertNull($this->error->next());
		$this->assertTrue($this->error->valid());
		$this->assertEquals(2, $this->error->key());
		$this->assertEquals($msg3, $this->error->current());
		
		/* go to next message */
		$this->assertNull($this->error->next());
		$this->assertTrue($this->error->valid());
		$this->assertEquals(3, $this->error->key());
		$this->assertEquals($msg4, $this->error->current());
			
		/* try to go past the end */
		$this->assertNull($this->error->next());
		$this->assertFalse($this->error->valid());
		$this->assertNull($this->error->key());
		$this->assertFalse($this->error->current());

		/* go back to the first element */
		$this->assertNull($this->error->rewind());	
		$this->assertTrue($this->error->valid());
		$this->assertEquals(0, $this->error->key());
		$this->assertEquals($this->firstMsg, $this->error->current());
	}

	/**
	 * Return an array of all the errors added for this field
	 *
	 * @return null
	 */
	public function testGetErrors()
	{
		$expected = array($this->firstMsg);
		$this->assertEquals($expected, $this->error->getErrors());

		$msg2 = 'second error message';
		$msg3 = 'third error message';
		$msg4 = 'fourth error message';
		$this->error->add($msg2)
					->add($msg3)
					->add($msg4);

		$expected = array($this->firstMsg, $msg2, $msg3, $msg4);
		$this->assertEquals($expected, $this->error->getErrors());	
	}

	public function testToString()
	{
		$msg2 = 'second error message';
		$this->error->add($msg2);

		$sep = $this->error->getSeparator();
		$expected = "{$this->field}{$sep}{$this->firstMsg}{$sep}{$msg2}";
		$this->expectOutputString($expected);

		echo $this->error;
	}
}
