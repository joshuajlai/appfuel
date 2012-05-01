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
namespace TestFuel\Unit\Expr;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Expr\ExprList;
	

/**
 * Test capabilities of handling a list of expressions
 */
class ExprTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var BinaryExpr
	 */
	protected $exprList = null;

	/**
	 * Name of the expression inteface class
	 * @var string
	 */
	protected $exprInterface  = 'Appfuel\Expr\ExprInterface';
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->exprList = new ExprList();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		unset($this->exprList);
	}

	/**
	 * @return null
	 */
	public function testHasCorrectInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Expr\ExprListInterface', 
			$this->exprList
		);
	}

	public function testAddGetAll()
	{
		/* default value */
		$this->assertEquals(array(), $this->exprList->getAll());
		
		$expr_1 = $this->getMock($this->exprInterface);
		$expr_2 = $this->getMock($this->exprInterface);
		$expr_3 = $this->getMock($this->exprInterface);
		$expr_4 = $this->getMock($this->exprInterface);
		
		$this->assertSame($this->exprList, $this->exprList->add($expr_1));

		/* the current expression allows has a null for an operator because
		 * its the last expression in the list
		 */	
		$expected = array(
			array($expr_1, null)
		);
		$this->assertEquals($expected, $this->exprList->getAll());
		
		$this->assertSame($this->exprList, $this->exprList->add($expr_2));
		$expected = array(
			array($expr_1, 'and'),
			array($expr_2, null)
		);
		$this->assertEquals($expected, $this->exprList->getAll());

		$this->assertSame(
			$this->exprList, 
			$this->exprList->add($expr_3, 'or')
		);
		$expected = array(
			array($expr_1, 'and'),
			array($expr_2, 'and'),
			array($expr_3, null)
		);
		$this->assertEquals($expected, $this->exprList->getAll());

		$this->assertSame(
			$this->exprList, 
			$this->exprList->add($expr_4, 'AND')
		);
		$expected = array(
			array($expr_1, 'and'),
			array($expr_2, 'and'),
			array($expr_3, 'or'),
			array($expr_4, null)
		);
		$this->assertEquals($expected, $this->exprList->getAll());	
	}

	/**
	 * @return null
	 */
	public function testCurrentNextKeyValidRewind()
	{
		$expr_1 = $this->getMock($this->exprInterface);
		$expr_2 = $this->getMock($this->exprInterface);
		$expr_3 = $this->getMock($this->exprInterface);
		$expr_4 = $this->getMock($this->exprInterface);

		$this->exprList->add($expr_1)
					   ->add($expr_2)
					   ->add($expr_3)
					   ->add($expr_4);
	
		$expected1 = array($expr_1, 'and');
		$expected2 = array($expr_2, 'and');
		$expected3 = array($expr_3, 'and');
		$expected4 = array($expr_4, null);

		$this->assertEquals(0, $this->exprList->key());
		$this->assertTrue($this->exprList->valid());
		$this->assertEquals($expected1, $this->exprList->current());

		$this->assertNull($this->exprList->next());
		$this->assertEquals(1, $this->exprList->key());
		$this->assertTrue($this->exprList->valid());
		$this->assertEquals($expected2, $this->exprList->current());

		$this->assertNull($this->exprList->next());
		$this->assertEquals(2, $this->exprList->key());
		$this->assertTrue($this->exprList->valid());
		$this->assertEquals($expected3, $this->exprList->current());

		$this->assertNull($this->exprList->next());
		$this->assertEquals(3, $this->exprList->key());
		$this->assertFalse($this->exprList->valid());
		$this->assertEquals($expected4, $this->exprList->current());
		
		/* back to the beginning */
		$this->assertNull($this->exprList->rewind());
		$this->assertEquals(0, $this->exprList->key());
		$this->assertTrue($this->exprList->valid());
		$this->assertEquals($expected1, $this->exprList->current());
	}

    /**
     * @expectedException   InvalidArgumentException
     * @return null
     */
    public function testAddFilterBadOperator()
    {
        $expr_1 = $this->getMock($this->exprInterface);

        $op = 'BAD_OPERATOR';
        $this->assertEquals(array(), $this->exprList->getAll());
        $this->exprList->add($expr_1, $op);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @return null
     */
    public function testAddFilterBadOperatorEmptyString()
    {
        $expr_1 = $this->getMock($this->exprInterface);

        $this->assertEquals(array(), $this->exprList->getAll());
        $this->exprList->add($expr_1, '');
    }

    /**
     * @expectedException   InvalidArgumentException
     * @return null
     */
    public function testAddFilterBadOperatorArray()
    {
        $expr_1 = $this->getMock($this->exprInterface);

        $this->assertEquals(array(), $this->exprList->getAll());
        $this->exprList->add($expr_1, array(1,2,3));
    }

    /**
     * @expectedException   InvalidArgumentException
     * @return null
     */
    public function testAddFilterBadOperatorObj()
    {
        $expr_1 = $this->getMock($this->exprInterface);

        $this->assertEquals(array(), $this->exprList->getAll());
        $this->exprList->add($expr_1, new StdClass());
    }


}
