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

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Sql\Expr\BinaryExprInterface;

/**
 * Simple expression designed to old objects that support to string
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
     * @param   string   $operand
     * @return  File
     */
    public function __construct($leftOp, $operator, $rightOp)
    {
		$this->setOperator($operator);
		$this->setLeftOperand($leftOp);
		$this->setRightOperand($rightOp);
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
		return $this->getOperand();
	}
	
	/**
	 * return the string representing a binary expression
	 * 
	 * @return	string
	 */
	public function build()
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
			throw new Exception($err);
		}

		$this->operator = $op;
	}

	/**
	 * Reuse the basic expressions operand as the left operand
	 *
	 * @param	string | object		$op
	 * @return	null
	 */
	protected function setLeftOperand($op)
	{
		return parent::setOperand($op);
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
            throw new Exception($err);
        }

        $this->rightOp = $op;
	}

}

