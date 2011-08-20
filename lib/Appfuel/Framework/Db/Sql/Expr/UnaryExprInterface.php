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
interface UnaryExprInterface extends ExprInterface
{
	/**
	 * Operator used in the urnary expression
	 * @return	string
	 */
	public function getOperator();

	/**
	 * Used to deteremine which build to use
	 * 
	 * @param	string	$type
	 * @return	null
	 */
	public function setFixType($type);

	/**
	 * @return string
	 */
	public function getFixType();

	/**
	 * @return bool
	 */
	public function isPrefix();

	/**
	 * @return bool
	 */
	public function isPostfix();

	/**
	 * Build expression a postfix
	 * 
	 * @return string
	 */
	public function buildPostfix();
	
	/**
	 * Build expression as prefix
	 *
	 * @return	string
	 */
	public function buildPrefix();	
}
