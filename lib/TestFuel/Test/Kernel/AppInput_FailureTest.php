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
namespace TestFuel\Test\Kernel;

use StdClass,
	Appfuel\Kernel\AppInput,
	TestFuel\TestCase\BaseTestCase;

/**
 * The request object was designed to service web,api and cli request
 */
class AppInput_FailureTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Request
	 */
	protected $input = null;

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
		$this->params = array(
			'get'	 => array('param1' => 'value1', 'param2' => 12344),
			'post'   => array('param3' => array(1,2,3), 'param3' => 'test'),
			'files'  => array('param4' => 'value4'),
			'cookie' => array('param5' => 'value5'),
			'argv'   => array('param6' => 'value6')
		);

		$this->input = new AppInput('get', $this->params);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->input = null;
	}

	/**
	 * @return array
	 */
	public function provideInvalidParams()
	{
		return array(
			array(array('get' => 'should be an array')),
			array(array('post'   => 'should be an array')),
			array(array('files'  => 'should be an array')),
			array(array('cookie' => 'should be an array')),
			array(array('argv'   => 'should be an array')),
		);
	}

	/**
	 * @return array
	 */
	public function provideInvalidParamsKeys()
	{
		/* param has an invalid key */
		$param = array('' => 'value');
		return array(
			array(array('get' => $param)),
			array(array('post'   => $param)),
			array(array('files'  => $param)),
			array(array('cookie' => $param)),
			array(array('argv'   => $param)),
		);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorMethodEmptyString()
	{
		$input = new AppInput('');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorMethodInt()
	{
		$input = new AppInput(12345);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorMethodArray()
	{
		$input = new AppInput(array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorMethodObject()
	{
		$input = new AppInput(new StdClass());
	}

	/**
	 * Any string thats not cli, get, or post
	 *
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorMethodNotCliGetPost()
	{
		$input = new AppInput('getter');
	}

	/**
	 * @dataProvider		provideInvalidParams
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorParams(array $params)
	{
		$input = new AppInput('get', $params);
	}

	/**
	 * @dataProvider		 provideInvalidParamsKeys
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorParamsKeys(array $params)
	{
		$input = new AppInput('get', $params);
	}
}
