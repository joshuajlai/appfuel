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
	Appfuel\Validate\Coordinator,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary;

/**
 * Test the coordinator's ability to move raw and clean data aswell as add error text
 */
class CoordinatorTest extends BaseTestCase
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
			'Appfuel\Validate\CoordinatorInterface',
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
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testAddCleanBadKeyEmptyString()
	{
		$this->coord->addClean('', 'some-value');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testAddCleanBadKeyArray()
	{
		$this->coord->addClean(array(1,2,3), 'some-value');
	}

	/**
	 * @expectedException	InvalidArgumentException
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

	/**
	 * @return null
	 */
	public function testInitialStateOfErrors()
	{
		$this->assertFalse($this->coord->isError());
		$this->assertEquals(array(), $this->coord->getErrors());
		$this->assertFalse($this->coord->isFieldError('key-does-not-exist'));
		$this->assertNull($this->coord->getError('key-does-not-exist'));
	}

	/**
	 * Error are added to the coordinator associated by the field that caused 
	 * them.
	 * 
	 * @depends		testInitialStateOfErrors
	 * @return null
	 */
	public function testAddErrorManyErrorsToSingleField()
	{
		/* when a field does not exist for an error a null is returned */
		$field = 'my-field';
		$msg1  = 'my-field error message';
		$this->assertNull($this->coord->getError($field));
		$this->assertFalse($this->coord->isFieldError($field));
		$this->assertSame(
			$this->coord,
			$this->coord->addError($field, $msg1),
			'must expose a fluent interface'
		);

		/* prove the error object was created and field, msg were added */
		$this->assertTrue($this->coord->isError());
		$this->assertTrue($this->coord->isFieldError($field));

		$error = $this->coord->getError($field);
		$this->assertInstanceOf('Appfuel\Validate\Error', $error);
		$this->assertEquals($field, $error->getField());
		$this->assertEquals($msg1, $error->current());

		/* add second error to the same field */
		$msg2 = "second error message";
		$this->assertSame(
			$this->coord,
			$this->coord->addError($field, $msg2),
			'must expose a fluent interface'
		);

		/* make sure checks still return the same values */
		$this->assertTrue($this->coord->isError());
		$this->assertTrue($this->coord->isFieldError($field));

		/* prove we have a reference to the same object just created */
		$this->assertSame($error, $this->coord->getError($field));
		$this->assertEquals($msg1, $error->current());

		$error->next();
		$this->assertEquals($msg2, $error->current());

		/* duplicate messages are not checked against */
		$this->assertSame(
			$this->coord,
			$this->coord->addError($field, $msg1),
			'must expose a fluent interface'
		);

		$expected = array($msg1, $msg2, $msg1);
		$this->assertEquals($expected, $error->getErrors());

		/* test that getErrors returns an associative array of
		 * field => Error
		 */
		$expected = array($field => $error);
		$this->assertEquals($expected, $this->coord->getErrors());
	}

	/**
	 * Tests adding errors to multiple fields as well as clearing all the 
	 * errors 
	 *
	 * @depends testAddErrorManyErrorsToSingleField
	 * @return null
	 */
	public function testAddErrorMultipleFields()
	{
		$field1		  = 'field_1';
		$field_msg1   = 'error field 1 msg 1';
		$field_msg2	  = 'error field 1 msg 2';
		$field2		  = 'field_2';
		$field2_msg1  = 'error field 2 msg 1';
		$this->coord->addError($field1, $field_msg1) 
					->addError($field1, $field_msg2)
					->addError($field2, $field2_msg1);

		$this->assertTrue($this->coord->isError());
		$error1 = $this->coord->getError($field1);
		$error2 = $this->coord->getError($field2);

		$this->assertInstanceOf('Appfuel\Validate\Error', $error1);
		$this->assertInstanceOf('Appfuel\Validate\Error', $error2);

		$this->assertEquals($field1, $error1->getField());
		$this->assertEquals($field2, $error2->getField());

		$this->assertEquals($field_msg1, $error1->current());
		$error1->next();
		$this->assertEquals($field_msg2, $error1->current());

		$this->assertEquals($field2_msg1, $error2->current());

		$expected = array($field1 => $error1, $field2 => $error2);
		$this->assertEquals($expected, $this->coord->getErrors());

		$this->assertSame(
			$this->coord,
			$this->coord->clearErrors(),
			'must expose a fluent interface'
		);
		$this->assertEquals(array(), $this->coord->getErrors());
		$this->assertFalse($this->coord->isError());
	}

	/**
	 * @depends testAddErrorManyErrorsToSingleField
	 * @return null
	 */
	public function testAddErrorNumericField()
	{
		$field = 123;
		$msg   = 'this is an error message';
		
		$this->assertSame(
			$this->coord,
			$this->coord->addError($field, $msg),
			'must expose a fluent interface'
		);
		$this->assertTrue($this->coord->isError());
		$error = $this->coord->getError(123);
		$this->assertInstanceOf('Appfuel\Validate\Error', $error);
		$this->assertEquals(123, $error->getField());
		$this->assertEquals($msg, $error->current());
		$this->assertEquals(
			array(123 => $error),
			$this->coord->getErrors()
		);	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testAddErrorBadFieldEmptyString()
	{
		$this->coord->addError('', 'this is message');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testAddErrorBadFieldArray()
	{
		$this->coord->addError(array(1,3,4), 'this is message');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testAddErrorBadFieldObject()
	{
		$this->coord->addError(new StdClass(), 'this is message');
	}



}
