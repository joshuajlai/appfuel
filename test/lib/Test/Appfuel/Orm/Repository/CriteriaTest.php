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
namespace Test\Appfuel\Orm\Repository;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Orm\Domain\DomainExpr,
	Appfuel\Orm\Repository\Criteria,
	Appfuel\Framework\Expr\ExprList,
	Appfuel\Framework\DataStructure\Dictionary;

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
	 * Used to create mock objects in tests
	 * @var string
	 */
	protected $listInterface = 'Appfuel\Framework\Expr\ExprListInterface';
	
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
	 * @return null
	 */
	public function testConstructor()
	{
		/* default values */
		$this->assertEquals(array(), $this->criteria->getExprLists());

		$options = $this->criteria->getOptions();
		$this->assertInstanceOf(
			'Appfuel\Framework\DataStructure\DictionaryInterface',
			$options
		);
		$this->assertEquals(0, $options->count());

		$list = array(
			'list_1' => $this->getMock($this->listInterface),
			'list_2' => $this->getMock($this->listInterface),
		);

		$criteria = new Criteria($list);
		$this->assertEquals($list, $criteria->getExprLists());

		$options = new Dictionary();
		$criteria = new Criteria(null, $options);
		$this->assertSame($options, $criteria->getOptions());

		$criteria = new Criteria($list, $options);
		$this->assertEquals($list, $criteria->getExprLists());
		$this->assertSame($options, $criteria->getOptions());
	}

	/**
	 * @return null
	 */
	public function testGetSetOptions()
	{
		$options = new Dictionary();
		$this->assertNotSame($options, $this->criteria->getOptions());	
		$this->assertSame(
			$this->criteria,
			$this->criteria->setOptions($options),
			'must expose a fluent interface'
		);

		$this->assertSame($options, $this->criteria->getOptions());
	}

	/**
	 * @return null
	 */
	public function testAddOption()
	{
		$options = new Dictionary();
		$this->criteria->setOptions($options);

		$this->assertSame(
			$this->criteria,
			$this->criteria->addOption('my-key', 'my-value'),
			'must expose a fluent interface'
		);
	
		$this->assertEquals(1, $options->count());
		$this->assertTrue($options->exists('my-key'));
		$this->assertEquals('my-value', $options->get('my-key'));	
			
		$this->assertSame(
			$this->criteria,
			$this->criteria->addOption(99, 'other-value'),
			'must expose a fluent interface'
		);
	
		$this->assertEquals(2, $options->count());
		$this->assertTrue($options->exists(99));
		$this->assertTrue($options->exists('my-key'));
		
		$this->assertEquals('other-value', $options->get(99));	
		$this->assertEquals('my-value', $options->get('my-key'));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddOptionBadKeyEmptyString()
	{
		$this->criteria->addOption('', 'my-value');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddOptionBadKeyArray()
	{
		$this->criteria->addOption(array(1,2,3), 'my-value');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddOptionBadKeyObj()
	{
		$this->criteria->addOption(new StdClass(), 'my-value');
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
	 * @expectedException	Appfuel\Framework\Exception
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
	 * @expectedException	Appfuel\Framework\Exception
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
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddExprBadExprArray()
	{
		$expr = new DomainExpr("user.member = something");
		$this->criteria->addExpr(array(1,2,3), $expr);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddExprBadExprObj()
	{
		$expr = new DomainExpr("user.member = something");
		$this->criteria->addExpr(new StdClass(), $expr);
	}


	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddExprBadKeyEmptyString()
	{
		$expr = new DomainExpr("user.member = something");
		$this->criteria->addExpr('', $expr);
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
