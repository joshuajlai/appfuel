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
namespace TestFuel\Test\Framework\Expr;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\Expr\BasicExpr;

/**
 * A generic expression can do three things 
 * 1) retrieve its operand
 * 2) build its self into a string/use itself in the context of a string
 * 3) add parentheses to itself
 */
class BasicExprTest extends BaseTestCase
{
    /**
     * @return null
     */
    public function testBasicExprHasCorrectInterface()
    {
		$expr = new BasicExpr('expr');
        $this->assertInstanceOf(
            'Appfuel\Framework\Expr\ExprInterface',
            $expr
        );
    }

	/**
	 * @return null
	 */
	public function testScalarExprGetOperand()
	{
		$operand = 9;
		$expr = new BasicExpr($operand);
		$this->assertEquals($operand, $expr->getOperand());

		$operand = 'this is a string';
		$expr = new BasicExpr($operand);
		$this->assertEquals($operand, $expr->getOperand());
	
		$operand = 9.99;
		$expr = new BasicExpr($operand);
		$this->assertEquals($operand, $expr->getOperand());
	}

	/**
	 * @return null
	 */
	public function testArrayExpr()
	{
		$operand = array(1,2,3,4);
		$expr = new BasicExpr($operand);
		$expected = '1,2,3,4';
		$this->assertEquals($expected, $expr->build());

		$expr = new BasicExpr($operand, true);
		$expected = '(1,2,3,4)';
		$this->assertEquals($expected, $expr->build());
	
		$operand = array(new BasicExpr(123),2,3,new BasicExpr('abc'));
		$expr = new BasicExpr($operand);
		$expected = '123,2,3,abc';
		$this->assertEquals($expected, $expr->build());
	}

	/**
	 * @return null
	 */
	public function testObjectSupportingToStringGetOperand()
	{
		$operand = new SplFileInfo('blah.txt');
		$this->assertTrue(method_exists($operand, '__toString'));

		$expr = new BasicExpr($operand);
		$this->assertSame($operand, $expr->getOperand());

		$operand = new BasicExpr('User.LastName');
		$expr = new BasicExpr($operand);
		$this->assertSame($operand, $expr->getOperand());
	}

	/**
	 * @return null
	 */
	public function testBuildScalar()
	{
		$operand = 9;
		$expr = new BasicExpr($operand);
		
		$result = $expr->build();
		$this->assertInternalType('string', $result);
		$this->assertEquals($operand, $result);

		$operand = 'this is a string';
		$expr = new BasicExpr($operand);
		
		$result = $expr->build();
		$this->assertInternalType('string', $result);
		$this->assertEquals($operand, $result);

		$operand = 9.99;
		$expr = new BasicExpr($operand);
		
		$result = $expr->build();
		$this->assertInternalType('string', $result);
		$this->assertEquals($operand, $result);	
	}

	public function testBuildExprObject()
	{
		$operand = new SplFileInfo('blah.txt');
		$expr = new BasicExpr($operand);

		$result = $expr->build();
		$this->assertInternalType('string', $result);
		$this->assertEquals('blah.txt', $result);

		$operand = new BasicExpr('This is a string');
		$expr = new BasicExpr($operand);

		$result = $expr->build();
		$this->assertInternalType('string', $result);
		$this->assertEquals('This is a string', $result);
		$this->assertEquals($operand->build(), $result);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testObjectDoesNotSupportToString()
	{	
		$operand = new StdClass();
		$this->assertFalse(method_exists($operand, '__toString'));
	
		$expr = new BasicExpr($operand);	
	}
	
	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testBasicExprEmptyString()
	{	
		$expr = new BasicExpr('');	
	}

    /**
     * @return null
     */
    public function testToString()
    {
		$expr = new BasicExpr('my_var');
        $this->expectOutputString('my_var');

        echo $expr;
    }

	/**
     * @return null
     */
    public function testToStringNumber()
    {
		$expr = new BasicExpr(99);
        $this->expectOutputString('99');

        echo $expr;
    }
	
	/**
     * @return null
     */
    public function testToStringFloat()
    {
		$expr = new BasicExpr(9.99);
        $this->expectOutputString('9.99');

        echo $expr;
    }

	/**
     * @return null
     */
    public function testToStringObject()
    {
		$op = new BasicExpr(9.99);
		$expr = new BasicExpr($op);

        $this->expectOutputString('9.99');

        echo $expr;
    }

	/**
	 * @return null
	 */
	public function testIsEnableDisableParentheses()
	{
		$op = 'my value';
		$expr = new BasicExpr($op);
		$this->assertFalse($expr->isParentheses());

		$this->assertSame($expr, $expr->enableParentheses());
		$this->assertTrue($expr->isParentheses());

		$expected = "($op)";
		$this->assertEquals($expected, $expr->build());

		$this->assertSame($expr, $expr->disableParentheses());
		$this->assertFalse($expr->isParentheses());
		$this->assertEquals($op, $expr->build());

		$expr = new BasicExpr($op, true);
		$this->assertTrue($expr->isParentheses());
		$this->assertEquals($expected, $expr->build());
	}
}
