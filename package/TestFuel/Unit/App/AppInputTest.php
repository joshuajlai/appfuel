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
namespace TestFuel\Unit\Validate;

use StdClass,
	Appfuel\App\AppInput,
	Appfuel\Validate\ValidationFactory,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary;

/**
 * The request object was designed to service web,api and cli request
 */
class AppInputTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideMostUsedMethods()
	{
		return array(
			array('get'),
			array('post'),
			array('put'),
			array('delete'),
			array('cli')
		);
	}

	/**
	 * @param	string $method
	 * @param	array	$params
	 * @param	ValidationHandlerInterface	$handler
	 * @return	AppInput
	 */
	public function createAppInput($method,
								   array $params = array(),
								   ValidationHandlerInterface $handler = null)
	{
		return new AppInput($method, $params, $handler);
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultGetConstructor()
	{
		$method = 'GET';
		$input = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $input);
		$this->assertTrue($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isPut());
		$this->assertFalse($input->isDelete());
		$this->assertFalse($input->isCli());
		$this->assertEquals('get', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());

		/*
		 * params are categorized by method, however its the builders
		 * responsibility to ensure param categories are available
		 */
		$this->assertFalse($input->getAll('get'));
		$this->assertFalse($input->getAll('post'));
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultPostConstructor()
	{
		$method = 'Post';
		$input = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $input);
		$this->assertTrue($input->isPost());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPut());
		$this->assertFalse($input->isDelete());
		$this->assertFalse($input->isCli());
		$this->assertEquals('post', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());
		$this->assertFalse($input->getAll('post'));
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultPutConstructor()
	{
		$method = 'put';
		$input = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $input);
		$this->assertTrue($input->isPut());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isDelete());
		$this->assertFalse($input->isCli());
		$this->assertEquals('put', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());
		$this->assertFalse($input->getAll('put'));
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultDeleteConstructor()
	{
		$method = 'delete';
		$input = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $input);
		$this->assertTrue($input->isDelete());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isPut());
		$this->assertFalse($input->isCli());
		$this->assertEquals('delete', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());
		$this->assertFalse($input->getAll('delete'));
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultCliConstructor()
	{
		$method = 'cli';
		$input = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $input);
		$this->assertTrue($input->isCli());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isPut());
		$this->assertFalse($input->isDelete());
		$this->assertEquals('cli', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());
		$this->assertFalse($input->getAll('cli'));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function constructorManyParams()
	{
		$method = 'get';
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 1234,
				'param3' => new StdClass()
			),
			'post' => array(
				'param4' => 'value-4',
				'param5' => 'value-5',
			),
			'cli' => array(
				'param6' => 'value-6'
			)
		);
		$input = $this->createAppInput($method, $params);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $input);
		$this->assertEquals($params, $input->getAll());
		$this->assertEquals($params['get'], $input->getAll('get'));
		$this->assertEquals($params['post'], $input->getAll('post'));
		$this->assertEquals($params['cli'], $input->getAll('cli'));
	}

	/**
	 * Used to retrieve the calling script name for the command line. This
	 * assumes a param type of 'cmd' 
	 * @test
	 * @return	null
	 */
	public function getCmd()
	{
		$method = 'cli';
		$params = array('args' => array('cmd' => './my-script'));
		$input = $this->createAppInput($method, $params);
		$this->assertEquals($params['args']['cmd'], $input->getCmd());

		$params = array('args' => array('notcmd' => './my-script'));
		$input = $this->createAppInput($method, $params);
		$this->assertFalse($input->getCmd());

		$input = $this->createAppInput($method, array());
		$this->assertFalse($input->getCmd());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function getArgs()
	{
		$method = 'cli';
		$params['args']['list'] = array('arg1', 'arg2', 'arg3');
		$input = $this->createAppInput($method, $params);
		$this->assertEquals($params['args']['list'], $input->getArgs());

		unset($params['args']['list']);
		$params['args']['not-list'] = array('arg1', 'arg2', 'arg3');
		$input = $this->createAppInput($method, $params);
		$this->assertFalse($input->getArgs());
	}

	/**
	 * Also used for the command line, determines if a short flag has been
	 * set
	 *
	 * @test
	 * @return	null
	 */
	public function isShortOptFlag()
	{
		$method = 'cli';
		$params = array(
			'short' => array(
				'a' => true,
				'b' => true,
				'c' => false
			)
		);
		$input = $this->createAppInput($method, $params);
		$this->assertTrue($input->isShortOptFlag('a'));
		$this->assertTrue($input->isShortOptFlag('b'));
		$this->assertFalse($input->isShortOptFlag('c'));
		$this->assertFalse($input->isShortOptFlag('not-there'));
		$this->assertFalse($input->isShortOptFlag(123245));
		$this->assertFalse($input->isShortOptFlag(true));
		$this->assertFalse($input->isShortOptFlag(array(1,2,3)));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function getShortOption()
	{
		$method = 'cli';
		$params = array(
			'short' => array(
				'a' => 'value-a',
				'b' => 1234,
				'c' => 'value-c'
			)
		);
		$input = $this->createAppInput($method, $params);
		$this->assertEquals('value-a', $input->getShortOpt('a'));
		$this->assertEquals(1234, $input->getShortOpt('b'));
		$this->assertEquals('value-c', $input->getShortOpt('c'));
		$this->assertNull($input->getShortOpt('z'));
		$this->assertEquals('default', $input->getShortOpt('z', 'default'));
		$this->assertNull($input->getShortOpt(true));
		$this->assertNull($input->getShortOpt(array(1,2,3)));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isLongOptFlag()
	{
		$method = 'cli';
		$params = array(
			'long' => array(
				'my-flag-a' => true,
				'my-flag-b' => true,
				'my-flag-c' => false
			)
		);
		$input = $this->createAppInput($method, $params);
		$this->assertTrue($input->isLongOptFlag('my-flag-a'));
		$this->assertTrue($input->isLongOptFlag('my-flag-b'));
		$this->assertFalse($input->isLongOptFlag('my-flag-c'));
		$this->assertFalse($input->isLongOptFlag('not-there'));
		$this->assertFalse($input->isLongOptFlag(123245));
		$this->assertFalse($input->isLongOptFlag(true));
		$this->assertFalse($input->isLongOptFlag(array(1,2,3)));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function getLongOpt()
	{
		$method = 'cli';
		$params = array(
			'long' => array(
				'my-flag-a' => 'value-a',
				'my-flag-b' => 12345,
				'my-flag-c' => 'value-c'
			)
		);
		$input = $this->createAppInput($method, $params);
		$this->assertEquals('value-a', $input->getLongOpt('my-flag-a'));
		$this->assertEquals(12345, $input->getLongOpt('my-flag-b'));
		$this->assertEquals('value-c', $input->getLongOpt('my-flag-c'));

		$this->assertNull($input->getLongOpt('none'));
		$this->assertEquals('default', $input->getLongOpt('none', 'default'));
		$this->assertNull($input->getLongOpt(true));
		$this->assertNull($input->getLongOpt(array(1,2,3)));
		$this->assertNull($input->getLongOpt(new StdClass()));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isValidParamType()
	{
		$method = 'cli';
		$params = array(
			'typeA' => array(
				'param1' => 'value1'
			),
			'TYPEB' => array(
				'param2' => 'value2'
			),

			'typec' => array(
				'param3' => 'value3'
			)
		);
		$input = $this->createAppInput($method, $params);
		$this->assertTrue($input->isValidParamType('typeA'));
		$this->assertTrue($input->isValidParamType('TYPEB'));
		$this->assertTrue($input->isValidParamType('typec'));

		$this->assertFalse($input->isValidParamType('typea'));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return	null
	 */
	public function isValidParamTypeNonString($type)
	{
		$method = 'cli';
		$params = array(
			'typeA' => array(
				'param1' => 'value1'
			),
			'TYPEB' => array(
				'param2' => 'value2'
			),

			'typec' => array(
				'param3' => 'value3'
			)
		);
		$input = $this->createAppInput($method, $params);
		$this->assertFalse($input->isValidParamType($type));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function retrieveWitGet()
	{
		$method = 'post';
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 12345,
			),
			'post' => array(
				'param3' => 'value3'
			),
		);
		$input = $this->createAppInput($method, $params);

		$this->assertEquals('value1', $input->get('get', 'param1'));
		$this->assertEquals('value1', $input->get('Get', 'param1'));
		$this->assertEquals(12345, $input->get('get', 'param2'));
		$this->assertEquals('value3', $input->get('post', 'param3'));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function getKeyNotValid($key)
	{
		$method = 'post';
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 12345,
			),
			'post' => array(
				'param3' => 'value3'
			),
		);
		$input = $this->createAppInput($method, $params);

		$this->assertNull($input->get('get', $key));
		$this->assertEquals('default', $input->get('get', $key, 'default'));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function getParamTypeInvalid($type)
	{
		$method = 'post';
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 12345,
			),
			'post' => array(
				'param3' => 'value3'
			),
		);
		$input = $this->createAppInput($method, $params);

		$this->assertNull($input->get($type, 'param1'));
		$this->assertEquals('default', $input->get($type, 'param1', 'default'));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function collect()
	{
		$method = 'post';
		$params = array(
			'post' => array(
				'param1' => 'value1',
				'param2' => 12345,
				'param3' => 'value3',
				'param4' => 'value4',
				'param5' => 'value5'
			),
		);
		$input = $this->createAppInput($method, $params);
		$keys  = array('param1', 'param2', 'param5');
	
		$result = $input->collect('post', $keys);
		$class  = 'Appfuel\DataStructure\Dictionary';
		$this->assertInstanceOf($class, $result);
		$expected = array(
			'param1' => 'value1', 
			'param2' => 12345, 
			'param5' => 'value5'
		);
		$this->assertEquals($expected, $result->getAll());

		
		$result = $input->collect('post', $keys, true);
		$this->assertEquals($expected, $result);

		return $params;
	}

	/**
	 * @test
	 * @depends	collect
	 * @return	null
	 */
	public function collectKeyNotFound(array $params)
	{
		$input = $this->createAppInput('post', $params);
		$keys  = array('param1', new StdClass(), true, 'paramXX', 'param5');
		$expected = array(
			'param1' => 'value1', 
			'param5' => 'value5'
		);
		$result = $input->collect('post', $keys, true);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isSatifiedByOneField()
	{
		$boolClass = 'Appfuel\Validate\Filter\BoolFilter';
		$intClass  = 'Appfuel\Validate\Filter\IntFilter';
		$ipClass   = 'Appfuel\Validate\Filter\IpFilter';

		ValidationFactory::addToValidatorMap('int', $intClass);

		$params = array(
			'get' => array(
				'key-a' => 123,
			),
		);
		$input = $this->createAppInput('post', $params);

		$list[] = array(
			'field' => 'key-a',
			'location' => 'get',
			'filters' => array(
				'int' => array(
					'options' => array(
						'min' => 100,
						'max' => 124
					),
					'error' => 'integer must be between 100 and 124'
				),
			),
		);

		$this->assertTrue($input->isSatisfiedBy($list));
		$this->assertFalse($input->isError());
		
		$stack = $input->getErrorStack();
		$this->assertInstanceOf('Appfuel\Error\ErrorStack', $stack);
		$this->assertFalse($stack->isError());
		$this->assertEquals(123, $input->getClean('key-a'));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isSatifiedByOneFieldFailure()
	{
		$ipClass   = 'Appfuel\Validate\Filter\IpFilter';

		ValidationFactory::addToValidatorMap('ip', $ipClass);

		$params = array(
			'post' => array(
				'key-a' => 'abc.168.1.1',
			),
		);
		$input = $this->createAppInput('post', $params);

		$list[] = array(
			'field' => 'key-a',
			'location' => 'post',
			'filters' => array(
				'ip' => array(
					'error' => 'invalid ip for key-a'
				),
			),
		);

		$this->assertFalse($input->isSatisfiedBy($list));
		$this->assertTrue($input->isError());
		
		$stack = $input->getErrorStack();
		$this->assertInstanceOf('Appfuel\Error\ErrorStack', $stack);
		$this->assertTrue($stack->isError());

		/* error message with default code of 500 */
		$this->assertEquals('invalid ip for key-a', $stack->getMessage());
		$this->assertEquals(500, $stack->getCode());
		$this->assertNull($input->getClean('key-a'));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isSatifiedByManyFieldsOneValidator()
	{
		$intClass  = 'Appfuel\Validate\Filter\IntFilter';
		ValidationFactory::addToValidatorMap('int', $intClass);

		$params = array(
			'get' => array(
				'key-a' => 123,
				'key-b' => 101,
				'key-c' => 115
			),
		);
		$input = $this->createAppInput('post', $params);

		$list[] = array(
			'fields' => array('key-a', 'key-b', 'key-c'),
			'location' => 'get',
			'filters' => array(
				'int' => array(
					'options' => array(
						'min' => 100,
						'max' => 124
					),
					'error' => 'integer must be between 100 and 124'
				),
			),
		);
	

		$this->assertTrue($input->isSatisfiedBy($list));
		$this->assertFalse($input->isError());
		
		$stack = $input->getErrorStack();
		$this->assertInstanceOf('Appfuel\Error\ErrorStack', $stack);
		$this->assertFalse($stack->isError());
		$this->assertEquals(123, $input->getClean('key-a'));
		$this->assertEquals(101, $input->getClean('key-b'));
		$this->assertEquals(115, $input->getClean('key-c'));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isSatifiedByManyFieldsManyValidators()
	{
		$boolClass = 'Appfuel\Validate\Filter\BoolFilter';
		$intClass  = 'Appfuel\Validate\Filter\IntFilter';
		$ipClass   = 'Appfuel\Validate\Filter\IpFilter';

		ValidationFactory::addToValidatorMap('int', $intClass);
		ValidationFactory::addToValidatorMap('bool', $boolClass);
		ValidationFactory::addToValidatorMap('ip', $ipClass);

		$params = array(
			'get' => array(
				'key-a' => 123,
				'key-b' => 101,
				'key-c' => 115,
				'key-g' => '10.0.0.4',
			),
			'post' => array(
				'key-d' => 'on',
				'key-e' => 'off',
				'key-x' => 'not a bool',
				'key-f' => '192.168.1.1',
			),
		);

		$input = $this->createAppInput('post', $params);

		$list = array(
			array(
				'fields' => array('key-a', 'key-b', 'key-c'),
				'location' => 'get',
				'filters' => array(
					'int' => array(
						'options' => array(
							'min' => 100,
							'max' => 124
						),
						'error' => 'integer must be between 100 and 124'
					),
				),
			),
			array(
				'fields'	=> array('key-d', 'key-e', 'key-x'),
				'location'	=> 'post',
				'filters'	=> array(
					'bool' => array(
						'error' => 'key-d or key-e are not valid bools'
					)
				),
			),
			array(
				'field'		=> 'key-f',
				'location'	=> 'post',
				'filters'	=> array(
					'ip' => array(
						'error' => 'key-d is not a valid ip'
					)
				),
			),
			array(
				'field'		=> 'key-g',
				'location'	=> 'get',
				'filters'	=> array(
					'ip' => array(
						'error' => 'key-d is not a valid ip'
					)
				),
			),
		);
	

		$this->assertTrue($input->isSatisfiedBy($list));
		$this->assertFalse($input->isError());
		
		$stack = $input->getErrorStack();
		$this->assertInstanceOf('Appfuel\Error\ErrorStack', $stack);
		$this->assertFalse($stack->isError());
		$this->assertEquals(123, $input->getClean('key-a'));
		$this->assertEquals(101, $input->getClean('key-b'));
		$this->assertEquals(115, $input->getClean('key-c'));
		$this->assertEquals(true, $input->getClean('key-d'));
		$this->assertEquals(false,$input->getClean('key-e'));
		$this->assertEquals(false,$input->getClean('key-x'));
		$this->assertEquals('192.168.1.1', $input->getClean('key-f'));
		$this->assertEquals('10.0.0.4', $input->getClean('key-g'));
	}
}
