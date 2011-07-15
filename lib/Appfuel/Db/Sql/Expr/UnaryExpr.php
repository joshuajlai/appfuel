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
	Appfuel\Framework\Db\Sql\Expr\UnaryExprInterface;

/**
 * Simple expression designed to old objects that support to string
 */
class UnaryExpr extends BasicExpr implements UnaryExprInterface
{
	/**
	 * Operand used for this expression
	 * @var string
	 */
	protected $operand = null;

	/**
	 * Operator used in the expression
	 * @var string
	 */
	protected $operator = null;

	/**
	 * Determines if the expression is build as prexfix or postfix
	 * @var string
	 */
	protected $fixType = 'pre';
    
	/**
     * @param   string   $operand
     * @return  File
     */
    public function __construct($operator, $operand)
    {
		$this->setOperator($operator);
		$this->setOperand($operand);
    }

	/**
	 * @param	string	$type	pre|post
	 * @return	null
	 */
	public function setFixType($type)
	{
		$err = 'fix type must be (pre|post)';
		if (! is_string($type) || empty($type)) {
			throw new Exception("$err param given is empty or not a string");
		}

		if (! in_array($type, array('pre', 'post'))) {
			throw new Exception("$err param given is ($type)");
		}

		$this->fixType = $type;
	}

	/**
	 * @return	string
	 */
	public function getFixType()
	{
		return $this->fixType;	
	}

	/**
	 * @return	bool
	 */
	public function isPrefix()
	{
		return 'pre' === $this->fixType; 
	}

	/**
	 * @return	bool
	 */
	public function isPostfix()
	{
		return 'post' === $this->fixType; 
	}

	/**
	 * @return	mixed string | object
	 */
	public function getOperator()
	{
		return $this->operator;
	}
	
	/**
	 * return the string representing a unary expression based on
	 * the fix type
	 * 
	 * @return	string
	 */
	public function build()
	{
		if ($this->isPrefix()) {
			return $this->buildPrefix();
		}

		return $this->buildPostfix();
	}

	/**
	 * Build a postfix expression
	 *
	 * @return string
	 */
	public function buildPostfix()
	{
		return parent::build() . ' ' . $this->getOperator();
	}

	/**
	 * Build a prefix expression
	 *
	 * @return string
	 */
	public function buildPrefix()
	{
		return $this->getOperator() . ' ' . parent::build();
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

}

