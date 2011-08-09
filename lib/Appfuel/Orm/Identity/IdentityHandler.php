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
namespace Appfuel\Orm\Source\Db\Identity;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Source\Db\Identity\IdentityHandlerInterface;

/**
 * Maps the domain members to a database table and columns
 */
class IdentityHandler implements IdentityHandlerInterface
{
	/**
	 * Domain name used to refer to this domain so you don't have to use 
	 * the class name
	 * @var string
	 */
	protected $name = null;

	/**
	 * List of the initial member marshalled into the domain
	 * @var array
	 */
	protected $mappers = array();

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
	public function setMapper($name, closure $mapper)
	{
		$this->map[$name] = $mapper;
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
	public function getDomainName()
	{
		return $this->name;
	}

	/**
	 * @return	DbIdentity
	 */
	public function setDomainName($label)
	{
		if (! $this->isString($label)) {
			throw new Exception("label must be a valid string");
		}
		$this->name = $label;
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
	 * Returns the namespace of the dependent identity. Used to create
	 * that identity
	 *
	 * @param	string	$domainName
	 * @return	string | false on failure
	 */
	public function getDependentClass($domainName)
	{
		if (! $this->isDependent($domainName)) {
			return false;
		}

		return $this->dependencies[$domainName]['class'];
	}

	/**
	 * @param	string	$domainName
	 * @return	string | false on failure
	 */
	public function getDependentType($domainName)
	{
		if (! $this->isDependent($domainName)) {
			return false;
		}

		return $this->dependencies[$domainName]['type'];
	}

	/**
	 * @param	string	$domainName
	 * @return	string | false on failure
	 */
	public function getDependentRelation($domainName)
	{
		if (! $this->isDependent($domainName)) {
			return false;
		}

		return $this->dependencies[$domainName]['relation'];
	}


	/**
	 * @param	string domainName
	 * @return	bool
	 */
	public function isDependent($domainName)
	{
		if (! $this->isString($domainName)) {
			return false;
		}

		return array_key_exists($domainName, $this->dependencies);
	}

	/**
	 * The dependecy holds an array data structure that details information
	 * about other domains this domain is allowed access to. The datastructure
	 * follows this format:
	 *
	 * domain-key: array (
	 *		'type'		=> string <root|child>
	 *		'relation'	=> string <one-one|one-many|many-many|none>
	 *		'class'		=> string <(fully qualified class name of the identity)>
	 * );
	 * 
	 * All these keys must exist with a domain key because they are used by
	 * the framework to create other identities or generate automated sql.
	 *
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

			if (! isset($depends['type'])) {
				throw new Exception("$err type is missing from $label");
			}

			if (! in_array($depends['type'], $validTypes)) {
				$err .= "incorrect type, must be (root|child) ";
				throw new Exception("$err error occurred on $label");
			}
					   
			if (! isset($depends['relation'])) {
				throw new Exception("$err relation is missing from $label");
			}

			if (! in_array($depends['relation'], $validRelations)) {
				$err .= "incorrect relation, must be ";
				$err .= " (one-one|one-many|many-may) ";
				throw new Exception("$err error occurred on $label");
			}

			if (! isset($depends['class'])) {
				throw new Exception("$err domain namespace is missing");
			}

			$class = $depends['class'];
			if (! is_string($class) || empty($class)) {
				$err .= "damain class name must be a non empty string";
				throw new Exception($err);
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
