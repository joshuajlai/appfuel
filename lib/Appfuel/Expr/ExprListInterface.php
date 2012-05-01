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

use Countable,
	Iterator;
/**
 * Most basic of all expressions
 */
interface ExprListInterface extends Countable, Iterator
{
	/**
	 * Returns an array of all expressions each item is an array with the
	 * expression and logical operator that joins the next expression. The
	 * last expression has no operator
	 *
	 * @return	array
	 */
	public function getAll();
	

	/**
	 * Add an expression to the list. Last expression has no logical operator.
	 * The logical operator is always applied to the previous expression and
	 * is ignored in the case of the first expressions
	 *
	 * @throws	Appfuel\Framework\Exception
	 * @param	ExprInterface	$expr
	 * @param	string			$logical	valid operators are (and|or)
	 * @return	ExprListInterface
	 */
	public function add(ExprInterface $expr, $logical = 'and');
	
}
