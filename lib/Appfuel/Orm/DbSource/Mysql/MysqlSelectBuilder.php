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
	 * @param	array	$spec
	 * @return	string
	 */	
	public function build(array $spec, 
						  DbMapInterface $map,
						  $isAlias = true, 
						  $isPrepared = true,
						  $isMapBack = true)
	{
		$isAlias    = ($isAlias === false) ? false : true;
		$isPrepared = ($isPrepared === false) ? false : true;
		if (isset($spec['domain-columns'])) {
			$isMapBack = ($isMapBack) ? false : true;
			$this->addDomainColumns(
				$spec['domain-columns'],
				$map,
				$isAlias,
				$isMapBack
			);
		}

		if (isset($spec['db-columns'])) {
			$this->addDbColumns($spec['db-columns'], $map, $isAlias);
		}

		if (isset($spec['domain-from'])) {
			$this->setDomainFrom($spec['domain-from'], $map, $isAlias);
		}
		else if (isset($spec['db-from'])) {
			$this->setDbFrom($spec['db-from']);
		}


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

	/**
	 * @param	array	$list
	 * @param	DbMapInterface	$map
	 * @param	bool	$isAlias
	 * @return	MysqlSelectBuilder
	 */
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
	 * @param	string	$key
	 * @param	DbMapInterface $map
	 * @param	bool	$isAlias
	 * @return	MysqlSelectBuilder
	 */
	public function setDomainFrom($key, DbMapInterface $map, $isAlias = true)
	{
		$this->data['from'] = $map->getTableName($key, $isAlias);
		return $this;
	}

	
	/**
	 * @param	string	$domainStr
	 * @param	DbMapInterface $map
	 * @return	MysqlSelectBuilder
	 */
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
	 * @param	array			$list
	 * @param	DbMapInterface	$map
	 * @return	MysqlSelectBuilder
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

	/**
	 * @param	ExprListInterface	$list
	 * @param	DbMapInterface		$map
	 * @return	MysqlSelectBuilder
	 */
	public function loadWhereExprs(ExprListInterface $list, 
								   DbMapInterface $map,
								   $isPrepared = true)
	{
		$this->data['where'][] = $this->processExprList(
			'where', 
			$list, 
			$map
			$isPrepared
		);
		return $this;
	}

	/**
	 * @param	array	$list	list of domain members to group by
	 * @param	DbMapInterface $map
	 * @param	bool	$isAlias
	 * @return	MysqlSelectBuilder
	 */
	public function loadDomainGroupBy(array $list, 
									  DbMapInterface $map,
									  $isAlias = true)
	{
		$result = array();
		foreach ($list as $spec) {
			if (is_string($spec)) {
				$result[] = $map->mapDomainStr($spec, $isAlias, false);
			}
		}

		$this->data['group'][] = implode(', ', $result);
	}

	/**
	 * @param	array	$list
	 * @return	MysqlSelectBuilder
	 */
	public function loadDbGroupBy(array $list)
	{
		$this->data['group'][] = implode(', ', $list);
		return $this;
	}

	/**
	 * @param	array	$list
	 * @param	DbMapInterface	$map
	 * @param	bool	$isAlias
	 * @return	MysqlSelectBuilder
	 */
	public function loadDomainOrderBy(array $list, 
									  DbMapInterface $map,
									  $isAlias = true)
	{
		$result = array();
		$dir    = 'asc';
		foreach ($list as $spec) {
			if (is_string($spec)) {
				$result[] = $spec;
			}
			else if (is_array($spec)) {
				$domainStr = current($spec);
				$tmp       = next($spec);
				if (is_string($dir)) {
					$tmp = strtolower($tmp);
					if ('asc' === $tmp || 'desc' === $tmp) {
						$dir = $tmp;
					}
				}
				$column = $map->mapDomainStr($domainStr, $isAlias, false);
				if (! $column) {
					$err = "failed to load order: -($domainStr) not mapped";
					throw new RunTimeException($err);
				}
				$result[] = "$column $dir";
			}
		}

		$this->data['order'] = implode(', ', $result);
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	MysqlSelectBuilder
	 */
	public function loadDbOrderBy(array $list)
	{
		$this->data['order'][] = implode(',', $list);
		return $this;
	}

	/**
	 * @param	int		$offset
	 * @param	int		$max
	 * @param	bool	$isPrepared
	 * @return	MysqlSelectBuilder
	 */
	public function setLimit($offset, $max = null, $isPrepared = true)
	{
		if (! is_int($offset) || $offset < 0) {
			$err = 'set limit failed: offset must be a positive int';
			throw new InvalidArgumentException($err);
		}

		$value = array($offset);
		
		$limit  = "$offset";
		if (true === $isPrepared) {
			$limit = '?';
			$this->addValue('limit', $offset);
		}

		if (is_int($max) && $max > 0) {
			if (true === $isPrepared) {
				$limit .= ", ?";
				$this->addValue('limit', $max);
			}
			else {
				$limit .= ", $max";
			}
		}

		$this->data['limit'] = $limit;
		return $this;
	}

	/**
	 * @param	string	$type
	 * @param	ExprListInterface $list
	 * @param	DbMapInterface $map
	 * @return	MysqlSelectBuilder
	 */
	public function processExprList($type, 
									ExprListInterface $list, 
									DbMapInterface $map,
									$isPrepared = true)
	{
		$result = '';
        foreach ($list as $key => $data) {
            $expr = current($data);
            $column = $map->mapColumn($expr->getDomain(), $expr->getMember());
            if (true === $isPrepared) {
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
