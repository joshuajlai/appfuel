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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\RequestUri,
	Appfuel\Kernel\Mvc\MvcRouteDetail,
	Appfuel\Kernel\Mvc\MvcActionBuilder;

/**
 */
class MvcActionBuilderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MvcActionBuilder
	 */
	protected $builder = null;

	/**
	 * @var string
	 */
	protected $actionClassName = null;

    /**
     * @var array
     */
    protected $serverBk = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->serverBk = $_SERVER;
		$this->actionClassName = MvcActionBuilder::getActionClassName();
		$this->builder = new MvcActionBuilder;
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		$name = $this->actionClassName;
		$this->actionClassName = MvcActionBuilder::setActionClassName($name);
		$this->builder = null;
		$_SERVER = $this->serverBk;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcActionBuilderInterface',
			$this->builder
		);

		$loader = $this->builder->getClassLoader();
		$this->assertInstanceOf(
			'Appfuel\ClassLoader\StandardAutoLoader',
			$this->builder->getClassLoader()
		);

		$className = MvcActionBuilder::getActionClassName();
		$this->assertEquals('ActionController', $className);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetActionClassName()
	{
		$name = 'MyControllerClass';
		$this->assertNull(MvcActionBuilder::setActionClassName($name));
		$this->assertEquals($name, MvcActionBuilder::getActionClassName());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideEmptyStrings
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetActionClassNameEmptyString_Failure($name)
	{
		MvcActionBuilder::setActionClassName($name);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetActionClassNameNotString_Failure($name)
	{
		MvcActionBuilder::setActionClassName($name);
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetClassLoader()
	{
		$loader = $this->getMock('Appfuel\ClassLoader\AutoLoaderInterface');
		$this->assertSame(
			$this->builder, 
			$this->builder->setClassLoader($loader)
		);
		$this->assertSame($loader, $this->builder->getClassLoader());
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetUriWithInterface()
	{
		$this->assertNull($this->builder->getUri());
		$uri = $this->getMock('Appfuel\Kernel\Mvc\RequestUriInterface');
		$this->assertSame($this->builder, $this->builder->setUri($uri));
		$this->assertSame($uri, $this->builder->getUri());
	}

	/**
	 * @dataProvider		provideNonEmptyStrings
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetUriWithString($str)
	{
		$this->assertSame($this->builder, $this->builder->setUri($str));
		$uri = $this->builder->getUri();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\RequestUri', $uri);
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testCreateUri()
	{
		$uriStr = 'my-route-key/somevar/somevalue';
		$uri = $this->builder->createUri($uriStr);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\RequestUri', $uri);
	}

    /**
     * @depends testSetUriWithString
     * @return  null
     */
    public function testUseServerRequestUri()
    {  
        $uriString = 'my-route/param1/value1';
        $_SERVER['REQUEST_URI'] = $uriString;

        $this->assertSame(
            $this->builder,
            $this->builder->useServerRequestUri(),
            'uses fluent interface'
        );

        $uri = $this->builder->getUri();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\RequestUriInterface',
            $uri,
            'Uri object built from the SERVER[REQUEST_URI]'
        );

        $this->assertEquals($uriString, $uri->getUriString());
    }

    /**
	 * @expectedException	LogicException
     * @depends				testSetUriWithString
     * @return				null
     */
	public function testUseServerRequestUriServerNotFound_Failure()
	{
		unset($_SERVER['REQUEST_URI']);
        $this->builder->useServerRequestUri();
	}

    /**
	 * @expectedException	LogicException
	 * @dataProvider		provideInvalidStringsIncludeNull
     * @depends				testSetUriWithString
     * @return				null
     */
	public function testUseServerRequestUriUriNotString_Failure($uri)
	{
		$_SERVER['REQUEST_URI'] = $uri;
        $this->builder->useServerRequestUri();
	}

    /**
     * @depends testInitialState
     * @return  null
     */
    public function testGetSetInput()
    {
        $this->assertNull($this->builder->getInput(), 'default value is null');

        $input = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
        $this->assertSame(
            $this->builder,
            $this->builder->setInput($input),
            'uses fluent interface'
        );
        $this->assertSame($input, $this->builder->getInput());
    }

    /**
     * @depends testInitialState
     * @return  null
     */
    public function testCreateInput()
    {
        $inputClass = 'Appfuel\Kernel\Mvc\AppInput';
        $input = $this->builder->createInput('get');
        $expected = array(
            'get'   => array(),
            'post'  => array(),
            'files' => array(),
            'cookie' => array(),
            'argv'  => array()
        );
        $this->assertEquals($expected, $input->getAll());
        $this->assertInstanceOf($inputClass, $input);

        $input = $this->builder->createInput('post');
        $this->assertInstanceOf($inputClass, $input);
        $this->assertEquals($expected, $input->getAll());

        $input = $this->builder->createInput('cli');
        $this->assertInstanceOf($inputClass, $input);
        $this->assertEquals($expected, $input->getAll());


        $params = array(
            'get'    => array('param1' => 'value1'),
            'post'   => array('param2' => 'value2'),
            'files'  => array('param3' => 'value3'),
            'cookie' => array('param4' => 'value4'),
            'argv'   => array('param5' => 'value5')
        );
        $input = $this->builder->createInput('get', $params);
        $this->assertInstanceOf($inputClass, $input);
        $this->assertEquals($params, $input->getAll());

        $input = $this->builder->createInput('post', $params);
        $this->assertInstanceOf($inputClass, $input);
        $this->assertEquals($params, $input->getAll());

        $input = $this->builder->createInput('cli', $params);
        $this->assertInstanceOf($inputClass, $input);
        $this->assertEquals($params, $input->getAll());
    }

    /**
     * When the request method is not set it will default to cli. This is 
     * because php automatically sets the REQUEST_METHOD in the server super
     * global for all http request but not for cli request. 
     * 
     * @depends testCreateInput
     * @return  null
     */
    public function testBuildInputFromDefaultsRequestMethodNotSet()
    {
        $this->assertNull($this->builder->getInput());

        $uri = new RequestUri('my-route/af-param1/value1');
        $this->builder->setUri($uri);

        $this->assertSame(
            $this->builder,
            $this->builder->buildInputFromDefaults(),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $expected = array(
            'get'    => array('af-param1' => 'value1'),
            'post'   => $_POST,
            'files'  => $_FILES,
            'cookie' => $_COOKIE,
            'argv'   => $_SERVER['argv']
        );

        $this->assertEquals($expected, $input->getAll());
    }

    /**
     * @depends testCreateInput
     * @return  null
     */
    public function testDefineInputAsNoParams()
    {
        $this->assertNull($this->builder->getInput());

        $emptyParams = array(
            'get'    => array(),
            'post'   => array(),
            'files'  => array(),
            'cookie' => array(),
            'argv'   => array()
        );


        $method = 'get';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($emptyParams, $input->getAll());

        $method = 'post';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );


        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($emptyParams, $input->getAll());

        $method = 'cli';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method),
            'uses fluent interface'
        );

        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($emptyParams, $input->getAll());
    }


    /**
     * @depends testCreateInput
     * @return  null
     */
    public function testDefineInputAsWithGetParams()
    {
        $this->assertNull($this->builder->getInput());

        $params = array(
            'get' => array('param1' => 'value1'),
        );
        $method = 'get';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method, $params),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $expected = array(
            'get'   => $params['get'],
            'post'  => array(),
            'files' => array(),
            'cookie' => array(),
            'argv'  => array()
        );
        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($expected, $input->getAll());
    }

    /**
     * @depends testCreateInput
     * @return  null
     */
    public function testDefineInputAsWithPostParams()
    {
        $this->assertNull($this->builder->getInput());

        $params = array(
            'post' => array('param1' => 'value1'),
        );
        $method = 'post';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method, $params),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $expected = array(
            'get'   => array(),
            'post'  => $params['post'],
            'files' => array(),
            'cookie' => array(),
            'argv'  => array()
        );
        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($expected, $input->getAll());
    }

    /**
     * @depends testCreateInput
     * @return  null
     */
    public function testDefineInputAsWithFilesParams()
    {
        $this->assertNull($this->builder->getInput());

        $params = array(
            'files' => array('param1' => 'value1'),
        );
        $method = 'cli';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method, $params),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $expected = array(
            'get'   => array(),
            'post'  => array(),
            'files' => $params['files'],
            'cookie' => array(),
            'argv'  => array()
        );
        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($expected, $input->getAll());
    }

    /**
     * @depends testCreateInput
     * @return  null
     */
    public function testDefineInputAsWithCookieParams()
    {
        $this->assertNull($this->builder->getInput());

        $params = array(
            'cookie' => array('param1' => 'value1'),
        );
        $method = 'get';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method, $params),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $expected = array(
            'get'   => array(),
            'post'  => array(),
            'files' => array(),
            'cookie' => $params['cookie'],
            'argv'  => array()
        );
        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($expected, $input->getAll());
    }

    /**
     * @depends testCreateInput
     * @return  null
     */
    public function testDefineInputAsWithArgvParams()
    {
        $this->assertNull($this->builder->getInput());

        $params = array(
            'argv' => array('param1' => 'value1'),
        );
        $method = 'cli';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method, $params),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $expected = array(
            'get'   => array(),
            'post'  => array(),
            'files' => array(),
            'cookie' => array(),
            'argv'  => $params['argv']
        );
        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($expected, $input->getAll());
    }

    /**
     * @depends testCreateInput
     * @return  null
     */
    public function testDefineInputAsWithAllParams()
    {
        $this->assertNull($this->builder->getInput());

        $params = array(
            'get' => array('param1' => 'value1'),
            'post' => array('paramr2' => 'value2'),
            'files' => array('param3' => 'value3'),
            'cookie' => array('param4' => 'value4'),
            'argv' => array('param5' => 'value5'),
        );
        $method = 'cli';
        $this->assertSame(
            $this->builder,
            $this->builder->defineInputAs($method, $params),
            'uses fluent interface'
        );
    
        $input = $this->builder->getInput();
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\AppInput',
            $input
        );

        $this->assertEquals($method, $input->getMethod());
        $this->assertEquals($params, $input->getAll());
    }

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetAddAclCode()
	{
		$this->assertEquals(array(), $this->builder->getAclCodes());
		
		$code1 = 'my-code';
		$this->assertSame(
			$this->builder,
			$this->builder->addAclCode($code1)
		);
		$expected = array($code1);
		$this->assertEquals($expected, $this->builder->getAclCodes());

		$code2 = 'your-code';
		$this->assertSame(
			$this->builder,
			$this->builder->addAclCode($code2)
		);
		$expected = array($code1, $code2);
		$this->assertEquals($expected, $this->builder->getAclCodes());

		$code3 = 'our-code';
		$this->assertSame(
			$this->builder,
			$this->builder->addAclCode($code3)
		);
		$expected = array($code1, $code2, $code3);
		$this->assertEquals($expected, $this->builder->getAclCodes());
	}

	/**
	 * @depends	testGetAddAclCode
	 * @return	null
	 */
	public function testAddAclCodeNoDuplicates()
	{
		$code1 = 'my-code';
		$code2 = 'my-code';
		$code3 = 'my-code';
		$this->builder->addAclCode($code1)
					  ->addAclCode($code2)
					  ->addAclCode($code3);

		$this->assertEquals(array($code1), $this->builder->getAclCodes());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testGetAddAclCode
	 * @return				null
	 */
	public function testAddAclCodeEmptyString_Failure()
	{
		$this->builder->addAclCode('');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testGetAddAclCode
	 * @return				null
	 */
	public function testAddAclCodeInvalidString_Failure($code)
	{
		$this->builder->addAclCode($code);
	}


}
