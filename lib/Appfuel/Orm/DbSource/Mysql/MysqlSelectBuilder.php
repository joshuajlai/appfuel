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
namespace Appfuel\Orm\DbSource\Mysql;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\ViewInterface,
	Appfuel\Expr\ExprListInterface,
	Appfuel\Orm\OrmCriteriaInterface,
	Appfuel\Orm\DbSource\DbMapInterface;

/**
 */
class MysqlSelectBuilder
{
	/**
	 * Used to store data before sql is assigned
	 * @var array
	 */
	protected $data = array(
		'columns' => array(),
		'from'    => null,
		'joins'   => array(),
		'where'   => null,
		'group'   => null,
		'having'  => null,
		'order'   => null,
		'limit'   => null,
	);

	/**
	 * @var bool
	 */
	protected $isAlias = true;

	/**
	 * @var bool
	 */
	protected $isPrepared = true;

	/**
	 * List of values used where isPrepared is true
	 * @var array
	 */
	protected $values = array(
		'columns' => array(),
		'joins'   => array(),
		'where'   => array(),
		'group'   => array(),
		'having'  => array(),
		'order'   => array(),
		'limit'   => array(),
	);

	/**
	 * @param	enableAliases	$enableAliases
	 * @param	isPrepared		$isPrepared
	 * @return	SqlHelper
	 */
	public function __construct($enableAliases = true, $isPrepared = true)
	{
		if (false === $enableAliases) {
			$this->isAlias = false;
		}

		if (false === $isPrepared) {
			$this->isPrepared = false;
		}
	}
		
	/**
	 * @return	bool
	 */
	public function isAlias()
	{
		return $this->isAlias;
	}

	/**
	 * @return	bool
	 */
	public function isPrepared()
	{
		return $this->isPrepared;
	}

