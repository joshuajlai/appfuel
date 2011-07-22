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
namespace Appfuel\Db\Sql\Expr;

use Appfuel\Expr\UnaryExpr;

/**
 * Simple expression designed to old objects that support to string
 */
class SqlUnaryExpr extends UnaryExpr
{
	/**
	 * Build a postfix expression
	 *
	 * @return string
	 */
	public function buildPostfix()
	{
		$operand = $this->getOperand();
		return  $this->convertToString($operand). ' ' . $this->getOperator();
	}

	/**
	 * Build a prefix expression
	 *
	 * @return string
	 */
	public function buildPrefix()
	{
		$operand = $this->getOperand();
		return  $this->getOperator() . ' ' . $this->convertToString($operand);
	}
}
