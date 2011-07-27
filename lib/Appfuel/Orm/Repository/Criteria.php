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
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\DataStructure\DictionaryInterface,
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
	 * List of named expressions. A named expression is an expression list 
	 * identified by a key. This allows the repo to specify domain expressions
	 * for any number of targets not just the where clause of a sql statement
	 * as is the case usually
	 *
	 * @var	Dictionary
	 */
	protected $exprs = array();

	/**
	 * General key value pair to used to hold optional values that are not
	 * in the form of expressions
	 * @var Dictionary
	 */
	protected $options = null;

	/**
	 * @param	DictionaryInterface $options
	 * @return	Criteria
	 */
	public function __construct(array $exprs = null, 
								DictionaryInterface $options = null)
	{
		/* default value is an empty array */
		if (null !== $exprs) {
			$this->setExprs($exprs);
		}

		/* default value is an empty expression */
		if (null === $options) {
			$options = $this->createDictionary();
		}

		$this->setOptions($options);
	}

	/**
	 * @return	Dictionary
	 */
	public function getOptions()
	{
		return $this->option;
	}

	/**
	 * @param	DictionaryInterface		$options
	 * @return	Criteria
	 */
	public function setOptions(DictionaryInterface $options)
	{
		$this->options = $options;
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
				throw new Exception("Each key must have an ExprListInterface");
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
	public function addExpr($key, DomainExprInterface $expr, $op = 'and')
	{
		if (! $this->isValidString($key)) {
			throw new Exception("option key must be a non empty string");
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
		if (! $this->isValidString($key)) {
			return false;
		}
	
		if (isset($this->exprs[$key]) && 
			$this->exprs[$key] instanceof ExprListInterface) {
			return true;
		}

		return false;
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
