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

use Appfuel\App\Context\ContextUri,
	Appfuel\App\Context\AppContext,
	Appfuel\App\Context\ContextInput,
	TestFuel\TestCase\ControllerTestCase,
	Appfuel\Domain\Action\ActionModel,
	Appfuel\Domain\Operation\OperationalRoute,
	Appfuel\Framework\Action\ControllerNamespace;

/**
 * A context is a container that holds all the information necessary to handle
 * the operation the user has indicated they want to execute. The context is
 * create by the AppManager given to the front controller passed into every
 * intercept filter, though the action controller, back to the front controller
 * and finally into the render engine. 
 */
class AppContextTest extends ControllerTestCase
{
    /**
     * System under test
     * @var Context
     */
    protected $context = null;

	/**
	 * Uri object used to find the operational route
	 * @var ContextUriInterface
	 */
	protected $uri = null;

	/**
	 * Context Input
	 * @var ContextInputInterface
	 */
	protected $input = null;

	/**
	 * @var OperationalRouteInterface
	 */
	protected $opRoute = null;

	/**
	 * @var ActionModel
	 */
	protected $action = null;

    /**
     * @return null
     */
    public function setUp()
    {
		$this->uri = new ContextUri('my-route/qx/param1/value1');
		$this->input   = new ContextInput('get');
		$this->opRoute = new OperationalRoute();
		$this->action  = new ActionModel();

		$this->action->setRootNamespace('Appfuel\App\Action')
					 ->setRelativeNamespace('Error\Handler\Default')
					 ->_markClean();
	
		$filters = array(
			'pre'	=> array('filter1', 'filter2'),
			'post'	=> array('filter3', 'filter4')
		);

		$this->opRoute->setAccessPolicy('public')
					  ->setAction($this->action)
					  ->setFilters($filters)
					  ->_markClean();

		$this->context = new AppContext(
			$this->uri,
			$this->opRoute,
			$this->input
		);
    }

    /**
     * @return null
     */
    public function tearDown()
    {
		$this->input = null;
		$this->opRoute = null;
		$this->context = null;   
    }

	/**
	 * @return	null
	 */
	public function testHasInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\App\Context\ContextInterface',
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
		$this->assertSame($this->input, $this->context->getInput());

		$this->assertEquals(
			$this->uri->getRouteString(), 
			$this->context->getRouteString()
		);

		$this->assertEquals(
			$this->uri->getUriString(), 
			$this->context->getOriginalUriString()
		);

		$this->assertEquals(
			$this->uri->getParamString(), 
			$this->context->getUriParamString()
		);

		$action = $this->context->getAction();
		$this->assertInstanceOf(
			'Appfuel\Framework\Domain\Action\ActionDomainInterface',
			$action
		);		
		$this->assertSame($action, $this->opRoute->getAction());

		$this->assertEquals(
			$this->opRoute->getAccessPolicy(),
			$this->context->getAccessPolicy()
		);

		$this->assertEquals(
			array('filter1', 'filter2'),
			$this->context->getPreFilters()
		);

		$this->assertEquals(
			array('filter3', 'filter4'),
			$this->context->getPostFilters()
		);
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
