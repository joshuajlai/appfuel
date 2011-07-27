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
namespace Appfuel\Orm\Repository;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Expr\ExprList,
	Appfuel\Framework\Expr\ExprListInterface,
	Appfuel\Framework\Orm\Repository\DomainExprInterface,
	Appfuel\Framework\Orm\Repository\CriteriaInterface;

/**
 * The criteria holds any information necessary for the sql factory to build
 * the correct sql for the db request to pull domain information down from 
 * the database
 */
class Criteria implements CriteriaInterface
{
	/**
	 * Name of the primary domain used in the sql statement. This is given
	 * as the domain-key mapped in the domain identity and not the domain
	 * class name.
	 * @var string
	 */
	protected $targetDomain = null;

	/**
	 * Name of the type of operation this criteria repersents
	 * @var string
	 */
	protected $opType = null;

	/**
	 * Filters are a list of expressions that can be separated by one of 
	 * to logical operators AND|OR the last expression has no operator
	 * @var ExprList
	 */
	protected $filterList = null;

	/**
	 * List of options to old any key value pairs that have to be 
	 * passed into the sqlfactory or build factory
	 * @var	Dictionary
	 */
	protected $options = null;

	/**
	 * @return	array
	 */
	public function getFilterList()
	{
		return $this->filterList;
	}

	/**
	 * @param	ExprListInterface	$list
	 * @return	Criteria
	 */
	public function setFilterList(ExprListInterface $list)
	{
		$this->filterList = $list;
		return $this;
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
	public function addFilter(DomainExprInterface $expr, $logical = 'and')
	{
		$list = $this->getFilterList();
		if (! $list instanceof ExprListInterface) {
			throw new Exception("Can not add filter: exprList is not set");
		}

		$list->add($expr, $logical);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTargetDomain()
	{
		return $this->targetDomain;
	}

	/**
	 * @param	string	$domainKey
	 * @return	Criteria
	 */
	public function setTargetDomain($domainKey)
	{
		if (! $this->isValidString($domainKey)) {
			throw new Exception("domainKey must be a non empty string");
		}

		$this->targetDomain = $domainKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOperationType()
	{
		return $this->opType;
	}

	/**
	 * @param	string	$domainKey
	 * @return	Criteria
	 */
	public function setOperationType($type)
	{
		if (! $this->isValidString($type)) {
			throw new Exception("operation must be a non empty string");
		}

		$this->opType = $type;
		return $this;
	}


	/**
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isValidString($str)
	{
		if (empty($str) || ! is_string($str)) {
			return false;
		}

		return true;
	}
}
