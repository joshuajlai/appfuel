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
namespace Appfuel\Expr;

use InvalidArgumentException;

/**
 * Binary expression with left and right operands
 */
class BinaryExpr extends BasicExpr implements BinaryExprInterface
{
	/**
	 * Operand used on right handside of the expression
	 * @var string
	 */
	protected $rightOp = null;

	/**
	 * Operator used in the expression
	 * @var string
	 */
	protected $operator = null;

	/**
	 * Because we are extending the BasicExpr our left operand is its only
	 * only operand so we will reuse that and only write a setter for the
	 * right
	 *
     * @param   string | object		$leftOp
	 * @param	string				$operator
	 * @parma	string | object		$rightOp
	 * @param	bool				$isPar		flag or using parentheses
     * @return  BinaryExpr
     */
    public function __construct($leftOp, $operator, $rightOp, $isPar = false)
    {
		$this->setOperator($operator);
		$this->setRightOperand($rightOp);

		parent::__construct($leftOp, $isPar);
    }

	/**
	 * @return	mixed string | object
	 */
	public function getOperator()
	{
		return $this->operator;
	}

	/**
	 * @return	string | object
	 */
	public function getRightOperand()
	{
		return $this->rightOp;
	}

	/**
	 * @return	string | object
	 */
	public function getLeftOperand()
	{
		return $this->operand;
	}
	
	/**
	 * return the string representing a binary expression
	 * 
	 * @return	string
	 */
	public function build()
	{
		$str = $this->doBuild();
		if ($this->isParentheses()) {
			$str = "($str)";
		}

		return $str;
	}

	/**
	 * @return	string
	 */
	protected function doBuild()
	{
		return $this->buildLeftOperand() . ' ' . 
			   $this->getOperator()      . ' ' .
			   $this->buildRightOperand();
	}

	/**
	 * @return string
	 */
	protected function buildLeftOperand()
	{
		return $this->convertToString($this->getLeftOperand());
	}

	/**
	 * @return string
	 */
	protected function buildRightOperand()
	{
		return $this->convertToString($this->getRightOperand());
	}

	/**
	 * @param	mixed	object | scalar
	 * @return	null
	 */
	protected function setOperator($op)
	{
		if (empty($op) || ! is_string($op)) {
			$err = 'Invalid operator must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->operator = $op;
	}

	/**
	 * Reuse the basic expressions operand as the left operand
	 *
	 * @param	string | object		$op
	 * @return	null
	 */
	protected function setRightOperand($op)
	{
        if (! $this->isValid($op)) {
            $err = 'Invalid operand must be scalar or object with __toString';
            throw new InvalidArgumentException($err);
        }

        $this->rightOp = $op;
	}
}

