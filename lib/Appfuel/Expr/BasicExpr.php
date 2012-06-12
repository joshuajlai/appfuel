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
 * Simple expression designed to old objects that support to string
 */
class BasicExpr implements ExprInterface
{
	/**
	 * Operand used for this expression
	 * @var string
	 */
	protected $operand = null;

	/**
	 * Flag used to determine if the expr should be wrapped in parentheses
	 * @var bool
	 */
	protected $isParentheses = false;

    /**
     * @param   string   $operand
     * @return  File
     */
    public function __construct($operand, $isParentheses = false)
    {
		$this->setOperand($operand);
		if (true === $isParentheses) {
			$this->enableParentheses();
		}
    }

	/**
	 * @reutrn	BasicExpr
	 */
	public function setParenthesesStatus($flag)
	{
		if (true === $flag) {
			$this->isParentheses = true;
		}
		else {
			$this->isParentheses = false;

		}

		return $this;
	}

	/**
	 * @return	BasicExpr
	 */
	public function enableParentheses()
	{
		return $this->setParenthesesStatus(true);
	}

	/**
	 * @return	BasicExpr
	 */
	public function disableParentheses()
	{
		return $this->setParenthesesStatus(false);
	}

	/**
	 * @return	BasicExpr
	 */
	public function isParentheses()
	{
		return $this->isParentheses;
	}

	/**
	 * @return	mixed string | object
	 */
	public function getOperand()
	{
		return	$this->operand;
	}

	/**
	 * return the string representation of the expression
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

	protected function doBuild()
	{
		return $this->convertToString($this->getOperand());
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->build();
	}

	protected function convertToString($var)
	{
		if (is_scalar($var)) {
			$var = (string)$var;
		}
		else if (is_object($var)) {
			$var = $var->__toString();
		}
		else if (is_array($var)) {
			$var = implode(',', $var);
		}

		return $var;
	}

	/**
	 * @param	mixed	object | scalar
	 * @return	null
	 */
	protected function setOperand($op)
	{
		if (! $this->isValid($op)) {
			$err = 'Invalid operand must be scalar or object with __toString';
			throw new InvalidArgumentException($err);
		}

		$this->operand = $op;
	}

	/**
	 * Both operator and operand share the same validation rule which is:
	 * any no empty scalar value or any object that supports __toString
	 *
	 * @param	mixed	$op
	 * @return	bool
	 */
	protected function isValid($op)
	{		
		if (null === $op) {
			return false;
		}

		if (is_scalar($op)) {
			return true;
		}

		if (is_object($op) && method_exists($op, '__toString')) {
			return true;
		}

		if (is_array($op)) {
			foreach ($op as $item) {
				if (empty($item)) {
					return false;
				}

				if (is_scalar($item)) {
					continue;
				}

				if (is_object($item) && method_exists($item, '__toString')) {
					continue;
				}

				return false;
			}
			return true;
		}

		return false;
	}
}

