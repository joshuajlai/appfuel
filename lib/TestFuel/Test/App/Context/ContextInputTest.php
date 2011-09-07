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
	 * Add all types of params so we can test the getters for all of them with
	 * no extra setup
	 * 
	 * @return null
	 */
	public function setUp()
	{
		$this->method = 'get';
		$this->params = array(
			'get'	 => array('param1' => 'value1', 'param2' => 12344),
			'post'   => array('param3' => array(1,2,3), 'param3' => 'test'),
			'files'  => array('param4' => 'value4'),
			'cookie' => array('param5' => 'value5'),
			'argv'   => array('param6' => 'value6')
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

	/**
	 * Use getAll to return all the input paramaters which should all be 
	 * empty. This test shows you can easily have an empty input object,
	 * you only have to supply the input method (get|post|cli)
	 *
	 * @return	null
	 */
	public function testConstructorNoParams()
	{
		$input = new ContextInput('get');
		
		$expected = array(
			'get'	 => array(), 
			'post'   => array(),
			'files'	 => array(),
			'cookie' => array(),
			'argv'   => array()
		);
		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * The method of the request was set to get in the constructor
	 *
	 * @depends	testConstructorNoParams
	 * @return	null
	 */
	public function testIsGetPostCli()
	{
		$this->assertTrue($this->input->isGet());
		$this->assertFalse($this->input->isPost());
		$this->assertFalse($this->input->isCli());
		$this->assertEquals('get', $this->input->getMethod());

		$input = new ContextInput('post');
		$this->assertTrue($input->isPost());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isCli());
		$this->assertEquals('post', $input->getMethod());
		
		$input = new ContextInput('cli');
		$this->assertTrue($input->isCli());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertEquals('cli', $input->getMethod());

		/* prove not case sensitive */
		$input = new ContextInput('GET');
		$this->assertTrue($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isCli());
		$this->assertEquals('get', $input->getMethod());
	
		$input = new ContextInput('POST');
		$this->assertTrue($input->isPost());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isCli());
		$this->assertEquals('post', $input->getMethod());
	
		$input = new ContextInput('CLI');
		$this->assertTrue($input->isCli());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertEquals('cli', $input->getMethod());
	}
}
