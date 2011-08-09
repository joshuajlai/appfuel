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
namespace Test\Appfuel\Orm\Repository;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Handler\DbHandler,
	Appfuel\Orm\Domain\ObjectFactory,
	Appfuel\Orm\Domain\DataBuilder,
	Appfuel\Orm\Repository\Criteria,
	Appfuel\Orm\Repository\Assembler,
	Appfuel\Orm\Source\Db\SourceHandler,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * We are creating a class that extends the data builder to test
 * the ability of the assembler to call different/custom methods 
 * from the data builder
 */
class MyDataBuilder extends DataBuilder
{
	public function myCustomBuild($domainKey, array $data)
	{
		return array('domain-key' => $domainKey, 'data' => $data);
	}
}

/**
 * Custom class used to test callback functionality of the data builder
 */
class MyCustomClass
{
	/**
	 * This method is intended to test a custom class using a callback method
	 * to build their own data not using appfuel classes. All custom methods
	 * must have the first two parameters in their function signature 
	 * reserved for domain-key, array data
	 *
	 * @param	string	$key	the domain key used in criteria
	 * @param	array	$data	first param is always reserved to put the 
	 *							datasource in
	 * @param	mixed	$param1	simulate a parameter
	 * @param	mixed	$param2 simulate another parameter
	 * @param	mixed	$param3 simulate another paramater
	 * @return	array
	 */
	public function myDataHandler($key, array $data, $param1, $param2, $param3)
	{
		return array(
			'first'		=> $key,
			'second'	=> $data,
			'third'		=> $param1,
			'fourth'	=> $param2,
			'fifth'		=> $param3
		);
	}

	/**
	 * This method is intended to test a custom class using a callback method
	 * to build their own data not using appfuel classes
	 *
	 * @param	array	$data	first param is always reserved to put the 
	 *							datasource in
	 * @param	mixed	$param1	simulate a parameter
	 * @param	mixed	$param2 simulate another parameter
	 * @param	mixed	$param2 simulate another paramater
	 * @return	array
	 */
	static public function myOtherHandler($key, array $data, $param1)
	{
		return array(
			'first'		=> $key,
			'second'	=> $data,
			'third'		=> $param1
		);
	}
}

/**
 */
class AssemblerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Assembler
	 */
	protected $asm = null;

	/**
	 * Handle interacting with the datasource
	 * @var	SourceHandlerInterface
	 */	
	protected $sourceHandler = null;

	/**
	 * @var DbHandler
	 */
	protected $dbHandler = null;

	/**
	 * @var DataBuilderInterface
	 */
	protected $dataBuilder = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->backupRegistry();

		$this->dataBuilder   = new MyDataBuilder(new ObjectFactory());
		$this->dbHandler     = new DbHandler();
		$this->sourceHandler = new SourceHandler($this->dbHandler);
		
		$this->asm = new Assembler(
			$this->sourceHandler,
			$this->dataBuilder
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->restoreRegistry();
		unset($this->dbHandler);
		unset($this->sourceHandler);
		unset($this->asm);
	}

	/**
	 * The first arg is used to map the domain namespace
	 * 
	 * The second arg is the domain keys. These are needed to initialize
	 * the registry so the object factory can map to the correct domain
	 * object.
	 * 
	 * The third arg is the the user class. This is the class name of the
	 * domain we are trying to build
	 *
	 * The fourth arg is the data used to populate the domain with
	 *
	 */
	public function provideUserData()
	{
		$key = 'user';
		/* declare where the fake domain will be found */
		$map = array($key => __NAMESPACE__ . '\Assembler\User');
		$domainKeys = array('domain-keys' => $map);

		$userClass = $map['user'] . '\UserModel';

		/*
		 * The represents the premapped data that would come back from the
		 * data source
		 */
		$data = array(
			'id'		=> 101,
			'firstName' => 'Robert',
			'lastName'  => 'Scott-Buccluech',
			'email'		=> 'rsb.code@gmail.com' 
		);

		return array(array($key, $domainKeys, $userClass, $data));
	}

	/**
	 * @return null
	 */
	public function xtestImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Repository\AssemblerInterface',
			$this->asm
		);
	}

	/**
	 * @return null
	 */
	public function xtestGetSourceHandler()
	{
		$this->assertSame(
			$this->sourceHandler,
			$this->asm->getSourceHandler(),
			'should be the same source handler passed into the constructor'
		);

		$this->assertSame(
			$this->dataBuilder,
			$this->asm->getDataBuilder(),
			'should be the same data builder passed into the constructor'
		);
	}

	/**
	 * This test represents the general use case where the repository
	 * create a criteria used to describe what to build and the source
	 * handler has alreay return the pre mapped data to be built into
	 * the domains
	 *
	 * @dataProvider	provideUserData
	 * @param	string	$key		used to find the domain namespace
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null
	 */
	public function xtestBuildData($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);

		$criteria = new Criteria();
		$criteria->add('domain-key', $key);
		$user = $this->asm->buildData($criteria, $data);
		$this->assertInstanceOf($userClass, $user);
	
		$state = $user->_getDomainState();
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data['id'], $user->getId());
		$this->assertEquals($data['firstName'], $user->getFirstName());
		$this->assertEquals($data['lastName'], $user->getLastName());
		$this->assertEquals($data['email'], $user->getEmail());	
	}

	/**
	 * The default method is buildDomainModel is the method selected when
	 * build-method is an empty string
	 *
	 * @dataProvider	provideUserData
	 * @param	string	$key		used to find the domain namespace
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null
	 */
	public function xtestBuildDataMethodEmptyStr($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);
		
		$criteria = new Criteria();
		$criteria->add('domain-key', $key);
		$user = $this->asm->buildData($criteria, $data);
		$this->assertInstanceOf($userClass, $user);
	
		$state = $user->_getDomainState();
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data['id'], $user->getId());
		$this->assertEquals($data['firstName'], $user->getFirstName());
		$this->assertEquals($data['lastName'], $user->getLastName());
		$this->assertEquals($data['email'], $user->getEmail());	
	}


	/**
	 * In this test we are going to tell the assembler to hand back just
	 * the array comming back from the datasource by using the no-build with
	 * build-method in the criteria
	 *	
	 * @dataProvider	provideUserData
	 * @param	string	$key		used to find the domain namespace
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null 
	 */
	public function xtestBuildDataJustData($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);
		$criteria = new Criteria();
		$criteria->add('domain-key', $key)
				 ->add('build-method', 'no-build');

		$result = $this->asm->buildData($criteria, $data);
		$this->assertEquals($data, $result);
	}

	/**
	 * Here we have added a custom method to MyDataHandler for which will
	 * will tell the assembler to use to build our data. With these custom 
	 * methods the assembler expects the function signature to be 
	 * myfunc($domainKey, array $data)
	 *
	 * @dataProvider	provideUserData
	 * @param	string	$key		used to find the domain namespace
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null 
	 */
	public function xtestBuildDataOtherMethod($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);
		
		$criteria = new Criteria();
		$criteria->add('domain-key', $key)
				 ->add('build-method', 'myCustomBuild');

		$result = $this->asm->buildData($criteria, $data);
		$expected = array(
			'domain-key' => 'user',
			'data'		 => $data
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * You must have a domain key if you are using any methods that are 
	 * declared with build-method from the criteria. If you don't the assembler
	 * will throw an exception
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function xtestBuildDataNoDomainKey()
	{
		$criteria = new Criteria();
	
		/* this wont get far enough to be useful */
		$data = array('id' => 112);
		$result = $this->asm->buildData($criteria, $data);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function xtestBuildDataBuildMethodIsInt()
	{
		$criteria = new Criteria();
		$criteria->add('domain-key', 'user')
				 ->add('build-method', 1234);
	
		/* this wont get far enough to be useful */
		$data = array('id' => 112);
		$result = $this->asm->buildData($criteria, $data);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function xtestBuildDataBuildMethodIsArray()
	{
		$criteria = new Criteria();
		$criteria->add('domain-key', 'user')
				 ->add('build-method', array(1,2,3,4));
	
		/* this wont get far enough to be useful */
		$data = array('id' => 112);
		$result = $this->asm->buildData($criteria, $data);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function xtestBuildDataBuildMethodIsObject()
	{
		$criteria = new Criteria();
		$criteria->add('domain-key', 'user')
				 ->add('build-method', new StdClass());
	
		/* this wont get far enough to be useful */
		$data = array('id' => 112);
		$result = $this->asm->buildData($criteria, $data);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function xtestBuildDataBuildMethodDoesNotExist()
	{
		$criteria = new Criteria();
		$criteria->add('domain-key', 'user')
				 ->add('build-method', 'does-not-exist');
	
		/* this wont get far enough to be useful */
		$data = array('id' => 112);
		$result = $this->asm->buildData($criteria, $data);
	}

	/**
	 * Define a callback with a string and array of params. 
	 *
	 * @dataProvider	provideUserData
	 * @param	string	$key		domain key
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null
	 */
	public function testBuildDataCustom($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);
		
		$class = __NAMESPACE__ . '\MyCustomClass';
		$obj      = new $class();
		$callback = array($obj, 'myDataHandler'); 
		$params   = array('param1', 'param2', 'param3');

		$criteria = new Criteria();
		$criteria->add('domain-key', $key)
				 ->add('custom-build', $callback)
				 ->add('custom-build-params', $params);
		
		$result = $this->asm->buildData($criteria, $data);
		
		/* the custom method will return the function signature as an 
		 * associative array
		 */
		$expected = array(
			'first'  => $key,
			'second' => $data,
			'third'  => $params[0],
			'fourth' => $params[1],
			'fifth'	 => $params[2]
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * Example of using a custom class with a static method to process the
	 * data.
	 *
	 * @dataProvider	provideUserData
	 *
	 * @param	string	$key		ignored, custom functions are responsible
	 *								for passing in the domain key
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null
	 */
	public function testBuildDataCustomStaticA($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);
		
		$callback = __NAMESPACE__ . '\MyCustomClass::myOtherHandler';
		$params   = array('param1');

		$criteria = new Criteria();
		$criteria->add('domain-key', $key)
				 ->add('custom-build', $callback)
				 ->add('custom-build-params', $params);
		
		$result = $this->asm->buildData($criteria, $data);
		
		/* the custom method will return the function signature as an 
		 * associative array
		 */
		$expected = array(
			'first'  => $key,
			'second' => $data,
			'third'  => $params[0],
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * Another example of using a custom class with a static method to process
	 * data
	 *
	 * @dataProvider	provideUserData
	 *
	 * @param	string	$key		domain key used in criteria
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null
	 */
	public function testBuildDataCustomStaticB($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);
		
		$class    = __NAMESPACE__ . '\MyCustomClass';
		$callback = array($class, 'myOtherHandler');
		$params   = array('param1');

		$criteria = new Criteria();
		$criteria->add('domain-key', $key)
				 ->add('custom-build', $callback)
				 ->add('custom-build-params', $params);
		
		$result = $this->asm->buildData($criteria, $data);

		/* the custom method will return the function signature as an 
		 * associative array
		 */
		$expected = array(
			'first'  => $key,
			'second' => $data,
			'third'  => $params[0]
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * An exmple of using a closure to handle your data building
	 *
	 * @dataProvider	provideUserData
	 *
	 * @param	string	$key		ignored, custom functions are responsible
	 *								for passing in the domain key
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null
	 */
	public function testBuildDataCustomClosure($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);
		
		$callback = function ($domainKey, $dataSource, $param1) {
			return array(
				'first'		=> $domainKey, 
				'second'	=> $dataSource,
				'third'		=> $param1
			);
		};

		$params = array('param1');

		$criteria = new Criteria();
		$criteria->add('domain-key', $key)
				 ->add('custom-build', $callback)
				 ->add('custom-build-params', $params);
		
		$result = $this->asm->buildData($criteria, $data);
		/* the custom method will return the function signature as an 
		 * associative array
		 */
		$expected = array(
			'first'  => $key,
			'second' => $data,
			'third'  => $params[0],
		);
		$this->assertEquals($expected, $result);
	}


}
