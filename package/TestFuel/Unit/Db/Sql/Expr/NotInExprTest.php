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
namespace TestFuel\Test\Db\Sql\Expr;

use StdClass,
	SplFileInfo,
	Appfuel\Db\Sql\Expr\NotInExpr,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\Expr\BasicExpr;

/**
 * Test capabilities of the binary expression class
 */
class NotInExprTest extends BaseTestCase
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
		$expr       = new BasicExpr('letter');
		$contents   = new BasicExpr(array('a', 'b', 'c'));
		$this->expr = new NotInExpr($expr, $contents);
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
        $expected = 'letter NOT IN (a,b,c)';
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
        $expected = 'letter NOT IN (a,b,c)';
		$this->expectOutputString($expected);
		echo $this->expr;
	}
}
