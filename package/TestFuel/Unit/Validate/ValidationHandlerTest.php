<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Unit\Validate;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Validate\FieldSpec,
	Appfuel\Validate\FieldValidator,
	Appfuel\Validate\Filter\FilterSpec,
	Appfuel\Validate\Filter\IntFilter,
	Appfuel\Validate\Filter\BoolFilter,
	Appfuel\Validate\Coordinator,
	Appfuel\Validate\CoordinatorInterface,
	Appfuel\Validate\ValidationHandler,
	Appfuel\Validate\ValidationFactory;

class ValidationHandlerTest extends BaseTestCase
{
    /**
     * @return  null
     */
    public function setUp()
    {
		parent::setUp();
		$this->backupValidationMap();
        ValidationFactory::clear();
    }

    /**
     * @return  null
     */
    public function tearDown()
    {
		parent::tearDown();
        ValidationFactory::clear();
		$this->restoreValidationMap();
    }

	/**
	 * @return string
	 */
	public function getCoordinatorInterface()
	{
		return 'Appfuel\Validate\CoordinatorInterface';
	}

	/**
	 * @return string
	 */
	public function getValidationHandlerInterface()
	{
		return 'Appfuel\Validate\ValidationHandlerInterface';
	}

	/**
	 * @return string
	 */
	public function getValidatorInterface()
	{
		return 'Appfuel\Validate\ValidatorInterface';
	}

	/**
	 * @param	array	$data
	 * @return	ValidationHandler
	 */
	public function createValidationHandler(CoordinatorInterface $coord = null)
	{
		return new ValidationHandler($coord);
	}

	/**
	 * @test
	 * @return	ValidationHandlerInterface
	 */
	public function handlerInterface()
	{
		$handler = $this->createValidationHandler();
		$interface = $this->getValidationHandlerInterface();
		$this->assertInstanceOf($interface, $handler);

		return $handler;
	}

	/**
	 * @test
	 * @depends	handlerInterface
	 * @return	ValidationHandler
	 */
	public function coordinator(ValidationHandler $handler)
	{
		$class = 'Appfuel\Validate\Coordinator';
		$this->assertInstanceOf($class, $handler->getCoordinator());

		$coord = $this->getMock($this->getCoordinatorInterface());
		$this->assertSame($handler, $handler->setCoordinator($coord));
		$this->assertSame($coord, $handler->getCoordinator());

		/* put back the original coordinator for future tests */
		$coord = new Coordinator();
		$handler->setCoordinator($coord);

		return $handler;
	}

	/**
	 * @test
	 * @depends	coordinator
	 * @return	null
	 */
	public function constructorWithCoordinator()
	{
		$coord   = $this->getMock($this->getCoordinatorInterface());
		$handler = $this->createValidationHandler($coord);
		
		$handlerInterface = $this->getValidationHandlerInterface();
		$this->assertInstanceOf($handlerInterface, $handler);
		$this->assertSame($coord, $handler->getCoordinator());
	}

	/**
	 * @test
	 * @depends	handlerInterface
	 * @return	ValidationHandler
	 */
	public function validators(ValidationHandler $handler)
	{
		$this->assertEquals(array(), $handler->getValidators());

		$interface  = $this->getValidatorInterface();
		$validator1 = $this->getMock($interface);
		$this->assertSame($handler, $handler->addValidator($validator1));

		$expected = array($validator1);
		$this->assertEquals($expected, $handler->getValidators());

		$validator2 = $this->getMock($interface);
		$this->assertSame($handler, $handler->addValidator($validator2));

		$expected[] = $validator2;
		$this->assertEquals($expected, $handler->getValidators());
		
		$validator3 = $this->getMock($interface);
		$this->assertSame($handler, $handler->addValidator($validator3));

		$expected[] = $validator3;
		$this->assertEquals($expected, $handler->getValidators());

		$this->assertSame($handler, $handler->clearValidators());	
		$this->assertEquals(array(), $handler->getValidators());
	}

	/**
	 * @test
	 * @depends	handlerInterface
	 * @return	ValidationHandler
	 */
	public function errors(ValidationHandler $handler)
	{
		$this->assertFalse($handler->isError());
		$coord = $handler->getCoordinator();
		$this->assertFalse($coord->isError());

		$coord->addError('my error');
		$this->assertTrue($handler->isError());
		$this->assertTrue($coord->isError());

		$stack = $handler->getErrorStack();
		$this->assertSame($stack, $coord->getErrorStack());

		$this->assertSame($handler, $handler->clearErrors());
		$this->assertFalse($handler->isError());
		$this->assertFalse($coord->isError());

		return $handler;
	}

	/**
	 * @test
	 * @depends	handlerInterface
	 * @return	ValidationHandler
	 */
	public function cleanData(ValidationHandler $handler)
	{
		$this->assertEquals(array(), $handler->getAllClean());
		
		$coord = $handler->getCoordinator();
		$coord->addClean('foo', 'bar');
		
		$expected = array('foo' => 'bar');
		$this->assertEquals($expected, $handler->getAllClean());

		$this->assertEquals('bar', $handler->getClean('foo'));
		$this->assertNull($handler->getClean('fiz'));
		$this->assertEquals('default', $handler->getClean('fiz', 'default'));

		$this->assertSame($handler, $handler->clearClean());
		$this->assertEquals(array(), $handler->getAllClean());
		$this->assertEquals(array(), $coord->getAllClean());
	
		return $handler;	
	}

