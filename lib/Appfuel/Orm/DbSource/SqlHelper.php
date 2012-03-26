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
namespace Appfuel\Orm\DbSource;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\ViewInterface,
	Appfuel\Expr\ExprListInterface,
	Appfuel\Orm\OrmCriteriaInterface,
	Appfuel\View\Compositor\FileCompositor;

/**
 */
class SqlHelper
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
	protected $template = null;

	/**
	 * Collection of db table maps used to generate sql
	 * @var DbMapInterface
	 */
	protected $dbMap = null;
	
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
	protected $values = array();

	/**
	 * @param	DbMapInterface	$map
	 * @param	ViewInterface	$template
	 * @param	enableAliases	$enableAliases
	 * @param	isPrepared		$isPrepared
	 * @return	SqlHelper
	 */
	public function __construct(DbMapInterface $map, 
								ViewInterface  $template,
								$enableAliases = true,
								$isPrepared    = true)
	{
		$this->map = $map;
		$this->template = $template;
		if (false === $enableAliases) {
			$this->isAlias = false;
		}

		if (false === $isPrepared) {
			$this->isPrepared = false;
		}
	}
		
	/**
	 * @return	DbMapInterface
	 */
	public function getDbMap()
	{
		return $this->map;
	}

	/**
	 * @return	ViewInterface
	 */
	public function getTemplate()
	{
		return $this->template;
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
	public function addValue($value)
	{
		if (! is_array($value) && ! is_scalar($value)) {
			$err = "db value must be a scalar or array value -($type) given";
			throw new InvalidArgumentException($err);
		}
		
		if (is_array($value)) {
			$this->values = array_merge($this->values, $value);
			return $this;
		}

		$this->values[] = $value;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
	 * @param	array $list
	 * @return	SqlHelper
	 */
	public function addColumns(array $list, $isMapBack = true)
	{
		$columns  = array();
		$isAlias  = $this->isAlias();
		$map	  = $this->getDbMap();
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

		$str = implode(', ', $columns);
		$this->data['columns'][] = implode(', ', $columns);
		return $this;
	}

	/**
	 * @param	string	$domainKey
	 * @return	SqlHelper
	 */
	public function setFromClause($key)
	{
		$this->data['from'] = $this->getTableName($key);
		return $this;
	}

	/**
	 * @param	string	$key
	 * @return	string
	 */
	public function getTableName($key)
	{
		if (! is_string($key) || empty($key)) {
			$err = 'domain key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$map = $this->getDbMap();

		$table = $map->getTableName($key);
		if (false === $table) {
			$err = "could not map domain -($key) to a table";
			throw new InvalidArgumentException($err);
		}
		$tableName = current($table);
	
		if (true === $this->isAlias()) {
			$tableName .= ' AS ' . next($table);
		}

		return $tableName;
	}

	public function mapJoinColumn($domainStr, $key, $map)
	{
		if (! is_string($domainStr) || empty($domainStr)) {
			$err = 'domain to column map failed: must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (false !== $pos = strpos($domainStr, '.', 2)) {
			$parts  = explode('.', $domainStr);
			if (empty($parts)) {
				$err  = "could not load join -($key): spliting domain ";
				$err .= " -($domain) failed on '.'";
				throw new RunTimeException($err);
			}
			$domain = current($parts);
			$member = next($parts);
			$column = $map->mapColumn($domain, $member, true);
		}
		else {
			$column = $map->mapColumn($key, $domainStr, true);
		}

		return $column;
	}
	
	/**
	 * @param	array	$list
	 * @return	SqlHelper
	 */
	public function loadJoins(array $list)
	{
		$map	  = $this->getDbMap();
		$joinType = 'inner';
		foreach ($list as $key => $data) {
			$stmt = 'LEFT JOIN ';
			$onOp = '=';
			$tableName = $this->getTableName($key);
			if (is_string($data['type'])) {
				$joinType = strtolower($data['type']);
			}
			if ('inner' === $joinType) {
				$stmt = 'INNER JOIN ';	
			}
			$stmt .= $this->getTableName($key) . ' ON ';
			
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
	}

	public function loadWhereExprs(ExprListInterface $list)
	{
		$map  = $this->getDbMap();
		$this->data['where'][] = $this->processExprList($list, $map);
		return $this;
	}

	public function processExprList(ExprListInterface $list, $map)
	{
		$result = '';
        foreach ($list as $key => $data) {
            $expr = current($data);
            $column = $map->mapColumn($expr->getDomain(), $expr->getMember());
            if ($this->isPrepared()) {
                $this->addValue($expr->getValue());
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
