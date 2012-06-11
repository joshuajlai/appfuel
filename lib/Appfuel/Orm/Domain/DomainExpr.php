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
namespace Appfuel\Orm\Domain;

use InvalidArgumentException,
	Appfuel\Expr\BinaryExpr;

/**
 * Allows a simple domain expression to given as a string in the constructor
 * and parsed into its components. This is used by the repository to capture
 * domain expressions and translate them into database expressions usually 
 * ending up in the where clause of a sql statement.
 */
class DomainExpr extends BinaryExpr implements DomainExprInterface
{
	/**
	 * @var string
	 */
	protected $domain = null;

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
    public function __construct($expr)
    {
		$result = $this->parseExpr($expr);
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
		return parent::getLeftOperand();
	}

	/**
	 * Merely improves readablity. The value of a domain expression is always
	 * the operand on the right side.
	 * 
	 * @return	mixed
	 */
	public function getValue()
	{
		return parent::getRightOperand();
	}
	
	/**
	 * Parse the expression into its components. The following explains
	 * the regex
	 *
	 * domain	=> this is one or more letters numbers -_
	 * member	=> also one or more letters numbers -_
	 * operator => this one is tricky we have the arithmetic operators plus
	 *			   things is, is not, in, not in, between, not between
	 *			   I match very loosely and validate the proper operators 
	 *			   from a white list
	 * @return null
	 */
	protected function parseExpr($expr)
	{
		$pattern  = "/(?<domain>([-_a-zA-Z0-9]+))\.";
		$pattern .= "(?<member>([-_a-zA-Z0-9]+))\s*";
		$pattern .= "(?<operator>";
		$pattern .= "((not )?(=|<>|<|<=|>|>=|is|in|between)( not)?))\s*";
		$pattern .= "(?<value>([.-_,a-zA-Z0-9 ]+))/i";

		$match  = array();
		preg_match_all($pattern, $expr, $match);

		$err = "Failed to parse domain expression: ";
		if (! array_key_exists('domain', $match) || empty($match['domain'])) {
			$err .= 'could not find domain';
			throw new InvalidArgumentException($err);
		}

		if (! array_key_exists('member', $match)) {
			$err .= 'could not find domain member';
			throw new InvalidArgumentException($err);
		}
		if (! array_key_exists('operator', $match)) {
			$err .= 'could not find expression operator';
			throw new InvalidArgumentException($err);
		}

		if (array_key_exists('value', $match)) {
			$this->setRightOperand(current($match['value']));
		}

		$validOps = array('<','=','<=','<>','>','>=',
						  'is','is not','in','not in',
						  'between','not between');

		$op = strtolower(trim(current($match['operator'])));
		if (! in_array($op, $validOps, true)) {
			$err .= 'invalid operator given';
			throw new InvalidArgumentException($err);
		}

		$this->setMember(current($match['member']));
		$this->setDomain(current($match['domain']));
		$this->setOperator(current($match['operator']));
	}

	/**
	 * @param	string	$domainKeu
	 * @return	null
	 */
	protected function setDomain($domainKey)
	{
		if (empty($domainKey) || ! is_string($domainKey)) {
			$err = "Invalid domain key must non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->domain = $domainKey;
	}

	/**
	 * @param	string	$domainMember
	 * @return	null
	 */
	protected function setMember($domainMember)
	{
		if (empty($domainMember) || ! is_string($domainMember)) {
			$err = "Invalid domain member must non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->setOperand($domainMember);
	}
}