	/**
	 * @test
	 * @depends	handlerInterface
	 * @return	ValidationHandler
	 */
	public function isSatisfiedByPassWithMock(ValidationHandler $handler)
	{
		$raw = array('field-a' => 'value-a');
		$validator1 = $this->getMock($this->getValidatorInterface());
		$validator1->expects($this->once())
				   ->method('isValid')
				   ->will($this->returnValue(true));

		$validator2 = $this->getMock($this->getValidatorInterface());
		$validator2->expects($this->once())
				   ->method('isValid')
				   ->will($this->returnValue(true));

		$handler->addValidator($validator1)
				->addValidator($validator2);

		$this->assertTrue($handler->isSatisfiedBy($raw));
		
		$handler->clearValidators();
		$coord = $handler->getCoordinator();
		$coord->clear();
		$handler->clearValidators();
		return $handler;	
	}

	/**
	 * @test
	 * @depends	handlerInterface
	 * @return	ValidationHandler
	 */
	public function isSatisfiedByFailWithMock(ValidationHandler $handler)
	{
		$raw = array('field-a' => 'value-a');
		$validator1 = $this->getMock($this->getValidatorInterface());
		$validator1->expects($this->once())
				   ->method('isValid')
				   ->will($this->returnValue(true));

		$validator2 = $this->getMock($this->getValidatorInterface());
		$validator2->expects($this->once())
				   ->method('isValid')
				   ->will($this->returnValue(false));

		$handler->addValidator($validator1)
				->addValidator($validator2);

		$this->assertFalse($handler->isSatisfiedBy($raw));
		$handler->clearValidators();


		$coord = $handler->getCoordinator();	
		$coord->clear();

		return $handler;	
	}

	/**
	 * @test
	 * @depends	handlerInterface
	 * @return	ValidationHandler
	 */
	public function isSatisfiedByPassReal(ValidationHandler $handler)
	{	
		$spec = array(
			'name' => 'int',
			'options' => array('min' => 100, 'max' => 105),
			'error'   => 'failed int range'
		);
		$spec = new FilterSpec($spec);

		$intFilter  = new IntFilter();
		$intFilter->loadSpec($spec);

		$spec = array(
			'name'  => 'bool',
			'error' => 'not a valid bool'
		);
		$spec = new FilterSpec($spec);

		$boolFilter = new BoolFilter();
		$boolFilter->loadSpec($spec);

		$validator1 = new FieldValidator();
		$validator1->addField('my-int');
		$validator1->addFilter($intFilter);

		$validator2 = new FieldValidator();
		$validator2->addField('my-bool');
		$validator2->addFilter($boolFilter);

		$handler->addValidator($validator1)
				->addValidator($validator2);
		
		$raw = array(
			'my-int'  => 103,
			'my-bool' => 'true'
		);

		$this->assertTrue($handler->isSatisfiedBy($raw));
		$this->assertFalse($handler->isError());
		$this->assertEquals(103, $handler->getClean('my-int'));
		$this->assertEquals(true, $handler->getClean('my-bool'));
	
		$coord = $handler->getCoordinator();
		$coord->clear();
		$handler->clearValidators();

	}

	/**
	 * @test
	 * @depends	handlerInterface
	 * @return	ValidationHandler
	 */
	public function isSatisfiedByFailReal(ValidationHandler $handler)
	{	
		$spec = array(
			'name' => 'int',
			'options' => array('min' => 100, 'max' => 105),
			'error'   => 'failed int range'
		);
		$spec = new FilterSpec($spec);

		$intFilter  = new IntFilter();
		$intFilter->loadSpec($spec);

		$spec = array(
			'name'  => 'bool',
			'error' => 'not a valid bool'
		);
		$spec = new FilterSpec($spec);

		$boolFilter = new BoolFilter();
		$boolFilter->loadSpec($spec);

		$validator1 = new FieldValidator();
		$validator1->addField('my-int');
		$validator1->addFilter($intFilter);

		$validator2 = new FieldValidator();
		$validator2->addField('my-bool');
		$validator2->addFilter($boolFilter);

		$handler->addValidator($validator1)
				->addValidator($validator2);
		
		$raw = array(
			'my-int'  => 106,
			'my-bool' => 'true'
		);

		$this->assertFalse($handler->isSatisfiedBy($raw));
		$this->assertTrue($handler->isError());
		$this->assertEquals(true, $handler->getClean('my-bool'));

		$stack = $handler->getErrorStack();
		$message = $stack->getMessage();
		$this->assertEquals($message, 'failed int range');
		
		$coord = $handler->getCoordinator();
		$coord->clear();
		$handler->clearValidators();
	}

}
