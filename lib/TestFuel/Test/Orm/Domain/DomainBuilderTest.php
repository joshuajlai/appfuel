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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Orm\Domain\DomainBuilder;

/**
 * Test the ability to build out domains
 */
class DomainBuilderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DomainBuilder
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
		$this->map = array(
			'user'		 => "\\TestFuel\Fake\Domain\User\UserDomain",
			'user-email' => "\\TestFuel\Fake\Domain\User\Email\EmailDomain",
			'role'		 => "\\TestFuel\Fake\Domain\\Role\\RoleDomain"
		);


		$this->initializeRegistry(null, $this->map);
		$this->builder = new DomainBuilder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->restoreRegistry();
		$this->builder = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainBuilderInterface',
			$this->builder
		);
	}
}
