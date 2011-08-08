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
	Appfuel\Orm\Domain\ObjectFactory;

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
			'user-email' => "$path\User\Email",
			'role'		 => "$path\\Role",
		);


		$this->initializeRegistry(array('domain-keys' => $this->map));
		$this->factory = new ObjectFactory();
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
}
