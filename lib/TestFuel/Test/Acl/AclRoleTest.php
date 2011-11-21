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
namespace TestFuel\Test\Acl;

use StdClass,
	Exception,
	Appfuel\Acl\AclRole,
	TestFuel\TestCase\BaseTestCase;

/**
 * The acl role is a value object that defines a level or authority and is
 * used to authenticate a user agaist a action (action controller). We will
 * test the immutable properties of the AclRole which include name, code,
 * priority and description which is optional
 */
class AclRoleTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	AclRole
	 */
	protected $role = null;

	/**
	 * First param in constructor
	 * @var string
	 */
	protected $roleName = null;

	/**
	 * Second param in constructor
	 * @var string
	 */	
	protected $roleCode = null;

	/**
	 * Third param in constructor
	 * @var string
	 */
	protected $rolePriority = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->roleName = 'Some Administrator';
		$this->roleCode = 'admin';
		$this->rolePriority = 100;
		$this->role = new AclRole(
			$this->roleName,
			$this->roleCode,
			$this->rolePriority
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->role = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Acl\AclRoleInterface',
			$this->role
		);
	}

	/**
	 * @return	array
	 */
	public function provideValidPriorityLevels()
	{
		return	array(
			array(0, 0),
			array(1, 1),
			array(-1, -1),
			array(100, 100),
			array(123456789123456789, 123456789123456789),
			array(-123456789123456789, -123456789123456789),
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidNumbers()
	{
		return array(
			array('I am a string'),
			array(array(1,2,3)),
			array(array()),
			array(array(2)),
			array(new StdClass())
		);
	}

	/**
	 * @depends	testInterface 
	 * @return	null
	 */
	public function testImmutableMembers()
	{
		$this->assertEquals($this->roleName, $this->role->getName());
		$this->assertEquals($this->roleCode, $this->role->getCode());
		$this->assertEquals($this->rolePriority,$this->role->getPriority());
		$this->assertNull($this->role->getDescription());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return				null
	 */
	public function testInvalidName($name)
	{
		$role = new AclRole($name, 'code', 100);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return				null
	 */
	public function testInvalidCode($code)
	{
		$role = new AclRole('name', $code, 100);
	}

	/**
	 * @dataProvider		provideValidPriorityLevels
	 * @depends				testInterface
	 * @return				null
	 */
	public function testPriority($level, $expected)
	{
		$role = new AclRole('name', 'code', $level);

		$this->assertEquals($expected, $role->getPriority());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidNumbers
	 * @depends				testInterface
	 * @return				null
	 */
	public function testPriorityInvalidInt($level, $expected)
	{
		$role = new AclRole('name', 'code', $level);

	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDescription()
	{
		$desc = 'i am some text';
		$role = new AclRole('name', 'code', 100, $desc);
		$this->assertEquals($desc, $role->getDescription());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testInvalidDescription($text)
	{
		$role = new AclRole('name', 'code', 100, $text);
	}
}
