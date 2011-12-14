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
namespace TestFuel\Unit\Orm\Repository;

use StdClass,
	Appfuel\Expr\ExprList,
	Appfuel\Orm\Domain\DomainExpr,
	Appfuel\Orm\Repository\Criteria,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary;

/**
 */
class CriteriaTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Criteria
	 */
	protected $criteria = null;
	
	/**
	 * Used to create mock objects in tests
	 * @var string
	 */
	protected $listInterface = 'Appfuel\Expr\ExprListInterface';
	
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
			'Appfuel\Orm\Repository\CriteriaInterface',
			$this->criteria
		);

		$this->assertInstanceOf(
			'Appfuel\DataStructure\DictionaryInterface',
			$this->criteria
		);
	}

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		/* default values */
		$this->assertEquals(array(), $this->criteria->getExprLists());

		$this->assertEquals(0, $this->criteria->count());

		$list = array(
			'list_1' => $this->getMock($this->listInterface),
			'list_2' => $this->getMock($this->listInterface),
		);

		$criteria = new Criteria($list);
		$this->assertEquals($list, $criteria->getExprLists());
	}

	/**
	 * You are allowed to manually set and get the list of named expressions.
	 * Note: you are resposible for making sure each key holds an 
	 * ExprListInterface otherwise addExpr will throw an exception
	 *
	 * @return	null
	 */
	public function testGetSetExprLists()
	{
		$this->assertEquals(array(), $this->criteria->getExprLists());

		$list_1 = $this->getMock($this->listInterface);
		$list_2 = $this->getMock($this->listInterface);
		$list_3 = $this->getMock($this->listInterface);

		$list = array(
			'key_1' => $list_1,
			'key_2' => $list_2,
			'key_3' => $list_3
		);

		$this->assertSame(
			$this->criteria, 
			$this->criteria->setExprLists($list)
		);
		$this->assertEquals($list, $this->criteria->getExprLists());

		/* we can reset the list by setting an empty array */
		$list = array();
		$this->assertSame(
			$this->criteria, 
			$this->criteria->setExprLists($list)
		);
		$this->assertEquals(array(), $this->criteria->getExprLists());

	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testSetExprsNoExprListInterfaceInList()
	{
		$list = array('key_1' => 'value_1');
		$this->criteria->setExprLists($list);
	}

	/**
	 * All keys must have ExprListInterfaces as values
	 *
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testSetExprsMissingExprListInterfaceInList()
	{
		$list_1 = $this->getMock($this->listInterface);
		$list_2 = $this->getMock($this->listInterface);
		$list_3 = $this->getMock($this->listInterface);

		$list = array(
			'key_1' => $list_1,
			'key_2' => $list_2,
			'key_3' => $list_3,
			'key_r' => 'value_1'
		);
		$this->criteria->setExprLists($list);
	}

	/**
	 * In this test we are add an expression to a key that does not exist
	 * so AddExpr will create a new ExprList. Then we add another expr to that
	 * same key and verify that a second expr was infact added
	 *
	 * @return null
	 */
	public function testAddGetIsExprForTheSameKey()
	{
		$this->assertEquals(array(), $this->criteria->getExprLists());
		
		$dExpr_1 = new DomainExpr('user.id = 6');
		$key_1   = 'key_1';
		$this->assertFalse($this->criteria->isExprList($key_1));
		$this->assertFalse($this->criteria->getExprList($key_1));
		
		$this->assertSame(
			$this->criteria,
			$this->criteria->addExpr($key_1, $dExpr_1),
			'must expose a fluent interface'
		);

		$this->assertTrue($this->criteria->isExprList($key_1));
		$resultList = $this->criteria->getExprList($key_1);
		$this->assertInstanceOf($this->listInterface, $resultList);

		/* pull out the current expression and logical operator */
		$result = $resultList->current();
		$this->assertEquals(1, $resultList->count());
		$this->assertSame($dExpr_1, $result[0]);
		$this->assertNull($result[1]);

		/* add another expression to the same expression list */
		$dExpr_2 = new DomainExpr('user.name <> bob');
			$this->assertSame(
			$this->criteria,
			$this->criteria->addExpr($key_1, $dExpr_2),
			'must expose a fluent interface'
		);
		$this->assertTrue($this->criteria->isExprList($key_1));
		
		/* prove second expression was added to the list */
		$this->assertEquals(2, $resultList->count());
		
		/* make sure the correct operator was added to previous expr */
		$result = $resultList->current();
		$this->assertEquals('and', $result[1]);

		/* pull out the current expression and logical operator */
		$resultList->next();
		$result = $resultList->current();
		$this->assertSame($dExpr_2, $result[0]);
		$this->assertNull($result[1]);
	

		$final = $this->criteria->getExprLists();
		$this->assertInternalType('array', $final);
		$this->assertArrayHasKey($key_1, $final);

		$resultList = $final[$key_1];
		$this->assertInstanceOf($this->listInterface, $resultList);
		$this->assertEquals(2, $resultList->count());
	}

	/**
	 * @return null
	 */
	public function testAddExprMoreThanOneKey()
	{
		$list_1 = new ExprList();
		$list_2 = new ExprList();

		$lists = array(
			'list_1' => $list_1,
			'list_2' => $list_2
		);
		
		$this->criteria->setExprLists($lists);
		$this->assertTrue($this->criteria->isExprList('list_1'));
		$this->assertTrue($this->criteria->isExprList('list_2'));

		$dExpr_1 = new DomainExpr('user.id = 6');
		$dExpr_2 = new DomainExpr('user.name <> bob');
		$this->assertSame(
			$this->criteria,
			$this->criteria->addExpr('list_1', $dExpr_1),
			'must expose a fluent interface'
		);

		$this->assertSame(
			$this->criteria,
			$this->criteria->addExpr('list_2', $dExpr_2),
			'must expose a fluent interface'
		);

		$resultList = $this->criteria->getExprLists();
		$this->assertEquals($lists, $resultList);

		$this->assertEquals(1, $list_1->count());
		$this->assertEquals(1, $list_2->count());
		
		$result = $list_1->current();
		$this->assertEquals($dExpr_1, $result[0]);
		$this->assertNull($result[1]);

		$result = $list_2->current();
		$this->assertEquals($dExpr_2, $result[0]);
		$this->assertNull($result[1]);
	}

	/**
	 * Any scalar value can be used a key
	 *
	 * @return null
	 */
	public function testAddExprNumberAsKey()
	{
		$dExpr = new DomainExpr('user.id = 6');
		$this->assertSame(
			$this->criteria,
			$this->criteria->addExpr(44, $dExpr),
			'must use a fluent interface'
		);

		$this->assertTrue($this->criteria->isExprList(44));
		$result = $this->criteria->getExprList(44);
		$this->assertInstanceOf($this->listInterface, $result);
		$this->assertEquals(1, $result->count());

		$data = $result->current();
		$this->assertEquals(array($dExpr, null), $data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testAddExprBadExprArray()
	{
		$expr = new DomainExpr("user.member = something");
		$this->criteria->addExpr(array(1,2,3), $expr);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testAddExprBadExprObj()
	{
		$expr = new DomainExpr("user.member = something");
		$this->criteria->addExpr(new StdClass(), $expr);
	}


	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testAddExprBadKeyEmptyString()
	{
		$expr = new DomainExpr("user.member = something");
		$this->criteria->addExpr('', $expr);
	}
}
