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
namespace TestFuel\Test\App;

use Appfuel\App\Context,
	TestFuel\TestCase\ControllerTestCase;

/**
 * A context is a container that holds all the information necessary to handle
 * the operation the user has indicated they want to execute. The context is
 * create by the AppManager given to the front controller passed into every
 * intercept filter, though the action controller, back to the front controller
 * and finally into the render engine. 
 */
class ContextTest extends ControllerTestCase
{
    /**
     * System under test
     * @var Context
     */
    protected $context = null;

	/**
	 * Input Request
	 * @var Request
	 */
	protected $request = null;

	/**
	 * @var OperationInterface
	 */
	protected $operation = null;

    /**
     * @return null
     */
    public function setUp()
    {
		$this->request   = $this->getMockRequest();
		$this->operation = $this->getMockOperation();
		$this->context = new Context($this->request, $this->operation);
    }

    /**
     * @return null
     */
    public function tearDown()
    {
		$this->request = null;
		$this->operation = null;
		$this->context = null;   
    }

	/**
	 * @return	null
	 */
	public function testHasInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\App\ContextInterface',
			$this->context
		);
		$this->assertInstanceOf(
			'Appfuel\Framework\DataStructure\DictionaryInterface',
			$this->context
		);
	}

    /**
     * Both the request and the operation are immutable and can not be removed
	 *
     * @return null
     */
    public function testImmutableMembers()
    {
		$this->assertSame($this->request, $this->context->getRequest());
		$this->assertSame($this->operation, $this->context->getOperation());
    }

	/**
	 * @return	null
	 */
	public function testGetIsSetExceptionDefautValues()
	{
		/* default value having an exception is null */
		$this->assertNull($this->context->getException());
		$this->assertFalse($this->context->isException());

		$text = 'i am an error';
		$code = null;
		$prev = null;
		$this->assertSame(
			$this->context,
			$this->context->setException($text, $code, $prev),
			'must expose a fluent interface'
		);
		$this->assertTrue($this->context->isException());
		
		$exception = $this->context->getException();
		$this->assertInstanceOf('Appfuel\Framework\Exception',$exception);
		$this->assertEquals($text, $exception->getMessage());
		$this->assertEquals(0, $exception->getCode(), 'default code is 0');
		$this->assertNull($exception->getPrevious());
	}

	/**
	 * @depends	testGetIsSetExceptionDefautValues
	 * @return	null
	 */
	public function testSetExceptionWithCodeNoPrevious()
	{
		$text = 'i am an error';
		$code = 66;
		$prev = null;
		$this->assertSame(
			$this->context,
			$this->context->setException($text, $code, $prev),
			'must expose a fluent interface'
		);
		$this->assertTrue($this->context->isException());
	
		$exception = $this->context->getException();
		$this->assertInstanceOf('Appfuel\Framework\Exception',$exception);
		$this->assertEquals($text, $exception->getMessage());
		$this->assertEquals($code, $exception->getCode());
		$this->assertNull($exception->getPrevious());
	}

	/**
	 * @depends	testSetExceptionWithCodeNoPrevious
	 * @return	null
	 */
	public function testSetExceptionWithPreviousNoCode()
	{
		$text = 'i am an error';
		$code = null;
		$prev = new \Exception('previous exception', 2);
		$this->assertSame(
			$this->context,
			$this->context->setException($text, $code, $prev),
			'must expose a fluent interface'
		);
		$this->assertTrue($this->context->isException());
	
		$exception = $this->context->getException();
		$this->assertInstanceOf('Appfuel\Framework\Exception',$exception);
		$this->assertEquals($text, $exception->getMessage());
		$this->assertEquals(0, $exception->getCode(), 'default code is 0');
		$this->assertSame($prev, $exception->getPrevious());
	}

	/**
	 * @depends	testSetExceptionWithPreviousNoCode
	 * @return	null
	 */
	public function testSetExceptionWithCodeAndPrevious()
	{
		$text = 'i am an error';
		$code = 66;
		$prev = new \Exception('previous exception', 2);
		$this->assertSame(
			$this->context,
			$this->context->setException($text, $code, $prev),
			'must expose a fluent interface'
		);
		$this->assertTrue($this->context->isException());
	
		$exception = $this->context->getException();
		$this->assertInstanceOf('Appfuel\Framework\Exception',$exception);
		$this->assertEquals($text, $exception->getMessage());
		$this->assertEquals($code, $exception->getCode());
		$this->assertSame($prev, $exception->getPrevious());
	}

	/**
	 * @depends	testSetExceptionWithCodeAndPrevious
	 * @return	null
	 */
	public function testGetSetClearIsExceptioException()
	{
		$this->assertFalse($this->context->isException());
		
		$text = 'i am an error';
		$code = 66;
		$prev = new \Exception('previous exception', 2);
		$this->assertSame(
			$this->context,
			$this->context->setException($text, $code, $prev),
			'must expose a fluent interface'
		);
		$this->assertTrue($this->context->isException());
	
		$exception = $this->context->getException();
		$this->assertInstanceOf('Appfuel\Framework\Exception',$exception);

		$this->assertSame($this->context, $this->context->clearException());
		$this->assertFalse($this->context->isException());
		$this->assertNull($this->context->getException());	
	}



}
