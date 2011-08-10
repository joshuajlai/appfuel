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
	Appfuel\Db\DbError,
	Appfuel\Db\Handler\DbHandler,
	Appfuel\Orm\Domain\ObjectFactory,
	Appfuel\Orm\Domain\DataBuilder,
	Appfuel\Orm\Repository\Criteria,
	Appfuel\Orm\Repository\Assembler,
	Appfuel\Orm\Source\Db\SourceHandler,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\Orm\Repository\CriteriaInterface;

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
 * Extends the source handler and is used to test that the assembler
 * can find and execute method in this class indicated by the criteria
 * key 'source-method'. All of these methods must take a criteria as
 * a parameter.
 */
class MySourceHandler extends SourceHandler
{
	/**
	 * Return back the parameter in a known format so any test can assert
	 * the it was correctly called
	 *
	 * @param	CriteriaInterface $criteria
	 * @return	array
	 */
	public function fetchMyData(CriteriaInterface $criteria)
	{
		return array('first' => $criteria);
	}

	/**	
	 * Hand back some fake user data to build a domain with
	 * 
	 * @param	CriteriaInterface	$criteria
	 * @return	array
	 */
	public function fetchUserData(CriteriaInterface $criteria)
	{
		return array(
			'id'		=> 99,
			'firstName' => 'Robert',
			'lastName'	=> 'Scott-Buccleuch',
			'email'		=> 'rsb.code@gmail.com'
		);
	}

	/**
	 * Used to test the assembler process when the source handler
	 * returns an error
	 *
	 * @param	CriteriaInterface $criteria
	 * @return	DbError
	 */
	public function fetchUserWithError(CriteriaInterface $criteria)
	{
		return new DbError(99, 'this is an error');
	}

	/**
	 * Used to return custom data
	 *
	 * @param	CriteriaInterface	$criteria
	 * @return	string
	 */
	public function fetchStringData(CriteriaInterface $criteria)
	{
		return 'Assembler expects an array not a string';
	}

