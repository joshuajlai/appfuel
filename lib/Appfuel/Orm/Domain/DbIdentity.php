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
namespace Appfuel\Orm\Domain;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\DbDomainIdentityInterface;

/**
 * Maps the domain members to a database table and columns
 */
class DbIdentity implements DbDomainIdentityInterface
{
	/**
	 * List of the initial member marshalled into the domain
	 * @var array
	 */
	protected $map = array();

	/**
	 * Name of the table this domain belongs too
	 * @var array
	 */
	protected $table = null;

	/**
	 * List of domains columns that represent the primary key
	 * @var string
	 */
	protected $primaryKey = array();

	/**
	 * Label used to refer to this domain so you don't have to use 
	 * the class name
	 * @var string
	 */
	protected $label = null;

	/**
	 * List of domain labels this domain has access to and there relationships
	 * @var array
	 */
	protected $dependencies = array();

	/**
	 * @return	array
	 */
	public function getMap()
	{
		return $this->map;
	}

	/**
	 * @param	array	$map
	 * @return	DbIdentity
	 */
	public function setMap(array $map)
	{
		if (empty($map)) {
			throw new Exception("setMap failed: domain map can not be empty");
		}

		$err = "invalid column map: column of member is not a valid string";
		foreach ($map as $member => $column) {
			if (! $this->isString($column) || ! $this->isString($member)) {
				throw new Exception($err);
			}
		}

		$this->map = $map;
		return $this;
	}

	/**
	 * returns a list of all columns for this domain 
	 * 
	 * @return array
	 */
	public function getAllColumns()
	{
		return array_values($this->map);
	}

	/**
	 * return the domain member for this column given or false if no column
	 * is mapped
	 *
	 * @param	string	$columnName
	 * @return	string	| false on failure
	 */
	public function mapToColumn($member)
	{
		if (empty($member) || ! is_string($member)) {
			return false;
		}

		return array_search($member, $this->map, true);
	}

	/**
	 * Flag used to determine if a column exists
	 * 
	 * @return bool
	 */
	public function isColumn($columnName)
	{
		$result = false;
		if (empty($columnName) || ! is_string($columnName)) {
			return $result;
		}

		return array_key_exists($columnName, $this->map);
	}

	/**
	 * reuturn the member name mapped for this column
	 *
	 * @param	string	$columnName
	 * @return	string | false on failure
	 */
	public function mapToMember($columnName)
	{
		if (empty($columnName) || ! is_string($columnName)) {
			return false;
		}

		if (! array_key_exists($columnName, $this->map)) {
			return false;
		}

		return $this->map[$columnName];
	}

	/**
	 * @param	string	$memberName
	 * @return	bool
	 */
	public function isMember($memberName)
	{
		if (empty($memberName) || ! is_string($memberName)) {
			return false;
		}

		$result = array_search($memberName, $this->map, true);
		if (! $result) {
			return false;
		}

		return true;
	}

	/**
	 * returns a list of all domain members for this domin
	 * 
	 * @return	array
	 */
	public function getAllMembers()
	{
		return array_keys($this->map);
	}

	/**
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @return	DbIdentity
	 */
	public function setTable($name)
	{
		if (! $this->isString($name)) {
			throw new Exception("table name must be a valid string");
		}
		$this->table = $name;
		return $this;
	}

	/**
	 * @return	array of member that map to primary key columns
	 */
	public function getPrimaryMembers()
	{
		$columns = $this->getPrimaryKey();
		$members = array();
		
		$err = 'key corruption detected for ';
		foreach ($columns as $column) {
			$member = $this->mapToMember($column);
			echo "\n", print_r($this,1), "\n";exit;
			if (! $member) {
				throw new Exception("$err $column member not mapped");
			}

			$members[] = $member;
		}

		return $members;
	}

	/**
	 * @return string
	 */
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	/**
	 * All columns in the key must exist in the domain map before the key
	 * is accepted.
	 *
	 * @throws	Appfuel\Framework\Exception
	 * @param	array	$key
	 * @return	DbIdentity
	 */
	public function setPrimaryKey(array $key)
	{
		$err = 'setPrimaryKey failed: key must be';
		foreach ($key as $column) {
			if (! array_key_exists($column, $this->map)) {
				throw new Exception("$err mapped, key not found as ($column)");
			}	
		}

		$this->primaryKey = $key;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @return	DbIdentity
	 */
	public function setLabel($label)
	{
		if (! $this->isString($label)) {
			throw new Exception("label must be a valid string");
		}
		$this->label = $label;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDependencies()
	{
		return $this->dependencies;
	}

	/**
	 * @return	DbIdentity
	 */
	public function setDependencies(array $list)
	{
		$err = "invalid dependency list ";
		$validTypes = array('root', 'child');
		$validRelations = array('one-one', 'one-many', 'many-many');
		foreach ($list as $label => $depends) {

			if (! is_string($label) || empty($label)) {
				throw new Exception("$err label must be a non empty string");
			}

			if (! array_key_exists('type',	$depends)) {
				throw new Exception("$err type is missing from $label");
			}

			if (! in_array($depends['type'], $validTypes)) {
				$err .= "incorrect type, must be (root|child) ";
				throw new Exception("$err error occurred on $label");
			}
					   
			if (! array_key_exists('relation',	$depends)) {
				throw new Exception("$err relation is missing from $label");
			}

			if (! in_array($depends['relation'], $validRelations)) {
				$err .= "incorrect relation, must be ";
				$err .= " (one-one|one-many|many-may) ";
				throw new Exception("$err error occurred on $label");
			}
		}
					   
		$this->dependencies = $list;
		return $this;
	}

	/**
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isString($str)
	{
		return is_string($str) && ! empty($str);
	}
}
