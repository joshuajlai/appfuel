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
namespace Appfuel\Orm\Repository;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Expr\BinaryExpr,
	Appfuel\Framework\Orm\Repository\DomainExprInterface;

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
	 * List of operators allowed in domain expressions
	 * @var	array
	 */
	protected $validOps = array(
		'<',
		'=',
		'<=',
		'<>',
		'>',
		'>=',
		'is',
		'is not',
		'in',
		'not in',
		'between',
		'not between'
	);

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
	 * Alias operand for better readability
	 * 
	 * @return	string
	 */
	public function getMember()
	{
		return parent::getLeftOperand();
	}

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
		$pattern .= "(?<value>([-_,a-zA-Z0-9 ]+))/i";

		$match  = array();
		preg_match_all($pattern, $expr, $match);

		$err = "Failed to parse domain expression: ";
		if (! array_key_exists('domain', $match)) {
			throw new Exception("$err could not find domain");
		}

		if (! array_key_exists('member', $match)) {
			throw new Exception("$err could not find domain member");
		}
		if (! array_key_exists('operator', $match)) {
			throw new Exception("$err could not find expression operator");
		}

		if (array_key_exists('value', $match)) {
			$this->setRightOperand(current($match['value']));
		}

		$op = strtolower(trim(current($match['operator'])));
		if (! in_array($op, $this->validOps, true)) {
			throw new Exception("$err invalid operator given");
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
			throw new Exception("Invalid domain key must non empty string");
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
			throw new Exception("Invalid domain member must non empty string");
		}

		$this->setOperand($domainMember);
	}
}

