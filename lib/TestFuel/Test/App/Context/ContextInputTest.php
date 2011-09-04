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
namespace TestFuel\Test\App\Context;

use Appfuel\App\Context\ContextInput,
	TestFuel\TestCase\BaseTestCase;

/**
 * The request object was designed to service web,api and cli request
 */
class ContextInputTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Request
	 */
	protected $input = null;

	/**
	 * First parameters of the constructor, the request method used for the
	 * user input
	 * @var string
	 */
	protected $method = null;

	/**
	 * Second parameter of the contructor, list of all input params by param
	 * type
	 * @var array
	 */	
	protected $params = array();

	/**
	 * Backup super global so we can use them to inject data into the request
	 * 
	 * @return null
	 */
	public function setUp()
	{
		$this->method = 'get';
		$this->params = array(
			'get'	=> array('param1' => 'value1', 'param2' => 12344),
			'post'  => array('param3' => array(1,2,3), 'param3' => 'test')
		);

		$this->input = new ContextInput($this->method, $this->params);
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		$this->input = null;
	}

	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\App\Context\ContextInputInterface',
			$this->input
		);
	}
}
