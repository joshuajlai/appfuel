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
namespace Appfuel\Orm;

use Appfuel\Expr\ExprListInterface,
	Appfuel\Orm\Domain\DomainExprInterface,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * The criteria holds any information necessary for the sql factory to build
 * the correct sql for the db request to pull domain information down from 
 * the database
 */
interface OrmCriteriaInterface extends DictionaryInterface
{
	/**
	 * @return	array
	 */
	public function getExprLists();

	/**
	 * @param	array	list
	 * @return	Criteria
	 */
	public function setExprLists(array $list);

	/**
	 * @param	string	$key
	 * @param	DomainExprInterface		$expr
	 * @param	string	$op
	 * @return	Criteria
	 */
	public function addExpr($key, $expr, $op = 'and');

	/**
	 * Return an expression list identified by key
	 * 
	 * @param	string	$key
	 * @return	ExprListInterface | false when not found or error
	 */
	public function getExprList($key);

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	public function isExprList($key);
}
