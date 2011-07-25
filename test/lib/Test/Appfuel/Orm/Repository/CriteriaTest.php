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
	Appfuel\Orm\Repository\Criteria;

/**
 */
class CriteriaTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Criteria
	 */
	protected $criteria = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->criteria = new Criteria();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->criteria);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Repository\CriteriaInterface',
			$this->criteria
		);
	}

	/**
	 * This is the domain-key of the target domain for the operation
	 * the criteria represents
	 *
	 * @return null
	 */
	public function testGetSetTargetDomain()
	{
		/* default value is null */
		$this->assertNull($this->criteria->getTargetDomain());

		$key = 'user';
		$this->assertSame(
			$this->criteria, 
			$this->criteria->setTargetDomain($key),
			'must expose a fluent interface'
		);
		$this->assertEquals($key, $this->criteria->getTargetDomain());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetTargetDomainEmptyString()
	{
		$this->criteria->setTargetDomain('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetTargetDomainNumber()
	{
		$this->criteria->setTargetDomain(12345678);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetTargetDomainArray()
	{
		$this->criteria->setTargetDomain(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetTargetDomainObj()
	{
		$this->criteria->setTargetDomain(new StdClass());
	}

	/**
	 * This describes the type of operation the criteria is part of.
	 * For example, we might have a criteria for a database  select
	 * so operationType would be select
	 *
	 * @return null
	 */
	public function testGetSetOperationType()
	{
		/* default value is null */
		$this->assertNull($this->criteria->getOperationType());

		$type = 'select';
		$this->assertSame(
			$this->criteria, 
			$this->criteria->setOperationType($type),
			'must expose a fluent interface'
		);
		$this->assertEquals($type, $this->criteria->getOperationType());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetOpTypeEmptyString()
	{
		$this->criteria->setOperationType('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetOpTypeNumber()
	{
		$this->criteria->setOperationType(12345678);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetOpTypeArray()
	{
		$this->criteria->setOperationType(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetOpTypeObj()
	{
		$this->criteria->setOperationType(new StdClass());
	}
}
