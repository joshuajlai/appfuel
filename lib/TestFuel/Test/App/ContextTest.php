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
     *
     * @return null
     */
    public function testImmutableMembers()
    {
		$this->assertSame($this->request, $this->context->getRequest());
		$this->assertSame($this->operation, $this->context->getOperation());
    }
}
