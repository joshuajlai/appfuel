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

use InvalidArgumentException,
	Appfuel\Expr\ExprList,
	Appfuel\Expr\ExprListInterface,
	Appfuel\DataStructure\Dictionary,
	Appfuel\Orm\Domain\DomainExpr,
	Appfuel\Orm\Domain\DomainExprInterface;

/**
 * The criteria holds any information necessary for the sql factory to build
 * the correct sql for the db request to pull domain information down from 
 * the database
 */
class OrmCriteria extends Dictionary implements OrmCriteriaInterface
{
	/**
	 * @var of the target domain
	 */
	protected $tagetDomain = null;

	/**
	 * List of named expressions. A named expression is an expression list 
	 * identified by a key. This allows the repo to specify domain expressions
	 * for any number of targets not just the where clause of a sql statement
	 * as is the case usually
	 *
	 * @var	Dictionary
	 */
	protected $exprs = array();

	/**
	 * Used to describe how much of the recordset to return
	 * @var	mixed  array
	 */
	protected $limit = array();

	/**
	 * @var	array
	 */ 
	protected $order = array();

	/**
	 * @var string
	 */
	protected $searchTerm = null;

	/**
	 * @param	DictionaryInterface $options
	 * @return	Criteria
	 */
	public function __construct(array $exprs = null, array $params = null)
	{
		/* default value is an empty array */
		if (null !== $exprs) {
			$this->setExprLists($exprs);
		}

		/* add any parameters */
		if (null !== $params) {
			parent::__construct($params);
		}
	}

	/**
	 * @return	string
	 */
	public function getTargetDomain()
	{
		return $this->targetDomain;
	}

	/**
	 * @param	string	$name
	 * @return	OrmCriteria
	 */
	public function setTargetDomain($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'domain name must be a non empty string';
			throw new InvalidArgumentException($name);
		}

		$this->targetDomain = $name;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getExprLists()
	{
		return $this->exprs;
	}

	/**
	 * @param	array	list
	 * @return	Criteria
	 */
	public function setExprLists(array $list)
	{
		foreach ($list as $key => $exprList) {
			if (! $exprList instanceof ExprListInterface) {
				 $err = "Each key must have an ExprListInterface";
				throw new InvalidArgumentException($err);
			}
		}
		$this->exprs = $list;
		return $this;
	}

	/**
	 * @param	string	$key
	 * @param	DomainExprInterface		$expr
	 * @param	string	$op
	 * @return	Criteria
	 */
	public function addExpr($key, $expr, $op = 'and')
	{
		if (empty($key) || ! is_scalar($key)) {
			$err = "option key must be a non empty string";
			throw new InvalidArgumentException($err);
		}
		
		if (is_string($expr) && ! empty($expr)) {
			$expr = $this->createDomainExpr($expr);
		}
		else if (! $expr instanceof DomainExprInterface) {
			$err  = 'expression must be a non empty string or a object that ';
			$err .= 'implements Appfuel\Orm\Domain\DomainExprInterface';
			throw new InvalidArgumentException($err);
		}

		$list = $this->getExprList($key);
		if (false === $list) {
			$list = $this->createExprList();
			$list->add($expr, $op);
			$this->exprs[$key] = $list;
			return $this;
		}
			
		$list->add($expr, $op);
		return $this;
	}

	/**
	 * Return an expression list identified by key
	 * 
	 * @param	string	$key
	 * @return	ExprListInterface | false when not found or error
	 */
	public function getExprList($key)
	{
		if (! $this->isExprList($key)) {
			return false;
		}

		return $this->exprs[$key];
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	public function isExprList($key)
	{
		if (empty($key) || ! is_scalar($key)) {
			return false;
		}
	
		if (isset($this->exprs[$key]) && 
			$this->exprs[$key] instanceof ExprListInterface) {
			return true;
		}

		return false;
	}

	public function getAllValues()
	{
		$lists = $this->getExprLists();
		
		$values = array();
		foreach ($lists as $list) {
			foreach ($list as $exprData) {
				$expr = current($exprData);
				$values[] = $expr->getValue();
			}
		}

		return $values;
	}

    /**
     * @param   string  $expr
     * @return  DomainExpr
     */
    public function createDomainExpr($expr)
    {
        return new DomainExpr($expr);
    }

	/**
	 * @param	string	$domainStr
	 * @param	string	$dir
	 * @return	OrmCriteria
	 */
	public function addOrder($domainStr, $dir = null)
	{
		if (! is_string($domainStr) || empty($domainStr)) {
			$err = "order must be a none empty string";
			throw new InvalidArgumentException($err);
		}

		$sortDir = 'asc';
		if (is_string($dir) && 'desc' === strtolower($dir)) {
			$sortDir = 'desc';
		}
		
        $this->order[$domainStr] = $sortDir;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getOrderList()
	{
		$result = array();
		foreach ($this->order as $domainStr => $dir) {
			$result[] = array($domainStr, $dir);
		}

		return $result;
	}

	/**
	 * @return	array
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * @param	int	$offset
	 * @param	int	$max
	 * @return	OrmCriteria
	 */
	public function setLimit($offset, $max = null)
	{
		if (! is_int($offset) || $offset < 0)  {
			$err = "set limit failed: 1st param must be an positive int";
			throw new InvalidArgumentException($err);
		}

		$limit = array($offset);
		
		if (null !== $max) {
			if (! is_int($max) || $max < 0)  {
				$err = "set limit failed: 2nd param must be an positive int";
				throw new InvalidArgumentException($err);
			}
			$limit[] = $max;
		}

		$this->limit = $limit;
		return $this;
	}

	/**
	 * @param	string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	public function setSearchTerm($term, $urlDecode = true)
	{
		if (! is_string($term)) {
			$err = "search term must be a string";
			throw new InvalidArgumentException($err);
		}

		if (true === $urlDecode) {
			$term = urldecode($term);
		}
		$this->searchTerm = trim($term);
	}

	/**
	 * @return	bool
	 */
	public function isSearchTerm()
	{
		return is_string($this->searchTerm);
	}

	/**
	 * @return	ExprList
	 */
	protected function createExprList()
	{
		return new ExprList();
	}

	/**
	 * @return	Dictionary
	 */
	protected function createDictionary()
	{
		return new Dictionary();
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
