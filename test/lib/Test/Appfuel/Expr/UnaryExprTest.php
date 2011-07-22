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
namespace Test\Appfuel\Expr;

use StdClass,
	SplFileInfo,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Expr\UnaryExpr;

/**
 * Test the adapters ability to wrap mysqli
 */
class UnaryExprTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var UnaryExpr
	 */
	protected $expr = null;
	
	/**
	 * Used as first paramter in constructor
	 * @var	string
	 */	
	protected $operator = null;
	
	/**
	 * Used as second paramter in constructor
	 * @var string
	 */
	protected $operand = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->operator = 'IS NULL';
		$this->operand  = 'my_var';
		$this->expr     = new UnaryExpr($this->operator, $this->operand);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		unset($this->expr);
	}

	/**
	 * @return null
	 */
	public function testUnaryHasCorrectInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Framework\Expr\ExprInterface',
			$this->expr
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\Expr\UnaryExprInterface',
			$this->expr
		);
	}

	/**
	 * @return	null
	 */
	public function testGetSetFixTypeIsPrePostfix()
	{
		/* default value */
		$this->assertEquals('pre', $this->expr->getFixType());
		$this->assertTrue($this->expr->isPrefix());
		$this->assertFalse($this->expr->isPostfix());

		$this->assertNull($this->expr->setFixType('post'));
		$this->assertEquals('post', $this->expr->getFixType());
		$this->assertTrue($this->expr->isPostfix());
		$this->assertFalse($this->expr->isPrefix());

		/* back to pre */
		$this->assertNull($this->expr->setFixType('pre'));
		$this->assertEquals('pre', $this->expr->getFixType());
		$this->assertTrue($this->expr->isPrefix());
		$this->assertFalse($this->expr->isPostfix());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBadTypeSetFixTypeNoPrePost()
	{
		$this->expr->setFixType('string-not-in-white-list');
	}


	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBadTypeSetFixTypeEmptyString()
	{
		$this->expr->setFixType('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBadTypeSetFixTypeNumber()
	{
		$this->expr->setFixType(123456);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBadTypeSetFixTypeArray()
	{
		$this->expr->setFixType(array(123));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testBadTypeSetFixTypeObject()
	{
		$this->expr->setFixType(new StdClass());
	}

	/**
	 * @return null
	 */
	public function testOperator()
	{
		/* this was a string */		
		$this->assertEquals($this->operator, $this->expr->getOperator());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testOperatorInvalidEmptyString()
	{
		$expr = new UnaryExpr('', 'operand');		
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testOperatorInvalidNumber()
	{
		$expr = new UnaryExpr(99, 'operand');		
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testOperatorInvalidArray()
	{
		$expr = new UnaryExpr(array(1,2,3), 'operand');		
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testOperatorInvalidObject()
	{
		$expr = new UnaryExpr(new StdClass(), 'operand');		
	}

	/**
	 * Even objects supporting toString
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testOperatorInvalidObjectWithToString()
	{
		$expr = new UnaryExpr(new SplFileInfo('failed'), 'operand');		
	}

	/**
	 * @return	null
	 */
	public function testBuildPrefix()
	{
		$this->assertTrue($this->expr->isPrefix());

		$result = $this->expr->buildPrefix();
		$expected = 'IS NULL my_var';
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return	null
	 */
	public function testBuildPostfix()
	{
		$this->expr->setFixType('post');

		$result = $this->expr->buildPostfix();
		$expected = 'my_var IS NULL';
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return null
	 */
	public function testBuildUsingBuildPrefix()
	{
		$this->assertTrue($this->expr->isPrefix());

		$result = $this->expr->build();
		$expected = 'IS NULL my_var';
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return null
	 */
	public function testBuildUsingBuildPostfix()
	{
		$this->expr->setFixType('post');

		$result = $this->expr->build();
		$expected = 'my_var IS NULL';
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return null
	 */
	public function testToStringPrefix()
	{
		$this->assertTrue($this->expr->isPrefix());
		$this->expectOutputString('IS NULL my_var');
		
		echo $this->expr;
	}
	
	/**
	 * @return null
	 */
	public function testDefaultValueIsParentheses()
	{
		$this->assertFalse($this->expr->isParentheses());
	}

    /**
     * @return null
     */
    public function testIsEnableDisableParentheses()
    {
		$expected = "(IS NULL my_var)";
		$this->expr->enableParentheses();
		$this->assertEquals($expected, $this->expr->build());

		$expected = "IS NULL my_var";
		$this->expr->disableParentheses();
		$this->assertEquals($expected, $this->expr->build());
    }
}