	/**
	 * @param	scalar	$value
	 * @return	SqlHelper
	 */
	public function addValue($type, $value)
	{
		if (! is_array($value) && ! is_scalar($value)) {
			$err = "db value must be a scalar or array value -($type) given";
			throw new InvalidArgumentException($err);
		}

		if (! isset($this->values[$type])) {
			$typeList = implode('| ', array_keys($this->values));
			$err = "type must be a one of the following strings -($typeList)";
			throw new InvalidArgumentException($err);
		}
	
		if (is_array($value)) {
			$this->values[$type] = array_merge($this->values[$type], $value);
			return $this;
		}

		$this->values[$type][] = $value;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getValues($type = null)
	{
		if (null === $type) {
			return $this->values;
		}

		if (! is_string($type) || ! isset($this->values[$type])) {
			return false;
		}
	
		return $this->values[$type];
	}

	/**
	 * @param	array			$list	list of domains members to be mapped
	 * @param	DbMapInterface	$map	db column to domain member map
	 * @param	bool			$isAlias 
	 * @param	bool			$isMapBack	will use the domain member name
	 *							in the columns 'AS' this creates a natural 
	 *							map back to the same domain keys
	 * @return	MysqlSelectBuilder
	 */
	public function addDomainColumns(array $list, 
									DbMapInterface $map,
									$isAlias = true,
									$isMapBack = true)
	{
		$isAlias   = (false === $isAlias)   ? false : true;
		$isMapBack = (false === $isMapBack) ? false : true;

		$columns  = array();
		foreach ($list as $key => $spec) {
			if (! is_string($key) || empty($key)) {
				$err = 'domain key must be a non empty string';
				throw new InvalidArgumentException($err);
			}
			if (is_string($spec) && 'all' === strtolower($spec)) {
				$tmp = $map->getAllColumns($key, $isAlias, $isMapBack);
				$columns = array_merge($columns, $tmp);
			}
			else if (is_array($spec)) {
				foreach ($spec as $member) {
					$columns[] = $map->mapColumn(
						$key, 
						$member, 
						$isAlias,
						$isMapBack
					);
				}
			}
		}

		$this->data['columns'][] = implode(', ', $columns);
		return $this;
	}

	public function addDbColumns(array $list, 
								 DbMapInterface $map,
								 $isAlias = true)
	{
		$columns = array();
		foreach ($list as $index => $spec) {
			if (is_string($spec)) {
				$columns[] = $spec;
			}
			else if (is_array($spec) && 3 === count($spec)) {
				$str       = $spec[0];
				$marker    = $spec[1];
				$domainStr = $spec[2];
				if (! is_string($marker) || false === strpos($str, $marker)) {
					$err  = "marker to replace is not a string or is ";
					$err .= "not found in -($str)";
					throw new RunTimeException($err);
				}
				$column = $map->mapDomainStr($domainStr, $isAlias, false);
				if (! $column) {
					$err  = "failed to add db column: could not map ";
					$err .= "-($domainStr) to a column";
					throw new RunTimeException($err);
				}
				$columns[] = str_replace($marker, $column, $str);
			}
		}

		$this->data['columns'][] = implode(', ', $columns);
		return $this;
	}

	/**
	 * @param	string	$domainKey
	 * @return	SqlHelper
	 */
	public function setFromClause($key, DbMapInterface $map, $isAlias = true)
	{
		$this->data['from'] = $map->getTableName($key, $isAlias);
		return $this;
	}

	public function mapJoinColumn($domainStr, $key, DbMapInterface $map)
	{
		if (! is_string($domainStr) || empty($domainStr)) {
			$err = 'domain to column map failed: must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$isAlias   = true;
		if (false !== $pos = strpos($domainStr, '.')) {
			$isMapBack = false;
			$column = $map->mapDomainStr($domainStr, $isAlias, $isMapBack);
		}
		else {
			$column = $map->mapColumn($key, $domainStr, $isAlias);
		}

		return $column;
	}
	
	/**
	 * @param	array	$list
	 * @return	SqlHelper
	 */
	public function loadJoins(array $list, DbMapInterface $map)
	{
		$joinType = 'inner';
		foreach ($list as $key => $data) {
			$stmt = 'LEFT JOIN ';
			$onOp = '=';
			$tableName = $map->getTableName($key, true);
			if (is_string($data['type'])) {
				$joinType = strtolower($data['type']);
			}
			if ('inner' === $joinType) {
				$stmt = 'INNER JOIN ';	
			}
			$stmt .= $map->getTableName($key, true) . ' ON ';
			
			if (! isset($data['left']) || ! is_string($data['left'])) {
				$err  = "could not load join -($key) key for left column ";
				$err .= " -(left) is not set or is not a string";
				throw new InvalidArgumentException($err);
			}
			$leftColumn = $this->mapJoinColumn($data['left'], $key, $map);

			if (! isset($data['right']) || ! is_string($data['right'])) {
				$err  = "could not load join -($key) key for right column ";
				$err .= "-(right) is not set or is not a string";
				throw new InvalidArgumentException($err);
			}
			$rightColumn = $this->mapJoinColumn($data['right'], $key, $map);

			if (isset($data['on-op']) && is_string($data['on-op'])) {
				$onOp = $data['on-op'];
			}
			$this->data['joins'][] = "$stmt $leftColumn $onOp $rightColumn";
		}

		return $this;
	}

	public function loadWhereExprs(ExprListInterface $list, 
								   DbMapInterface $map)
	{
		$this->data['where'][] = $this->processExprList('where', $list, $map);
		return $this;
	}

	public function processExprList($type, 
									ExprListInterface $list, 
									DbMapInterface $map)
	{
		$result = '';
        foreach ($list as $key => $data) {
            $expr = current($data);
            $column = $map->mapColumn($expr->getDomain(), $expr->getMember());
            if ($this->isPrepared()) {
                $this->addValue($type, $expr->getValue());
				$value = '?';
            }
			else {
				$value = $expr->getValue();
			}

            $op = $expr->getOperator();
            $result .= "{$column} {$op} {$value} ";
            $relOp = next($data);
            if ('and' === $relOp || 'or' === $relOp) {
                $result .= strtoupper($relOp) . ' ';
            }
        }
	
		return $result;	
	}
}
