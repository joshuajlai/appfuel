<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Framework\Db\Sql\Expr;

/**
 * Functionality needed by all sql expressions
 */
interface ExprInterface
{
	/**
	 * In simple and unary expressions this is the only operand but with
	 * binary expressions it is used as the left most operand
	 * 
	 * @return	mixed	string | object
	 */
	public function getOperand();

	/**
	 * Turns the expression into a string
	 *
	 * @return	string
	 */
	public function build();
	
	/**
	 * magic method to allow expressions to exist in the context of a string
	 *
	 * @return string
	 */
	public function __toString();
}
