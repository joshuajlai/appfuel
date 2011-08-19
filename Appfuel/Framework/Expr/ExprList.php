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
namespace Appfuel\Framework\Expr;

use Appfuel\Framework\Exception;

/**
 * The criteria holds any information necessary for the sql factory to build
 * the correct sql for the db request to pull domain information down from 
 * the database
 */
class ExprList implements ExprListInterface
{
	/**
	 * Operator used when joining to expressions
	 * @var string
	 */
	protected $currentOp = null;

	/**
	 * Holds a list of expressions
	 * @var array
	 */
	protected $exprs = array();

	/**
	 * @return	array
	 */
	public function getAll()
	{
		return $this->exprs;
	}

	/**
	 * Adds a domain expression to the filter stack. The current expression
	 * always has null for its logical operator because it can not not what
	 * future condition to join to. The second parameter is always used to
	 * join to the previous filter accept in the case of the first filter 
	 * where it is ignored.
	 *
	 * @throws	Appfuel\Framework\Exception
	 * @param	DomainExpr	$expr
	 * @param	string		$logical	join with previous filter with and|or
	 * @return	Criteria
	 */
	public function add(ExprInterface $expr, $logical = null)
	{
		/* used to join to the previous expression to this */
		$currentOp = $this->getCurrentOp();
			
		/* default operator used to join the next expression */
		if (null === $logical) {
			$logical = 'and';	
		}
		$this->setCurrentOp($logical);

	
		$last = $this->count() - 1;
		$this->exprs[] = array($expr, null);
		if ($last >= 0) {
			$this->exprs[$last][1] = $currentOp;
		}
			

		return $this;
	}

	/**
	 * Returns a logical operator used to join the next expression.
	 *
	 * @return	string | null 
	 */
	protected function getCurrentOp()
	{
		return $this->currentOp;
	}

	protected function setCurrentOp($op)
	{
		if (empty($op) || ! is_string($op)) {
			throw new Exception("operator must be (and|or) can not be empty");
		}

		$op = strtolower($op);
		if (! in_array($op, array('and', 'or'))) {
			throw new Exception("add filter failed 2nd param  must be and|or");
		}
	
		$this->currentOp = $op;
	}

	/**
	 * returns the number of expressions in the list
	 * @return	int
	 */
	public function count()
	{
		return count($this->exprs);
	}

	/**
	 * returns the current item in the expr list
	 *
	 * @return array
	 */
	public function current()
	{
		return current($this->exprs);
	}

	/**
	 * @return the current key of the list
	 */
	public function key()
	{
		return key($this->exprs);
	}


	/**
	 * move forward to the next expression
	 * 
	 * @return	null
	 */
	public function next()
	{
		next($this->exprs);
	}

	/**
	 *  Rewind the list to the first exprssion
	 * 
	 * @return null
	 */
	public function rewind()
	{
		reset($this->exprs);
	}

	/**
	 * A valid item consists of the current item being an array with two 
	 * elements with keys 0 and 1. Key 0 implements the ExprInterface and
	 * key 1 is either null or (and|or)
	 *
	 * @return bool
	 */
	public function valid()
	{
		$data = $this->current();
		return is_array($data) && isset($data[0]) && isset($data[1]) &&
			   $data[0] instanceof ExprInterface  &&
			   (in_array($data[1], array('and', 'or')) || is_null($data[1]));
	}
}
