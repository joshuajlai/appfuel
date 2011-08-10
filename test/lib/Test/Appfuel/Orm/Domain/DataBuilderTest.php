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
namespace Test\Appfuel\Orm\Domain;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Orm\Domain\DataBuilder,
	Appfuel\Orm\Domain\OrmObjectFactory;

/**
 * Test the ability to build out domains
 */
class DataBuilderTest extends ParentTestCase
{
	/**
	 * Used to create domain objects
	 * @var ObjectFactory
	 */
	protected $factory = null;

	/**
	 * System under test
	 * @var DataBuilder
	 */
	protected $builder = null;

	/**
	 * Domain Key map is a list of known classes we will instantiate as
	 * domain objects
	 * var array
	 */
	protected $map = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->backupRegistry();
		$path = __NAMESPACE__ . '\DataBuilder';
		$this->map = array(
			'user'		 => "$path\User",
		);


		$this->initializeRegistry(array('domain-keys' => $this->map));
		$this->factory = new OrmObjectFactory();
		$this->builder = new DataBuilder($this->factory);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->restoreRegistry();
		unset($this->factory);
		unset($this->builder);
	}

	/**
	 * @return null
	 */
	public function testImplementInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DataBuilderInterface',
			$this->builder
		);
	}

	/**
	 * The object factory is immutable object passed into the constructor
	 *
	 * @return null
	 */
	public function testGetObjectFactory()
	{
		$this->assertSame($this->factory, $this->builder->getObjectFactory());
	}

	/**
	 * Positive test case when the key is mapped and the qualified classname
	 * can be found and that class extends a domain model
	 * 
	 * @return null
	 */
	public function testBuildDomainModel()
	{
		$data = array(
			'id'		=> 101,
			'firstName' => 'Robert',
			'lastName'	=> 'Scott-Buccleuch',
			'email'		=> 'rsb.code@gmail.com'
		);

		$user = $this->builder->buildDomainModel('user', $data);

		$class = $this->map['user'] . '\UserModel';
		$this->assertInstanceOf($class, $user);
		$this->assertInstanceOf('Appfuel\Orm\Domain\DomainModel', $user);

		$state = $user->_getDomainState();
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data['id'], $user->getId());
		$this->assertEquals($data['firstName'], $user->getFirstName());
		$this->assertEquals($data['lastName'], $user->getLastName());
		$this->assertEquals($data['email'], $user->getEmail());
	}

	/**
	 * @return null
	 */
	public function testBuildDomainNewNoData()
	{
		$user  = $this->builder->buildDomainModel('user', null, true);
		$class = $this->map['user'] . '\UserModel';
		
		$this->assertInstanceOf($class, $user);
		$this->assertInstanceOf('Appfuel\Orm\Domain\DomainModel', $user);
		
		$state = $user->_getDomainState();
		$this->assertEmpty($state->getInitialMembers());
		$this->assertTrue($state->isNew());

		$this->assertNull($user->getId());
		$this->assertNull($user->getFirstName());
		$this->assertNull($user->getLastName());
		$this->assertNull($user->getEmail());
	}

	/**
	 * @return null
	 */
	public function testBuildDomainNewWithData()
	{
		$data = array(
			'id'		=> 101,
			'firstName' => 'Robert',
			'lastName'	=> 'Scott-Buccleuch',
			'email'		=> 'rsb.code@gmail.com'
		);


		$user  = $this->builder->buildDomainModel('user', $data, true);
		$class = $this->map['user'] . '\UserModel';
		
		$this->assertInstanceOf($class, $user);
		$this->assertInstanceOf('Appfuel\Orm\Domain\DomainModel', $user);
		
		$state = $user->_getDomainState();
		$this->assertEquals($data, $state->getInitialMembers());
		$this->assertTrue($state->isNew());

		$this->assertEquals($data['id'], $user->getId());
		$this->assertEquals($data['firstName'], $user->getFirstName());
		$this->assertEquals($data['lastName'], $user->getLastName());
		$this->assertEquals($data['email'], $user->getEmail());
	}

	/**
	 * You should not use the method this was but the combination exists 
	 * because i didn't refactor the double responsiblity of marshalling
	 * a new domain and one built from the datasource. In this case 
	 * we marshalled a domain object with no data which means eventually
	 * when you mark dirty all the other members are immediately invalid.
	 * Luckly the Repository controls the assembler and prevents this from
	 * happening
	 *
	 * @return null
	 */
	public function testBuildDomainNoNewNoData()
	{
		$user  = $this->builder->buildDomainModel('user');
		$class = $this->map['user'] . '\UserModel';
		
		$this->assertInstanceOf($class, $user);
		$this->assertInstanceOf('Appfuel\Orm\Domain\DomainModel', $user);
		
		$state = $user->_getDomainState();
		$this->assertEmpty($state->getInitialMembers());
		$this->assertTrue($state->isMarshal());

		$this->assertNull($user->getId());
		$this->assertNull($user->getFirstName());
		$this->assertNull($user->getLastName());
		$this->assertNull($user->getEmail());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBuildDomainModelKeyDoesNotExist()
	{
		$data = array('id' => 1);
		$domain = $this->builder->buildDomainModel('key-not-found', $data);
	}

	/**
	 * This would usually occur during an initialization error where the
	 * domain-keys map was not added to the registry
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBuildDomainModelKeyMapDoesNotExist()
	{
		/* clears out the registry */
		$this->initializeRegistry(array());
		
		$data = array('id' => 1);
		$user = $this->builder->buildDomainModel('user', $data);
	}

	/**
	 * Another miss configuration type error that might occur. In this case
	 * the map is suppose to be an array and was set to a string
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBuildDomainModelKeyMapIsAString()
	{
		$this->initializeRegistry(array('domain-keys' => 'bad-value'));
		
		$data = array('id' => 1);
		$user = $this->builder->buildDomainModel('user', $data);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBuildDomainModelKeyMapIsAnInt()
	{
		$this->initializeRegistry(array('domain-keys' => 12345));
		
		$data = array('id' => 1);
		$user = $this->builder->buildDomainModel('user', $data);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBuildDomainModelKeyMapIsAnObject()
	{
		$this->initializeRegistry(array('domain-keys' => new StdClass()));
		
		$data = array('id' => 1);
		$user = $this->builder->buildDomainModel('user', $data);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBuildDomainModelKeyMapIsEmptyArray()
	{
		$this->initializeRegistry(array('domain-keys' => array()));
		
		$data = array('id' => 1);
		$user = $this->builder->buildDomainModel('user', $data);
	}
}
