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
namespace Test\Appfuel\Orm\Source\Db\Identity;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Orm\Identity\OrmIdentityHandler;

/**
 */
class OrmIdentityHandlerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var OrmIdentityHandler
	 */
	protected $identity = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->identity = new OrmIdentityHandler();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->identity);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Identity\IdentityHandlerInterface',
			$this->identity
		);
	}

	/**
	 * An Identity can only have one domain name which is the label that
	 * refers to the domain model that identity is resolving for
	 *
	 * @return null
	 */
	public function testGetSetDomainName()
	{
		/* default value */
		$this->assertNull($this->identity->getDomainName());
		
		$name = 'role';
		$this->assertSame(
			$this->identity,
			$this->identity->setDomainName($name),
			'exposes a fluent interface'
		);
		$this->assertEquals($name, $this->identity->getDomainName());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDomainNameInt()
	{
		$this->identity->setDomainName(12233);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDomainNameEmptyString()
	{
		$this->identity->setDomainName('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDomainNameArray()
	{
		$this->identity->setDomainName(array(1,22,33));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDomainNameObject()
	{
		$this->identity->setDomainName(new StdClass());
	}

	/**
	 * @return	null
	 */
	public function testGetSetRootNamespace()
	{
		$this->assertNull($this->identity->getRootNamespace());
		
		$ns = 'Appfuel\Domain';
		$this->assertSame(
			$this->identity,
			$this->identity->setRootNamespace($ns),
			'exposes a fluent interface'
		);

		$this->assertEquals($ns, $this->identity->getRootNamespace());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRootNamespaceInt()
	{
		$this->identity->setRootNamespace(12233);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRootNamespaceEmptyString()
	{
		$this->identity->setRootNamespace('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRootNamespaceArray()
	{
		$this->identity->setRootNamespace(array(1,22,33));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRootNamespaceObject()
	{
		$this->identity->setRootNamespace(new StdClass());
	}
}
