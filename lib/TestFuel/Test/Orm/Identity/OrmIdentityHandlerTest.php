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
namespace TestFuel\Test\Orm\Identity;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Orm\Identity\OrmIdentityHandler;

/**
 */
class OrmIdentityHandlerTest extends BaseTestCase
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

	/**
	 * @return	null
	 */
	public function testAddGetIsMapper()
	{
		$key1 = 'memberToColumn';
		$mapper1 = function ($target) {
			return $target;
		};

		$this->assertFalse($this->identity->isMapper($key1));
		$this->assertFalse($this->identity->getMapper($key1));
		$this->assertSame(
			$this->identity,
			$this->identity->addMapper($key1, $mapper1),
			'exposes a fluent interface'
		);

		$this->assertTrue($this->identity->isMapper($key1));
		$this->assertSame($mapper1, $this->identity->getMapper($key1));

		$key2 = 'keyToTable';
		$mapper2 = function ($target) {
			return $target;
		};

		$this->assertFalse($this->identity->isMapper($key2));
		$this->assertFalse($this->identity->getMapper($key2));
		$this->assertSame(
			$this->identity,
			$this->identity->addMapper($key2, $mapper2),
			'exposes a fluent interface'
		);

		$this->assertTrue($this->identity->isMapper($key1));
		$this->assertTrue($this->identity->isMapper($key2));
		$this->assertSame($mapper1, $this->identity->getMapper($key1));
		$this->assertSame($mapper2, $this->identity->getMapper($key2));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddMapperKeyEmptyString()
	{
		$this->identity->addMapper('', function () {return 'blah';});
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddMapperKeyArray()
	{
		$this->identity->addMapper(array(1,2), function () {return 'blah';});
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddMapperKeyInt()
	{
		$this->identity->addMapper(123, function () {return 'blah';});
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddMapperKeyObj()
	{
		$this->identity->addMapper(
			new StdClass(), 
			function () {return 'blah';}
		);
	}
}
