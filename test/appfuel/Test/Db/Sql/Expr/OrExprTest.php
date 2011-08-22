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
	Appfuel\Db\Sql\Expr\SqlUnaryExpr,
	Appfuel\Db\Sql\Expr\OrExpr;

/**
 * Test capabilities of the binary expression class
 */
class OrExprTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var BinaryExpr
	 */
	protected $expr = null;
	
	/**
	 * Used as second paramter in constructor
	 * @var string
	 */
	protected $leftop = null;

	/**
	 * Used as second paramter in constructor
	 * @var string
	 */
	protected $rightop = null;


	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->leftop  = new BasicExpr('me');
		$this->rightop = new SqlUnaryExpr('not', 'you');;
		$this->expr    = new OrExpr($this->leftop, $this->rightop);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		unset($this->expr);
		unset($this->leftop);
		unset($this->rightop);
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
        $expected = 'me OR not you';
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
		$expected = 'me OR not you';
		$this->expectOutputString($expected);
		echo $this->expr;
	}
}