	/**
	 * Used to return custom data
	 *
	 * @param	CriteriaInterface	$criteria
	 * @return	true
	 */
	public function fetchBoolData(CriteriaInterface $criteria)
	{
		return true;
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
		$this->sourceHandler = new MySourceHandler($this->dbHandler);
		
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
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Repository\AssemblerInterface',
			$this->asm
		);
	}

	/**
	 * @return null
	 */
	public function testGetSourceHandler()
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
	 * Execute source looks into the Criteria for the method to be used 
	 * in the source handler. It then check the source handler for the 
	 * existence of the method and executes the method pushing the criteria
	 * as the only parameter and handing back the results
	 *
	 * @return null
	 */
	public function testExecuteSource()
	{
		$criteria = new Criteria();

		/* special method we used in the source handler class declared at 
		 * the top of this file to prove we can fire any method in this class
		 * if it exists publicly
		 */
		$criteria->add('source-method', 'fetchMyData');
		$result = $this->asm->executeSource($criteria);
		$expected = array('first' => $criteria);
		$this->assertEquals($expected, $result);
	}

	/**
	 * Method defined can not be an empty string
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testExecuteSourceMethodEmptyString()
	{
		$criteria = new Criteria();

		$criteria->add('source-method', '');
		$result = $this->asm->executeSource($criteria);
	}

	/**
	 * Method defined can not be an integer
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testExecuteSourceMethodInt()
	{
		$criteria = new Criteria();

		$criteria->add('source-method', 12345);
		$result = $this->asm->executeSource($criteria);
	}

	/**
	 * Method defined can not be an array
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testExecuteSourceMethodArray()
	{
		$criteria = new Criteria();

		$criteria->add('source-method', array(1,2,3));
		$result = $this->asm->executeSource($criteria);
	}

	/**
	 * Method defined can not be an object
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testExecuteSourceMethodObject()
	{
		$criteria = new Criteria();

		$criteria->add('source-method', new StdClass());
		$result = $this->asm->executeSource($criteria);
	}

	/**
	 * Method defined must exist
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testExecuteSourceMethodNotFound()
	{
		$criteria = new Criteria();

		$criteria->add('source-method', 'does-not-exist');
		$result = $this->asm->executeSource($criteria);
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
	public function testBuildData($key, $keys, $userClass, $data)
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
	public function testBuildDataMethodEmptyStr($key, $keys, $userClass, $data)
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
	public function testBuildDataOtherMethod($key, $keys, $userClass, $data)
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
	public function testBuildDataNoDomainKey()
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
	public function testBuildDataBuildMethodIsInt()
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
	public function testBuildDataBuildMethodIsArray()
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
	public function testBuildDataBuildMethodIsObject()
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
	public function testBuildDataBuildMethodDoesNotExist()
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
	 * @param	string	$key		the domain key used in criteria
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

	/**
	 * When no params are given for custom builds two params are always added
	 * which means custom builds require at min two params. The first is 
	 * @dataProvider	provideUserData
	 *
	 * @param	string	$key		the domain key used in criteria
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null
	 */
	public function testBuildDataCustom1Param($key, $keys, $userClass, $data)
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

	/**
	 * When no params are given for custom builds two params are always added
	 * which means custom builds require at min two params. The first is 
	 * @dataProvider	provideUserData
	 *
	 * @param	string	$key		the domain key used in criteria
	 * @param	array	$keys		the list of namespaces mapped
	 * @param	string	$userClass	qualified class name of domain for key
	 * @param	array	$data		data needed to build domain
	 * @return	null
	 */
	public function testBuildDataCustomNoParam($key, $keys, $userClass, $data)
	{
		$this->initializeRegistry($keys);
		
		$callback = function ($domainKey, $dataSource) {
			return array(
				'first'		=> $domainKey, 
				'second'	=> $dataSource,
			);
		};

		$criteria = new Criteria();
		$criteria->add('domain-key', $key)
				 ->add('custom-build', $callback);
		
		$result = $this->asm->buildData($criteria, $data);
		/* the custom method will return the function signature as an 
		 * associative array
		 */
		$expected = array(
			'first'  => $key,
			'second' => $data,
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * Here we will test process with our custom source handler returning
	 * known data that will be used to build a user object via the default
	 * databuilder method buildDomainModel. Note: that this user model
	 * is just a fake domain we created for this test to prove this works
	 *
	 * @return null
	 */
	public function testProcess()
	{
        /* declare where the fake domain will be found */
        $map = array('user' => __NAMESPACE__ . '\Assembler\User');
        $domainKeys = array('domain-keys' => $map);
		$this->initializeRegistry($domainKeys);

        $userClass = $map['user'] . '\UserModel';

		$criteria = new Criteria();
		$criteria->add('domain-key', 'user')
				 ->add('source-method', 'fetchUserData');

		$build = new Criteria();

		$user = $this->asm->process($criteria);
		$this->assertInstanceOf($userClass, $user);
		$this->assertInstanceOf('Appfuel\Orm\Domain\DomainModel', $user);
		$state = $user->_getDomainState();
		
		$this->assertTrue($state->isMarshal());
		$this->assertEquals(99, $user->getId());
		$this->assertEquals('Robert', $user->getFirstName());
		$this->assertEquals('Scott-Buccleuch', $user->getLastName());
		$this->assertEquals('rsb.code@gmail.com', $user->getEmail());
	}

	/**
	 * Here we will instruct the assembler to ignore the build and return
	 * the data raw
	 *
	 * @return null
	 */
	public function testProcessIgnoreBuild()
	{
		$criteria = new Criteria();
		$criteria->add('domain-key', 'user')
				 ->add('source-method', 'fetchUserData')
				 ->add('ignore-build', true);

		$user = $this->asm->process($criteria);
		$expected = array(
            'id'        => 99,
            'firstName' => 'Robert',
            'lastName'  => 'Scott-Buccleuch',
            'email'     => 'rsb.code@gmail.com'
        );
		$this->assertEquals($expected, $user);
	}

	/**
	 * Here we will show the assmebler returns immediate when an error 
	 * interface is detected
	 *
	 * @return null
	 */
	public function testProcessErrorReturned()
	{
		$criteria = new Criteria();
		$criteria->add('domain-key', 'user')
				 ->add('source-method', 'fetchUserWithError');

		$user = $this->asm->process($criteria);
		$this->assertInstanceOf(
			'Appfuel\Framework\AppfuelErrorInterface',
			$user
		);
	}

	/**
	 * Here we will show the assmebler will throw an exception when anything
	 * but an array is returned. If you truely need the data then specify 
	 * ignore build
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testProcessInvalidReturnData()
	{
		$criteria = new Criteria();
		$criteria->add('source-method', 'fetchStringData')
				 ->add('domain-key', 'user');

		$user = $this->asm->process($criteria);
	}

	/**
	 * In this test we will have the source return a bool and have a 
	 * custom closure accept it. To do this we need to specify the 
	 * key ignore-return-type
	 *
	 * @return null
	 */
	public function testProcessInvalidReturnCustomData()
	{
		$callback = function($key, $data) {
			return array('domain-key' => $key, 'data' => $data);
		};

		$criteria = new Criteria();
		$criteria->add('domain-key', 'user')
				 ->add('custom-build', $callback)
			     ->add('source-method', 'fetchBoolData')
				 ->add('ignore-return-type', true);

		$result = $this->asm->process($criteria);
		$expected = array(
			'domain-key' => 'user',
			'data'		 => true
		);
		$this->assertEquals($expected, $result);
	}
}
