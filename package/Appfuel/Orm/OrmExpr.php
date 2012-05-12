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
namespace Appfuel\Orm;

use InvalidArgumentException,
	Appfuel\Expr\BinaryExpr,
	Appfuel\Orm\Domain\DomainExprInterface;	

/**
 */
class OrmExpr extends BinaryExpr implements DomainExprInterface
{
	/**
	 * @var string
	 */
	protected $domain = null;

	/**
	 * Domain member
	 * @var string
	 */
	protected $member = null;

	/**
	 * Operator used int the expression
	 * @var string
	 */
	protected $op = null;

	/**
	 * @var mixed
	 */
	protected $value = null;

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
    public function __construct($domainStr, $op, $value)
    {
		$this->setDomain($domainStr);
		$this->setOperator($op);
		$this->setValue($value);
    }

	/**
	 * @return	string
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * Alias operand for better readability. The domain member is always the 
	 * operand on the left hand side of a binary expression
	 * 
	 * @return	string
	 */
	public function getMember()
	{
		return $this->member;
	}

	/**
	 * @return	string
	 */
	public function getOperator()
	{
		return $this->op;
	}

	/**
	 * Merely improves readablity. The value of a domain expression is always
	 * the operand on the right side.
	 * 
	 * @return	mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * @param	string	$domainKeu
	 * @return	null
	 */
	protected function setDomain($str)
	{
		if (empty($str) || ! is_string($str)) {
			$err = "Invalid domain must non empty string";
			throw new InvalidArgumentException($err);
		}

		if (false === strpos($str, '.')) {
			$err  = "domain string must be in the following format ";
			$err .= "-(domain.member)";
			throw new InvalidArgumentException($err);
		}
		$parts = explode('.', $str);
		
		$this->domain = current($parts);
		$this->member = next($parts);
	}

	public function setOperator($op)
	{
		if (empty($op) || ! is_string($op)) {
			$err = "operator must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->op = $op;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}
}
