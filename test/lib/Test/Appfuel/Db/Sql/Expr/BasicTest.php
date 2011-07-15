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
namespace Test\Appfuel\Db\Adapter;

use StdClass,
	SplFileInfo,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Sql\Expr\BasicExpr;

/**
 * Test the adapters ability to wrap mysqli
 */
class BasicExprTest extends ParentTestCase
{
    /**
     * @return null
     */
    public function testBasicExprHasCorrectInterface()
    {
		$expr = new BasicExpr('expr');
        $this->assertInstanceOf(
            'Appfuel\Framework\Db\Sql\Expr\ExprInterface',
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


}
