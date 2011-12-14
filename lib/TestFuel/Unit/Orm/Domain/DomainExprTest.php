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
namespace TestFuel\Unit\Orm\Domain;

use StdClass,
	Appfuel\Orm\Domain\DomainExpr,
	TestFuel\TestCase\BaseTestCase;

/**
 * Domain expressions allows a simple expression about a domain to be expressed
 * as a string in the form of <domain-key>.<domain-member> <op> <value>
 * ex) user.firstName = bob. We are going to test that valid expressions are
 * are parsed into their parts and are accessible. Also that know invalid 
 * expressions cause exceptions to be thrown.
 */
class DomainExprTest extends BaseTestCase
{
	/**
	 * List of valid expression. Bare in mind that because ___.--- passes
	 * as word it will fail the when the identity validates, unless of course
	 * that is your actual domain key and member, for which you have other
	 * problems.
	 *
	 * @return array
	 */
	public function exprProvider()
	{
		return array(
			array('user.name=bob', 'user', 'name', '=', 'bob'),
			array('user-email.isHtml < 1', 'user-email', 'isHtml', '<', '1'),
			array('role.id	<>			5','role', 'id', '<>', '5'),
			array('role.id <= 6', 'role', 'id', '<=', '6'),
			array('_user.id >= 44', '_user', 'id', '>=', '44'),
			array('___.--- > blah', '___', '---', '>', 'blah'),
			array('role.name is not empty', 'role', 'name', 'is not', 'empty'),
			array('role.name is empty', 'role', 'name', 'is', 'empty'),
			array('role.id in 1,2,3,4', 'role', 'id', 'in', '1,2,3,4'),
			array('role.id not in 1,2', 'role', 'id', 'not in', '1,2'),
			array('role.id between 1 and 2','role','id','between','1 and 2'),
			array(
				'role.id not between 1 and 2', 
				'role', 
				'id', 
				'not between', 
				'1 and 2'
			),
		);
	}

	/**
	 * @dataProvider	exprProvider
	 * @return null
	 */
	public function testDomainExpr($expr, $domain, $member, $op, $value)
	{
		$domainExpr = new DomainExpr($expr);
		$this->assertEquals($domain, $domainExpr->getDomain());
		$this->assertEquals($member, $domainExpr->getMember());
		$this->assertEquals($op, $domainExpr->getOperator());
		$this->assertEquals($value, $domainExpr->getValue());
		
	}

	/**
	 * the first part of the expression must be word.word otherwise an 
	 * exception is thrown
	 *
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExpressionNoDomain()
	{
		$expr = new DomainExpr('first-name=rob');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExpressionNoValue()
	{
		$expr = new DomainExpr('user.first-name=');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExprBadOpNotLessthan()
	{
		$expr = new DomainExpr('user.first-name not < 5');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExprBadOpNotLessthanEq()
	{
		$expr = new DomainExpr('user.first-name not <= 5');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExprBadOpNotGreaterthan()
	{
		$expr = new DomainExpr('user.first-name not > 5');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExprBadOpNotGreaterthanEq()
	{
		$expr = new DomainExpr('user.first-name not >= 5');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExprBadOpNotNot()
	{
		$expr = new DomainExpr('user.first-name not <> 5');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExprBadOpDoubleNot()
	{
		$expr = new DomainExpr('user.first-name not not 5');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExprBadOpInNot()
	{
		$expr = new DomainExpr('user.first-name in not 1,2,3');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testInvalidExprBadOpBetweenNot()
	{
		$expr = new DomainExpr('user.first-name between not 1 and 2');
	}
}
