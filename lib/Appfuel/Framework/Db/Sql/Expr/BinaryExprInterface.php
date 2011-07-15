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
 */
interface BinaryExprInterface extends ExprInterface
{
	/**
	 * Operator used in the urnary expression
	 * @return	string
	 */
	public function getOperator();

	/**
	 * @return string | object
	 */
	public function getLeftOperand();
	
	/**
	 * @return	string	| object
	 */
	public function getRightOperand();
	
}
