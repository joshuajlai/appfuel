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
	Appfuel\Orm\OrmCriteriaInterface,
	Appfuel\View\Compositor\FileCompositor;

/**
 */
class SqlFileCompositor 
	extends FileCompositor implements SqlFileCompositorInterface
{
	/**
	 * @var bool
	 */
	protected $isAlias = false;

	/**
	 * @var bool
	 */
	protected $isPrepared = false;

	/**
	 * Collection of db table maps used to generate sql
	 * @var DbMapInterface
	 */
	protected $dbMap = null;

	/**
	 * @return	SqlFileCompositor
	 */
	public function enableAlias()
	{
		$this->isAlias = true;
		return $this;
	}

	/**
	 * @return	SqlFileCompositor
	 */
	public function disableAlias()
	{
		$this->isAlias = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isAlias()
	{
		return $this->isAlias;
	}

	/**
	 * @return	SqlFileCompositor
	 */
	public function enablePrepared()
	{
		$this->isPrepared = true;
		return $this;
	}

	/**
	 * @return	SqlFileCompositor
	 */
	public function disablePrepared()
	{
		$this->isPrepared = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPrepared()
	{
		return $this->isPrepared;
	}

	/**
	 * @return	DbMapInterface
	 */
	public function getDbMap()
	{
		return $this->dbMap;
	}

	/**
	 * @param	DbMapInterface $map
	 * @return	SqlFileCompositor
	 */
	public function setDbMap(DbMapInterface $map)
	{
		$this->dbMap = $map;
		return $this;
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	public function isDbMap($key)
	{
		if ($this->dbMap instanceof DbMapInterface) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	public function isTableMap($key)
	{
		if (! $this->isDbMap($key)) {
			return false;
		}

		return $this->getDbMap()
					->isTableMap($key);
	}


	/**
	 * @param	string	$key
	 * @return	DbTableMapInterface
	 */
	public function getTableMap($key)
	{
		return $this->getDbMap()
					->getTableMap($key);
	}

	/**
	 * @param	string	$key
	 * @return	string
	 */
	public function getTableName($key)
	{
		if (! $this->isTableMap($key)) {
			$err = "failed to get table name: no table map for -($key)";
			throw new LogicException($err);
		}

	
		return $this->getTableMap()
				    ->getTableName();
	}

	/**
	 * @param	string	$key
	 * @return	string
	 */
	public function getTableAlias($key)
	{
		if (! $this->isTableMap($key)) {
			$err = "failed to get table alias: no table map for -($key)";
			throw new LogicException($err);
		}

	
		return $this->getTableMap()
					->getTableAlias();
	}

	/**
	 * @param	string	$key
	 * @return	null
	 */
	public function renderFrom($key, $isNewLine = true)
	{
		if (! $this->isTableMap($key)) {
			$err = "failed to render from clause: no table map for -($key)";
			throw new LogicException($err);
		}

		$map  = $this->getTableMap($key);
		$name = $map->getTableName();

		$sql = "FROM $name ";
		if ($this->isAlias()) {
			$sql .= "AS {$map->getTableAlias()} ";
		} 
		
		if (true === $isNewLine) { 
			$sql .= PHP_EOL;
		}

		echo $sql;
	}

	public function renderSelect($data, array $keywords = null, $isNL = true)
	{
		if (is_string($data)) {
			$columns = $this->mapColumns($data);
		}
		else if (is_array($data)) {
			$result = array();
			foreach ($data as $key => $list) {
				$result[] = $this->mapColumns($key, $list);
			}
			$columms = implode(', ', $result);
		}
		
		$sqlWords = '';
		if ($keywords !== null) {
			$sqlWords = implode(' ', $keywords) . ' ';
		}

		$nl = '';
		if (true === $isNL) {
			$nl = PHP_EOL;
		}
		
		echo "SELECT ", $sqlWords, $columns, $nl;
	}

	public function renderWhere($key, $exprKey = 'where', $isStrict = true)
	{
		$criteria = $this->get($key);
		if (! $criteria instanceof OrmCriteriaInterface) {
			$err = "failed to render where: criteria not found -($key)";
			throw new InvalidArgumentException($err);
		}

		if (! $criteria->isExprList($exprKey)) {
			if (true === $isStrict) {
				$err  = "expr list -($exprKey) was not found and required ";
				$err .= "to render the where clause";
				throw new LogicException($err);
			}

			return '';
		}
		$list = $criteria->getExprList($exprKey);
		$result = '';
		foreach ($list as $key => $exprData) {
			$expr = current($exprData);
			$column = $this->mapColumn($expr->getDomain(), $expr->getMember());
			$value  = '?';
			if (! $this->isPrepared()) {
				$value = $expr->getValue();
			}
			$op = $expr->getOperator();
			$result .= "{$column} {$op} {$value} ";
			$relOp = next($exprData);
			if ('and' === $relOp || 'or' === $relOp) {
				$result .= strtoupper($relOp) . ' ';
			}
		}
		echo "WHERE $result";
	}

	/**
	 * @param	string	$key	
	 * @param	string	$member
	 * @param	bool	$useAlias
	 * @param	string	$data
	 * @return	string
	 */
	public function renderColumn($key, $member, $nl = true, $data = null)
	{
		$newLine = '';
		if (true === $useNewLine) {
			$newLine = PHP_EOL;
		}

		$extra = '';
		if (is_string($data)) {
			$extra = $data;
		}
		echo $this->mapColumn($key, $member), $extra, $newLine;
	}

	/**
	 * @param	string	$key	
	 * @param	string	$member
	 * @param	bool	$useAlias
	 * @return	string
	 */
	public function mapColumn($key, $member)
	{
		if (! $this->isTableMap($key)) {
			$err = "failed to get table map for -($key)";
			throw new LogicException($err);
		}

		$table  = $this->getTableMap($key);
		$column = $table->mapColumn($member);
		if (false === $column) {
			$err = "failed to map column: member -($member) does not exist";
			throw new LogicException($err);
		}

		if (true === $this->isAlias()) {
			$column = "{$table->getTableAlias()}.{$column}";
		}

		return $column;
	}

	/**
	 * @param	string	$key	
	 * @param	array	$member
	 * @param	bool	$useAlias
	 * @param	string	$data
	 * @return	null
	 */
	public function renderColumns($key, array $list = null, $data = null)
	{
		$extra = '';
		if (is_string($data)) {
			$extra = $data;
		}

		$result = $this->mapColumns($key, $list);
		echo $result, $extra;
	}

	/**
	 * @param	string	$key	
	 * @param	array	$list
	 * @param	bool	$useAlias
	 * @return	string
	 */
	public function mapColumns($key, $list = null)
	{
		if (! $this->isTableMap($key)) {
			$err = "failed to get table map for -($key)";
			throw new LogicException($err);
		}
		
		$alias  = '';
		$table  = $this->getTableMap($key);
		if (null === $list) {
			if (true === $this->isAlias()) {
				$alias = $table->getTableAlias();
				$str = "{$alias}." . 
						implode(", {$alias}.", $table->getAllColumns());
			}
			else  {
				$str = implode(", ", $table->getAllColumns());
			}
			return $str;
		}
	
		if (! is_array($list)) {
			$err  = 'failed to map columns: list must be null -(for all) ';
			$err .= 'or an array of domain members to map to columns';
			throw new InvalidArgumentException($err); 
		}

		$result = array();
		foreach ($list as $colName) {
			$result[] = $this->mapColumn($key, $colName);
		}

		return implode(", ", $result);
	}

}
