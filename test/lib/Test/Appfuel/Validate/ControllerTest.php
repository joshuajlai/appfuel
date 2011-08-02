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
	Appfuel\Validate\Controller,
	Appfuel\Validate\Coordinator;

/**
 * Test the controller's ability to add rules or filters to fields and 
 * validate or sanitize those fields
 */
class ControllerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Controller
	 */
	protected $controller = null;

	/**
	 * Used to handle raw, clean and error data
	 * @var Coordinator
	 */
	protected $coord = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->coord = new Coordinator();
		$this->controller = new Controller($this->coord);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->controller);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Validate\ControllerInterface',
			$this->controller
		);
	}

}
