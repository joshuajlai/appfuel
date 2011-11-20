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
	Appfuel\Kernel\Mvc\AppInput,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary;

/**
 * The request object was designed to service web,api and cli request
 */
class AppInputTest extends BaseTestCase
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
			'post'   => array('param3' => array(1,2,3), 'param4' => 'value4'),
			'files'  => array('param5' => 'value5'),
			'cookie' => array('param6' => 'value6'),
			'argv'   => array('param7' => 'value7')
		);

		$this->input = new AppInput($this->method, $this->params);
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
			'Appfuel\Kernel\Mvc\AppInputInterface',
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
		$input = new AppInput('get');
		
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

		$input = new AppInput('post');
		$this->assertTrue($input->isPost());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isCli());
		$this->assertEquals('post', $input->getMethod());
		
		$input = new AppInput('cli');
		$this->assertTrue($input->isCli());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertEquals('cli', $input->getMethod());

		/* prove not case sensitive */
		$input = new AppInput('GET');
		$this->assertTrue($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isCli());
		$this->assertEquals('get', $input->getMethod());
	
		$input = new AppInput('POST');
		$this->assertTrue($input->isPost());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isCli());
		$this->assertEquals('post', $input->getMethod());
	
		$input = new AppInput('CLI');
		$this->assertTrue($input->isCli());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertEquals('cli', $input->getMethod());
	}

	/**
	 * The parameter type is the name of the array that holds all the input
	 * parameters for that type. By default get, post, files, cookie and argv
	 * exist, and you can define custom types by adding it to params in the
	 * AppInput constructor.
	 *
	 * @return	null
	 */
	public function testIsValidParamType()
	{
		/* test defaults exist */
		$this->assertTrue($this->input->isValidParamType('get'));
		$this->assertTrue($this->input->isValidParamType('post'));
		$this->assertTrue($this->input->isValidParamType('files'));
		$this->assertTrue($this->input->isValidParamType('cookie'));
		$this->assertTrue($this->input->isValidParamType('argv'));
		$this->assertFalse($this->input->isValidParamType('does-not-exist'));

		$params = array('custom-type' => array('param1' => 'value1'));
		$input = new AppInput('get', $params);

		/* added even though we did not supply them. They always exist */
		$this->assertTrue($input->isValidParamType('get'));
		$this->assertTrue($input->isValidParamType('post'));
		$this->assertTrue($input->isValidParamType('files'));
		$this->assertTrue($input->isValidParamType('cookie'));
		$this->assertTrue($input->isValidParamType('argv'));

		$this->assertTrue($input->isValidParamType('custom-type'));
		$this->assertFalse($input->isValidParamType('does-not-exist'));	

		$this->assertFalse($this->input->isValidParamType(array(1,2,3)));
		$this->assertFalse($this->input->isValidParamType(new StdClass()));
		$this->assertFalse($this->input->isValidParamType(''));
	}

	/**
	 * This is how you get input parameters from the AppInput. This test
	 * will retrieve all the known values added to the input during setup.
	 *
	 * @return	null
	 */
	public function testGet()
	{
		$this->assertEquals(
			$this->params['get']['param1'], 
			$this->input->get('get', 'param1')
		);
		/* prove first param is not case sensitive */
		$this->assertEquals(
			$this->params['get']['param1'], 
			$this->input->get('GET', 'param1')
		);

		$this->assertEquals(
			$this->params['get']['param2'], 
			$this->input->get('get', 'param2')
		);
	
		$this->assertEquals(
			$this->params['post']['param3'], 
			$this->input->get('post', 'param3')
		);

		$this->assertEquals(
			$this->params['post']['param4'], 
			$this->input->get('post', 'param4')
		);

		$this->assertEquals(
			$this->params['files']['param5'], 
			$this->input->get('files', 'param5')
		);

		$this->assertEquals(
			$this->params['cookie']['param6'], 
			$this->input->get('cookie', 'param6')
		);

		$this->assertEquals(
			$this->params['argv']['param7'], 
			$this->input->get('argv', 'param7')
		);	
	}

	/**
	 * Will return null when type does not exist or is not a string
	 *
	 * @depends	testGet
	 * @return	null
	 */
	public function testGetTypeDoesNotExist()
	{
		$this->assertFalse($this->input->isValidParamType('does-not-exist'));
		
		/* the default return value when not found is null */
		$this->assertNull($this->input->get('does-not-exist', 'param1'));
		$this->assertEquals(
			'custom default',
			$this->input->get('does-not-exist', 'param1', 'custom default'),
			'you can supply your own custom default'
		);

		
		$this->assertNull($this->input->get(array(1,2,3), 'param1'));
		$this->assertNull($this->input->get(new StdClass(), 'param1'));
		$this->assertNull($this->input->get('', 'param1'));
	}

	/**
	 * @depends	testGet
	 * @return	null
	 */
	public function testGetKeyIsNotScalarOrEmpty()
	{
		/* the default return value when not found is null */
		$this->assertNull($this->input->get('get', array(1,2,3)));
		$this->assertNull($this->input->get('get', new StdClass()));
		$this->assertNull($this->input->get('get', ''));
	}

	/**
	 * Trying to get a parameter that does not exist returns null by default
	 * but the third parameter always you to define that default value
	 *
	 * @depends	testGet
	 */
	public function testGetParamDoesNotExist()
	{
		$this->assertNull($this->input->get('get', 'param99'));
	
		$default = 'my-default';
		$this->assertEquals(
			$default, 
			$this->input->get('get', 'param99', $default)
		);

		$default = array(1,2,3);
		$this->assertEquals(
			$default, 
			$this->input->get('get', 'param99', $default)
		);

		$default = new StdClass();
		$this->assertEquals(
			$default, 
			$this->input->get('get', 'param99', $default)
		);

		$default = 123;
		$this->assertEquals(
			$default, 
			$this->input->get('get', 'param99', $default)
		);

		$default = 123.123;
		$this->assertEquals(
			$default, 
			$this->input->get('get', 'param99', $default)
		);
	}

	/**
	 * Collect will gather parameters for a type and return them
	 * 
	 * @depends	testGet
	 * @return	null
	 */
	public function testCollectReturnsDictionary()
	{
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 'value2',
				'param3' => 'value3',
				'param4' => 'value4'
			)
		);

		$input  = new AppInput('get', $params);
		$result = $input->collect('get', array('param1', 'param4'));
		
		$expected = array(
			'param1' => 'value1',
			'param4' => 'value4'
		);
		$expected = new Dictionary($expected);
		$this->assertEquals($expected, $result);

		$result = $input->collect('get', array('param3', 'param2'));

		/*
		 * note that it is returned in the order requested 
		 */
		$expected = array(
			'param3' => 'value3',
			'param2' => 'value2'
		);
		$expected = new Dictionary($expected);
		$this->assertEquals($expected, $result);

		/* keys that don't exist will not be returned */
		$result = $input->collect('get', array('param3','not-found','param2'));
		$this->assertEquals($expected, $result);

		$result = $input->collect('get', array('nope', 'nada', 'nilch'), true);
		$this->assertEquals(array(), $result);

		return $params;
	}

	/**
	 * When the third parameter is true the collection will be an array
	 *
	 * @depends	testCollectReturnsDictionary
	 * @return	null
	 */
	public function testCollectReturnsArray(array $params)
	{
		$input  = new AppInput('get', $params);
		$result = $input->collect('get', array('param1', 'param4'), true);
		
		$expected = array(
			'param1' => 'value1',
			'param4' => 'value4'
		);
		$this->assertEquals($expected, $result);

		$result = $input->collect('get', array('param3', 'param2'), true);

		/*
		 * note that it is returned in the order requested 
		 */
		$expected = array(
			'param3' => 'value3',
			'param2' => 'value2'
		);
		$this->assertEquals($expected, $result);

		/* keys that don't exist will not be returned */
		$result = $input->collect(
			'get', 
			array('param3','not-found','param2'),
			true
		);
		$this->assertEquals($expected, $result);

		$result = $input->collect('get', array('nope', 'nada', 'nilch'), true);
		$this->assertEquals(array(), $result);
	}

	/**
	 * GetAll will return all of one parameter type like get or post when that
	 * type is given as a parameter. When no parameter is given it will return
	 * all parameter types
	 *
	 * @return	null
	 */
	public function testGetAll()
	{
		$this->assertEquals($this->params, $this->input->getAll());
		$this->assertEquals($this->params['get'], $this->input->getAll('get'));
		$this->assertEquals(
			$this->params['post'], 
			$this->input->getAll('post')
		);

		$this->assertEquals(
			$this->params['files'], 
			$this->input->getAll('files')
		);

		$this->assertEquals(
			$this->params['cookie'], 
			$this->input->getAll('cookie')
		);

		$this->assertEquals(
			$this->params['argv'], 
			$this->input->getAll('argv')
		);

		$input  = new AppInput('get');
		$result = $input->getAll();
		$expected = array(
			'get'	 => array(),
			'post'	 => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @depends	testGetAll
	 * @return	null
	 */	
	public function testGetAllParamTypeDoesNotExist()
	{
		$this->assertFalse($this->input->getAll('does-not-exist'));
		$this->assertFalse($this->input->getAll(array(12345)));
		$this->assertFalse($this->input->getAll(new StdClass()));
	}
}
