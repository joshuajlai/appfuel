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
	Appfuel\Orm\Domain\DbIdentity;

/**
 * Db Identity maps database properties like table, columns, primary keys to
 * to domain members
 */
class DbIdentityTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var DbIdentity
	 */
	protected $identity = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->identity = new DbIdentity();
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
			'Appfuel\Framework\Orm\Domain\DbDomainIdentityInterface',
			$this->identity
		);
	}

	/**
	 * The map is an associative array of domain members to database columns.
	 * It is expected that every domain member has a valid database columm
	 *
	 * @return null
	 */
	public function testGetSetMap()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => 'last_name',
			'email'		=> 'system_email'
		);

		/* default value */
		$this->assertEquals(array(), $this->identity->getMap());

		$this->assertSame(
			$this->identity,
			$this->identity->setMap($map)
		);
		$this->assertSame($map, $this->identity->getMap());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapEmptyMap()
	{
		$this->identity->setMap(array());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapEmptyColumn()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => '',
			'email'		=> 'system_email'
		);
		$this->identity->setMap($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapNumericColumn()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => 999,
			'email'		=> 'system_email'
		);
		$this->identity->setMap($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapArrayColumn()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => array(1,2,3),
			'email'		=> 'system_email'
		);
		$this->identity->setMap($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapObjectColumn()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => new StdClass(),
			'email'		=> 'system_email'
		);
		$this->identity->setMap($map);
	}

	/**
	 * Currently the only info an identity needs about a database table is 
	 * the table name
	 *
	 * @return null
	 */
	public function testGetSetTable()
	{
		$this->assertNull($this->identity->getTable());

		$table = 'users';
		$this->assertSame(
			$this->identity,
			$this->identity->setTable($table)
		);
		$this->assertEquals($table, $this->identity->getTable());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTableEmptyString()
	{
		$this->identity->setTable('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTableNumberic()
	{
		$this->identity->setTable(9999);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTableArray()
	{
		$this->identity->setTable(array(1,2,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTableObject()
	{
		$this->identity->setTable(new StdClass());
	}

	/**
	 * @return null
	 */
	public function testGetSetLabel()
	{
		$this->assertNull($this->identity->getLabel());

		$label = 'user';
		$this->assertSame(
			$this->identity,
			$this->identity->setLabel($label)
		);
		$this->assertEquals($label, $this->identity->getLabel());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetLabelEmptyString()
	{
		$this->identity->setLabel('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetLabelNumberic()
	{
		$this->identity->setLabel(9999);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetLabelArray()
	{
		$this->identity->setLabel(array(1,2,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetLabelObject()
	{
		$this->identity->setLabel(new StdClass());
	}

	/**
	 * @return null
	 */
	public function testGetSetPrimaryKeySingleKey()
	{
		$map = array(
			'user_id'		=> 'id',
			'system_name'	=> 'userName',
			'first_name'	=> 'firstName',
			'last_name'		=> 'lastName'
		);
		$this->identity->setMap($map);

		$key = array('user_id');
		$this->assertEquals(array(), $this->identity->getPrimaryKey());
		$this->assertSame(
			$this->identity,
			$this->identity->setPrimarykey($key)
		);
		$this->assertEquals($key, $this->identity->getPrimaryKey());
	}

	/**
	 * @return null
	 */
	public function testGetSetPrimaryKeyCompoundKey()
	{
		$map = array(
			'user_id'		=> 'id',
			'customer_id'   => 'customerId',
			'system_name'	=> 'userName',
			'first_name'	=> 'firstName',
			'last_name'		=> 'lastName'
		);
		$this->identity->setMap($map);

		$key = array('user_id', 'customer_id');
		$this->assertEquals(array(), $this->identity->getPrimaryKey());
		$this->assertSame(
			$this->identity,
			$this->identity->setPrimarykey($key)
		);
		$this->assertEquals($key, $this->identity->getPrimaryKey());
	}


	/**
	 * All columns in the key must be mapped prior to setting the key
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetPrimaryKeyNotMapped()
	{
		$this->assertEquals(array(), $this->identity->getMap());
		$this->identity->setPrimaryKey(array('user_id'));
	}

	/**
	 * Since setMap guards against invalid strings and setPrimaryKey 
	 * requires a mapped string all invalid strings will fail because 
	 * they can not be mapped
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetPrimaryKeyEmptyString()
	{
		$this->assertEquals(array(), $this->identity->getMap());
		$this->identity->setPrimaryKey(array(''));
	}



}
