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
namespace Appfuel\Expr;

/**
 * Most basic of all expressions
 */
interface ExprInterface
{
	/**
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
     * @return  BasicExpr
     */
    public function enableParentheses();

    /**
     * @return  BasicExpr
     */
    public function disableParentheses();

	/**
	 * Accounts for programatically setting the status when readablity is not
	 * an issue
	 *
	 * @param	bool	$flag
	 * @return	BasicExpr
	 */
	public function setParenthesesStatus($flag);

    /**
	 * Flag used to determine if the expr will be wrapped in parentheses
     * @return  BasicExpr
     */
    public function isParentheses();
	
	/**
	 * magic method to allow expressions to exist in the context of a string
	 *
	 * @return string
	 */
	public function __toString();
}
