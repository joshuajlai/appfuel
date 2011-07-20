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
	protected $domainDependencies = array();

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
	 * @return array
	 */
	public function getDependencies()
	{
		return $this->dependencies;
	}

	/**
	 * @return	return DbIdentity
	 */
	public function setDependencies(array $list)
	{
		$this->dependencies = $list;
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
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isString($str)
	{
		return is_string($str) && ! empty($str);
	}
}
