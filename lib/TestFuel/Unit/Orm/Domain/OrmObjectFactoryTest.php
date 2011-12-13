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
namespace TestFuel\Test\Orm\Domain;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Orm\Domain\DomainObjectFactory;

/**
 * Test the ablity of the object factory to pull a domain key mapping from
 * the registry convert it to fully qualifed namespace and instantiate it
 */
class OrmObjectFactoryTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ObjectFactory
	 */
	protected $factory = null;

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
		$this->map = array(
			'user'		 => "\Example\Domain\User\UserModel",
			'user-email' => "\Example\Domain\User\Email\EmailModel",
			'role'		 => "\Example\Domain\\Role\\RoleModel",
			'non-domain' => "\Example\\Custom\\CustomDomainObject"
		);


		$this->initializeRegistry(null, $this->map);
		$this->factory = new DomainObjectFactory();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->restoreRegistry();
		unset($this->factory);
	}

	/**
	 * @return null
	 */
	public function testImplementInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainObjectFactoryInterface',
			$this->factory
		);
	}

	/**
	 * @return null
	 */
	public function testCreateDomainObject()
	{
		$result = $this->factory->createDomainObject('user');
		
		$this->assertInstanceOf($this->map['user'], $result);

		$result = $this->factory->createDomainObject('user-email');
		$this->assertInstanceOf($this->map['user-email'], $result);

		/* second parameter is same as default */
		$result = $this->factory->createDomainObject('role', true);
		$this->assertInstanceOf($this->map['role'], $result);	
	}

	/**
	 * When you need to create a object that is not a domain, but you 
	 * want it mapped like a domain use the second parameter with a false
	 *
	 * @return	null
	 */
	public function testCreateDomainObjectNotADomain()
	{
		/* when the second parameter is false no naming convention is applied
		 * and the mapped string is considered the fully qualified class
		 */
		$result = $this->factory->createDomainObject('non-domain', false);
		$this->assertInstanceOf($this->map['non-domain'], $result);	
	}

	/**
	 * The domain key map lives in appfuel's registry under the key
	 * 'domain-key'. When that map does not exist createDomainObject will
	 * return false for any parameters
	 *
	 * @return	null
	 */
	public function testCreateDomainObjectNoKeyMapInRegistry()
	{
		$this->initializeRegistry(array(), array());
		$this->assertFalse($this->factory->createDomainObject('user'));
		$this->assertFalse($this->factory->createDomainObject('user-email'));
		$this->assertFalse($this->factory->createDomainObject('role'));
		$this->assertFalse($this->factory->createDomainObject('non-domain'));
		$this->assertFalse($this->factory->createDomainObject('random-key'));	
	}

	/**
	 * method will return false for anything that is not an array even for
	 * empty arrays
	 *
	 * @return null
	 */
	public function testCreateDomainObjectMapExistEmptyArray()
	{
		$this->initializeRegistry(null, array());
		$this->assertFalse($this->factory->createDomainObject('user'));
	}


	/**
	 * When the object factory can not find a class it catch and re throws 
	 * a much more specific exception .
	 *
	 * @expectedException	
	   Appfuel\Framework\Orm\Domain\MappedObjectNotFoundException
	 * @return null 
	 */
	public function testCreateDomainKeyMappedClassNotFound()
	{
		$map = array('my-key' => 'MyClass\Does\NotExist');
		$this->initializeRegistry(null, $map);

		$this->factory->createDomainObject('my-key');
	}
}
