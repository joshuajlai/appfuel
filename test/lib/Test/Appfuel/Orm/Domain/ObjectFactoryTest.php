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
}
