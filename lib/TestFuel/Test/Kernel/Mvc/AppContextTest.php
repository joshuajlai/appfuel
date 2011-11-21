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
namespace TestFuel\Test\Kernel\Mvc;

use StdClass,
	Appfuel\Error\ErrorStack,
	Appfuel\Kernel\Mvc\AppInput,
	Appfuel\Kernel\Mvc\AppContext,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary;

/**
 * The request object was designed to service web,api and cli request
 */
class AppContextTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AppContext
	 */
	protected $context = null;

	/**
	 * First Paramter is the application input
	 * @var string
	 */
	protected $input = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->input = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
		$this->context = new AppContext($this->input);
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		$this->context = null;
	}

	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppContextInterface',
			$this->context
		);
	}

}
