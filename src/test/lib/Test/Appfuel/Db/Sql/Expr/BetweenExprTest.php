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
namespace Test\Appfuel\Db\Sql\Expr;

use StdClass,
	SplFileInfo,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Framework\Expr\BasicExpr,
	Appfuel\Framework\Expr\BinaryExpr,
	Appfuel\Db\Sql\Expr\SqlUnaryExpr,
	Appfuel\Db\Sql\Expr\BetweenExpr;

/**
 * Test capabilities of the binary expression class
 */
class BetweenExprTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var BinaryExpr
	 */
	protected $expr = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->expr = new BetweenExpr(
			new BasicExpr('my.age'),
			new BasicExpr(9),
			new BasicExpr(18)
		);
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
			'Appfuel\Framework\Expr\BinaryExprInterface',
			$this->expr
		);
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
	public function testBuild()
	{
		$expected = 'my.age BETWEEN 9 AND 18';
		$this->assertEquals($expected, $this->expr->build());
	
		$this->assertSame($this->expr, $this->expr->enableParentheses());
		$this->assertEquals("($expected)", $this->expr->build());
		
		$this->assertSame($this->expr, $this->expr->disableParentheses());
		$this->assertEquals($expected, $this->expr->build());
	}

	/**
	 * @return null
	 */
	public function testToString()
	{
		$expected = 'my.age BETWEEN 9 AND 18';
		$this->expectOutputString($expected);
		echo $this->expr;
	}
}
