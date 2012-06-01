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
}
