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

use InvalidArgumentException;

/**
 * The orm map is used to map database
 */
class DbTableMap implements DbTableMapInterface
{
	/**
	 * List of db column name to domain member mappings
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Name name of the table this map corresponds to
	 * @var string
	 */
	protected $table = null;

	/**
	 * Alias used in sql expressions
	 * @var string
	 */
	protected $alias = null;

    /**
     * Primary Key
     * an array of domain members that represents the primary key
     * @var array
     */
    protected $primaryKey = array();

	/**
	 * @param	array  $data
	 * @return	DbMap
	 */
	public function __construct(array $data)
	{
		if (isset($data['column-map'])) {
			$this->setColumnMap($data['column-map']);
		}

		if (isset($data['table'])) {
			$this->setTableName($data['table']);
		}

		if (isset($data['alias'])) {
			$this->setTableAlias($data['alias']);
		}

        if (isset($data['primaryKey'])) {
            $this->setPrimaryKey($data['primaryKey']);
        }
	}

	/**
	 * @return	string
	 */
	public function getTableName()
	{
		return $this->table;
	}

	/**
	 * @return	array
	 */
	public function getColumnMap()
	{
		return $this->columns;
	}

	/**
	 * @return	return	string
	 */
	public function getTableAlias()
	{
		return $this->alias;
	}

	/**
	 * @param	string	$member
	 * @return	string | false when not found
	 */
	public function mapColumn($member, $isAlias = true)
	{
		if (! is_string($member) || ! isset($this->columns[$member])) {
			return false;
		}

		$alias  = $this->getTableAlias();
		$column = $this->columns[$member];
		if (null !== $alias && true === $isAlias) {
			$column = "$alias.$column";
		}

		return $column;
	}

	/**
	 * @param	string	$member
	 * @return	string | false when not found
	 */
	public function mapMember($column)
	{
		if (! is_string($column)) {
			return false;
		}

		return array_search($column, $this->columns, true);
	}

	/**
	 * @return	array
	 */
	public function getAllColumns($isAlias = true)
	{
		$columns = array_values($this->columns);
		$alias = $this->getTableAlias();
		if (false === $isAlias || null === $alias) {
			return $columns;
		}
		
		foreach ($columns as $key => &$name) {
			$name = "$alias.$name";
		}

		return $columns;
	}

	/**
	 * @return	array
	 */
	public function getAllColumnsAsMembers($domainKey, $isAlias = true)
	{
		if (! is_string($domainKey) || empty($domainKey)) {
			$err = 'domain key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$columns = $this->getColumnMap();
		$tableAlias  = $this->getTableAlias();
		$alias   = '';
		if (null !== $tableAlias) {
			$alias = "$tableAlias.";	
		}
		
		$result = array();
		foreach ($columns as $member => $column) {
			$result[] = "{$alias}$column AS \"$domainKey.$member\"";
		}

		return $result;
	}


	/**
	 * @return	array
	 */
	public function getAllMembers()
	{
		return array_keys($this->columns);
	}

	/**
	 * @param	array	$columns
	 * @return	null
	 */
	protected function setColumnMap(array $columns)
	{
		if ($columns === array_values($columns)) {
			$err  = 'column map must be an associative array of ';
			$err .= 'db column to domain member';
			throw new InvalidArgumentException($err);
		}

		foreach ($columns as $column => $member) {
			if (! is_string($column) || empty($column) ||
				! is_string($member) || empty($member)) {
				$err = 'column and member names must be non empty strings';
				throw new InvalidArgumentException($err);
			}
		}
		$this->columns = $columns;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	public function setTableName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'table name must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		$this->table = $name;
	}

	/**
	 * @param	string	$alias
	 * @return	null
	 */
	public function setTableAlias($alias)
	{
		if (! is_string($alias)){
			$err = 'table alias must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->alias = $alias;
	}

    /**
     * @param   array   $primaryKey
     * @return  null
     */
    public function setPrimaryKey(array $primaryKey)
    {
        if (empty($primaryKey)) {
            return;
        }

        $this->primaryKey = $primaryKey;
    }

    /**
     * @return  array
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}
