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
	Appfuel\Framework\Expr\ExprList,
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

	/**
	 * @return null
	 */
	public function testGetAddFilters()
	{
		$this->assertEquals(null, $this->criteria->getFilterList());

		$filterList = new ExprList();
		$this->assertSame(
			$this->criteria,
			$this->criteria->setFilterList($filterList)
		);
		$this->assertSame($filterList, $this->criteria->getFilterList());

		$class = 'Appfuel\Framework\Orm\Repository\DomainExprInterface';
		$expr_1 = $this->getMock($class);
		$expr_2 = $this->getMock($class);
		$expr_3 = $this->getMock($class);
		$expr_4 = $this->getMock($class);
		$expr_5 = $this->getMock($class);

		$this->assertSame(
			$this->criteria, 
			$this->criteria->addFilter($expr_1)
		);

		/*
		 * The null indicates that this is the last expression and non others
		 * will follow.
		 */
		$expected = array(
			array($expr_1, null)
		);

		$this->assertEquals($expected, $filterList->getAll());
		
		/* use default logical operator 'and' */
		$this->assertSame(
			$this->criteria, 
			$this->criteria->addFilter($expr_2)
		);
		$expected = array(
			array($expr_1, 'and'),
			array($expr_2, null)
		);
		$this->assertEquals($expected, $filterList->getAll());


		$this->assertSame(
			$this->criteria, 
			$this->criteria->addFilter($expr_3, 'or')
		);

		$expected = array(
			array($expr_1, 'and'),
			array($expr_2, 'and'),
			array($expr_3, null),
		);

		$this->assertEquals($expected, $filterList->getAll());

		/* logical operators are converted to lower case */
		$this->criteria->addFilter($expr_4, 'AND');
		$expected = array(
			array($expr_1, 'and'),
			array($expr_2, 'and'),
			array($expr_3, 'or'),
			array($expr_4, null),
		);
		$this->assertEquals($expected, $filterList->getAll());
			
		$this->criteria->addFilter($expr_5, 'OR');
		$expected = array(
			array($expr_1, 'and'),
			array($expr_2, 'and'),
			array($expr_3, 'or'),
			array($expr_4, 'and'),
			array($expr_5, null),
		);
		$this->assertEquals($expected, $filterList->getAll());	
	}

	/**
	 * This test show now matter what operator is given for the first 
	 * expression it is ignored
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFilterBadOperator()
	{
		$class = 'Appfuel\Framework\Orm\Repository\DomainExprInterface';
		$expr_1 = $this->getMock($class);
	
		$filterList = new ExprList();
		$this->criteria->setFilterList($filterList);


		/* this would normally cause an exception but operator is ignored on
		 * first filter cause its not needed.
		 */
		$op = 'BAD_OPERATOR';
		$this->assertEquals(array(), $filterList->getAll());
		$this->assertSame(
			$this->criteria, 
			$this->criteria->addFilter($expr_1, $op)
		);

		$expected = array(array($expr_1, null));
		$this->assertEquals($expected, $filterList->getAll());	
	}
}
