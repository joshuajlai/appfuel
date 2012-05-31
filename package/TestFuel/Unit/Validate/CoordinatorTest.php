<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Validate;

use StdClass,
	Appfuel\Validate\Coordinator,
	Appfuel\Validate\CoordinatorInterface,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary,
	Appfuel\Error\ErrorStackInterface;

/**
 * Test the coordinator's ability to move raw and clean data aswell as add 
 * error text
 */
class CoordinatorTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidKeys()
	{
		return array(
			array(true),
			array(false),
			array(new StdClass()),
			array(array(1,2,3,4))
		);
	}

	/**
	 * @return	string	
	 */
	public function getErrorStackInterface()
	{
		return 'Appfuel\Error\ErrorStackInterface';
	}

	/**
	 * @return	string
	 */
	public function getCoordinatorInterface()
	{
		return 'Appfuel\Validate\CoordinatorInterface';
	}

	/**
	 * @return	Coordinator
	 */
	public function createCoordinator(ErrorStackInterface $stack = null)
	{
		return new Coordinator($stack);	
	}

	/**
	 * @test
	 * @return Coordinator
	 */
	public function coordinatorInterface()
	{
		$coord = $this->createCoordinator();
		$this->assertInstanceOf($this->getCoordinatorInterface(), $coord);
		return $coord;
	}

	/**
	 * @test
	 * @return	Coordinator
	 */
	public function createCoordinatorWithErrorStack()
	{
		$stack = $this->getMock($this->getErrorStackInterface());
		$coord = $this->createCoordinator($stack);
		$this->assertInstanceOf($this->getCoordinatorInterface(), $coord);
		$this->assertSame($stack, $coord->getErrorStack());	

		return $coord;
	}

	/**
	 * @test
	 * @depends	coordinatorInterface
	 * @return null
	 */
	public function clean(Coordinator $coord)
	{
		/* default value is an empty array */
		$this->assertEquals(array(), $coord->getAllClean());

		$this->assertSame($coord, $coord->addClean('key', 'value'));

		$this->assertEquals('value', $coord->getClean('key'));
		$this->assertEquals(array('key'=>'value'), $coord->getAllClean());
			

		$this->assertSame($coord, $coord->addClean('foo', 'bar'));

		$expected = array(
			'key' => 'value',
			'foo' => 'bar'
		);
	
		$this->assertEquals($expected, $coord->getAllClean());
		$this->assertEquals('value', $coord->getClean('key'));
		$this->assertEquals('bar', $coord->getClean('foo'));
	
		/* key can be a scalar value */
		$this->assertSame($coord, $coord->addClean(123, 'value_123'));
	
		$expected[123] = 'value_123';
		$this->assertEquals($expected, $coord->getAllClean());
		$this->assertEquals('value', $coord->getClean('key'));
		$this->assertEquals('bar', $coord->getClean('foo'));
		$this->assertEquals('value_123', $coord->getClean(123));


		$this->assertSame($coord, $coord->clearClean());
		$this->assertEquals(array(), $coord->getAllClean());

		return $coord;	
	}

	/**
	 * @test
	 * @depends	clean
	 * @return	Coordinator
	 */
	public function getCleanUsingDefault(Coordinator $coord)
	{
		$coord->clearClean();
		$coord->addClean('foo', 'bar');

		/* default is ignored when key is found */	
		$this->assertEquals('bar', $coord->getClean('foo', 'my-value'));
			
		/* default is used  when key is not found */	
		$this->assertEquals('default', $coord->getClean('none', 'default'));

		/* default value returned when key is not found is null */
		$this->assertNull($coord->getClean('none'));
		
		/* invalid keys always return default value */
		$default = 'bad-key';
		$this->assertEquals($default, $coord->getClean(array(), $default));
		$this->assertEquals($default, $coord->getClean('', $default));
		$this->assertEquals($default, $coord->getClean(new StdClass,$default));

		$coord->clearClean();	
	}

	/**
	 * @test
	 * @param			mixed $key
	 * @dataProvider	provideInvalidKeys
	 * @return			null
	 */
	public function getCleanUsingInvalidKey($key)
	{
		$coord   = $this->createCoordinator();
		$default = 'some value';
		$this->assertEquals($default, $coord->getClean($key, $default));
	}

	/**
	 * @test
	 * @param			mixed $key
	 * @dataProvider	provideInvalidKeys
	 * @return null
	 */
	public function addCleanInvalidKey($key)
	{
		$msg = "can not add field to the clean source, invalid key";
		$this->setExpectedException('InvalidArgumentException', $msg);
		$coord = $this->createCoordinator();
		$coord->addClean($key, 'some-value');
	}

	/**
	 * You can set the raw source with setSource of by passing it into the
	 * the constructor
	 *
	 * @test
	 * @depends	coordinatorInterface
	 * @return	null
	 */
	public function source(Coordinator $coord)
	{
		/* 
		 * when nothing is passed into the constructor the default source is
		 * an empty array
		 */
		$this->assertEquals(array(), $coord->getSource());

		$source = array('name' => 'value');
		$this->assertSame($coord, $coord->setSource($source));
		$this->assertEquals($source, $coord->getSource());

		/* 
		 * can also set an empty array which has the effect of resetting the
		 * source
		 */
		$this->assertSame($coord, $coord->setSource(array()));
		$this->assertEquals(array(), $coord->getSource());

		$coord->setSource($source);
		$this->assertSame($coord, $coord->clearSource());
		$this->assertEquals(array(), $coord->getSource());

		return $coord;
	}

	/**
	 * @test
	 * @depends	source
	 * @return	Coordinator
	 */
	public function raw(Coordinator $coord)
	{
		$source = array(
			'foo' => 'bar',
			123   => 456,
			'baz' => 'blah'
		);
		$coord->setSource($source);
		$this->assertEquals('bar', $coord->getRaw('foo'));
		$this->assertEquals(456, $coord->getRaw(123));
		$this->assertEquals('blah', $coord->getRaw('baz'));

		$coord->clearSource();

		return $coord;
	}

	/**
	 * @test
	 * @depends	source
	 * @return	Coordinator
	 */
	public function fieldNotFound(Coordinator $coord)
	{
		$token = CoordinatorInterface::FIELD_NOT_FOUND;
		$this->assertEquals($token, $coord->getFieldNotFoundToken());

		return $coord;
	}

	/**
	 * @test
	 * @depends	fieldNotFound
	 * @return	Coordinator
	 */
	public function rawFieldNotFound(Coordinator $coord)
	{
		$coord->clearSource();

		$token = $coord->getFieldNotFoundToken();
		$this->assertEquals($token, $coord->getRaw('no-field'));
		return $coord;
	}

	/**
	 * @test
	 * @param			mixed $key
	 * @dataProvider	provideInvalidKeys
	 * @return			null
	 */
	public function getRawWithInvalidKey($key)
	{
		$coord = $this->createCoordinator();
		$token = $coord->getFieldNotFoundToken(); 
		$this->assertEquals($token, $coord->getRaw($key));
	}

	/**
	 * @test
	 * @depends	coordinatorInterface
	 * @return null
	 */
	public function errorStack(Coordinator $coord)
	{
		$stack = $coord->getErrorStack();
		$stackInterface = $this->getErrorStackInterface();
		$this->assertInstanceOf($stackInterface, $stack);
		$this->assertFalse($stack->isError());
		$this->assertFalse($coord->isError());

		$this->assertSame($coord, $coord->addError('my error'));
		$this->assertTrue($coord->isError());
		$this->assertTrue($stack->isError());

		$error = $stack->getLastError();
		$this->assertInstanceof('Appfuel\Error\ErrorItem', $error);

		$this->assertEquals(500, $error->getCode());
		$this->assertEquals('my error', $error->getMessage());
		$this->assertSame($coord, $coord->addError('other error', 404));

		$error = $stack->getLastError();
		$this->assertInstanceof('Appfuel\Error\ErrorItem', $error);
		$this->assertEquals(404, $error->getCode());
		$this->assertEquals('other error', $error->getMessage());
		
		$this->assertSame($coord, $coord->clearErrors());
		$this->assertFalse($coord->isError());
		$this->assertFalse($stack->isError());
				
		return $coord;
	}

	/**
	 * Error are added to the coordinator associated by the field that caused 
	 * them.
	 * 
	 * @depends		testInitialStateOfErrors
	 * @return null
	 */
	public function estAddErrorManyErrorsToSingleField()
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
	public function estAddErrorMultipleFields()
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
	public function estAddErrorNumericField()
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
	public function estAddErrorBadFieldEmptyString()
	{
		$this->coord->addError('', 'this is message');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function estAddErrorBadFieldArray()
	{
		$this->coord->addError(array(1,3,4), 'this is message');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function estAddErrorBadFieldObject()
	{
		$this->coord->addError(new StdClass(), 'this is message');
	}
}
