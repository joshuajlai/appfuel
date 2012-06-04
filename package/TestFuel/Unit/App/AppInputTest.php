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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Appfuel\App\AppInput,
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
		$app = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $app);
		$this->assertTrue($app->isGet());
		$this->assertFalse($app->isPost());
		$this->assertFalse($app->isPut());
		$this->assertFalse($app->isDelete());
		$this->assertFalse($app->isCli());
		$this->assertEquals('get', $app->getMethod());

		$this->assertEquals(array(), $app->getAll());

		/*
		 * params are categorized by method, however its the builders
		 * responsibility to ensure param categories are available
		 */
		$this->assertFalse($app->getAll('get'));
		$this->assertFalse($app->getAll('post'));
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultPostConstructor()
	{
		$method = 'Post';
		$app = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $app);
		$this->assertTrue($app->isPost());
		$this->assertFalse($app->isGet());
		$this->assertFalse($app->isPut());
		$this->assertFalse($app->isDelete());
		$this->assertFalse($app->isCli());
		$this->assertEquals('post', $app->getMethod());

		$this->assertEquals(array(), $app->getAll());
		$this->assertFalse($app->getAll('post'));
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultPutConstructor()
	{
		$method = 'put';
		$app = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $app);
		$this->assertTrue($app->isPut());
		$this->assertFalse($app->isGet());
		$this->assertFalse($app->isPost());
		$this->assertFalse($app->isDelete());
		$this->assertFalse($app->isCli());
		$this->assertEquals('put', $app->getMethod());

		$this->assertEquals(array(), $app->getAll());
		$this->assertFalse($app->getAll('put'));
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultDeleteConstructor()
	{
		$method = 'delete';
		$app = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $app);
		$this->assertTrue($app->isDelete());
		$this->assertFalse($app->isGet());
		$this->assertFalse($app->isPost());
		$this->assertFalse($app->isPut());
		$this->assertFalse($app->isCli());
		$this->assertEquals('delete', $app->getMethod());

		$this->assertEquals(array(), $app->getAll());
		$this->assertFalse($app->getAll('delete'));
	}

	/**
	 * @test
	 * @return			null	
	 */
	public function defaultCliConstructor()
	{
		$method = 'cli';
		$app = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $app);
		$this->assertTrue($app->isCli());
		$this->assertFalse($app->isGet());
		$this->assertFalse($app->isPost());
		$this->assertFalse($app->isPut());
		$this->assertFalse($app->isDelete());
		$this->assertEquals('cli', $app->getMethod());

		$this->assertEquals(array(), $app->getAll());
		$this->assertFalse($app->getAll('cli'));
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
		$app = $this->createAppInput($method, $params);
		$this->assertInstanceOf('Appfuel\App\AppInputInterface', $app);
		$this->assertEquals($params, $app->getAll());
		$this->assertEquals($params['get'], $app->getAll('get'));
		$this->assertEquals($params['post'], $app->getAll('post'));
		$this->assertEquals($params['cli'], $app->getAll('cli'));
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
		$params = array('cmd' => './my-script');
		$app = $this->createAppInput($method, $params);
		$this->assertEquals($params['cmd'], $app->getCmd());	
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
		$app = $this->createAppInput($method, $params);
		$this->assertTrue($app->isShortOptFlag('a'));
		$this->assertTrue($app->isShortOptFlag('b'));
		$this->assertFalse($app->isShortOptFlag('c'));
		$this->assertFalse($app->isShortOptFlag('not-there'));
		$this->assertFalse($app->isShortOptFlag(123245));
		$this->assertFalse($app->isShortOptFlag(true));
		$this->assertFalse($app->isShortOptFlag(array(1,2,3)));
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
		$app = $this->createAppInput($method, $params);
		$this->assertTrue($app->isLongOptFlag('my-flag-a'));
		$this->assertTrue($app->isLongOptFlag('my-flag-b'));
		$this->assertFalse($app->isLongOptFlag('my-flag-c'));
		$this->assertFalse($app->isLongOptFlag('not-there'));
		$this->assertFalse($app->isLongOptFlag(123245));
		$this->assertFalse($app->isLongOptFlag(true));
		$this->assertFalse($app->isLongOptFlag(array(1,2,3)));
	}
}
