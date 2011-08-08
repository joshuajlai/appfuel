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
	Appfuel\Orm\Domain\ObjectFactory;

/**
 * Test the ablity of the object factory to pull a domain key mapping from
 * the registry convert it to fully qualifed namespace and instantiate it
 */
class ObjectFactoryTest extends ParentTestCase
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
		$this->backupRegistry();
		$path = __NAMESPACE__ . '\ObjectFactory';
		$this->map = array(
			'user'		 => "$path\User",
			'user-email' => "$path\User\Email",
			'role'		 => "$path\\Role",
			'non-domain' => "$path\\CustomDomainObject"
		);


		$this->initializeRegistry(array('domain-keys' => $this->map));
		$this->factory = new ObjectFactory();
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
			'Appfuel\Framework\Orm\Domain\ObjectFactoryInterface',
			$this->factory
		);
	}

	/**
	 * This test will use createDomainObject with the second param is 
	 * missing giving a default of isDomain = true, meaning this domain
	 * will be instantiated through naming convention
	 *
	 * @return null
	 */
	public function testCreateDomainObject()
	{
		$result = $this->factory->createDomainObject('user');
		
		$qualifiedClass = $this->map['user'] . '\UserModel';
		$this->assertInstanceOf($qualifiedClass, $result);

		$result = $this->factory->createDomainObject('user-email');
		$qualifiedClass = $this->map['user-email'] . '\EmailModel';
		$this->assertInstanceOf($qualifiedClass, $result);

		/* second parameter is same as default */
		$result = $this->factory->createDomainObject('role', true);
		$qualifiedClass = $this->map['role'] . '\RoleModel';
		$this->assertInstanceOf($qualifiedClass, $result);	
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
		$this->initializeRegistry(array());
		$this->assertFalse($this->factory->createDomainObject('user'));
		$this->assertFalse($this->factory->createDomainObject('user-email'));
		$this->assertFalse($this->factory->createDomainObject('role'));
		$this->assertFalse($this->factory->createDomainObject('non-domain'));
		$this->assertFalse($this->factory->createDomainObject('random-key'));	
	}

	/**
	 * method will return false for anything that is not an array
	 *
	 * @return null
	 */
	public function testCreateDomainObjectMapExistIsString()
	{
		$this->initializeRegistry(array('domain-keys'=>'not correct'));
		$this->assertFalse($this->factory->createDomainObject('user'));
	}

	/**
	 * method will return false for anything that is not an array
	 *
	 * @return null
	 */
	public function testCreateDomainObjectMapExistIsInt()
	{
		$this->initializeRegistry(array('domain-keys'=> 12345));
		$this->assertFalse($this->factory->createDomainObject('user'));
	}

	/**
	 * method will return false for anything that is not an array
	 *
	 * @return null
	 */
	public function testCreateDomainObjectMapExistIsObject()
	{
		$this->initializeRegistry(array('domain-keys'=> new StdClass()));
		$this->assertFalse($this->factory->createDomainObject('user'));
	}

	/**
	 * method will return false for anything that is not an array even for
	 * empty arrays
	 *
	 * @return null
	 */
	public function testCreateDomainObjectMapExistEmptyArray()
	{
		$this->initializeRegistry(array('domain-keys'=> array()));
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
		$this->initializeRegistry(array('domain-keys' => $map));

		$this->factory->createDomainObject('my-key');
	}
}
