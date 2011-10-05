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
namespace Appfuel\Db\Mysql\Constraint;

use Appfuel\Framework\Exception,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * The abstract tpe handles the common details of all mysql constraints
 */
abstract class AbstractConstraint
{
	/**
	 * Text used in sql statements to represent this constraint
	 * @var string
	 */
	protected $sqlString = null;

	/**
	 * Flag used to determine if the sql string will be uppercase
	 * @var bool
	 */
	protected $isUpperCase = false;

	/**
	 * This is depends on the constraint. NotNull has no value while
	 * unique and primary key use it the column name
	 * @var	string
	 */
	protected $value = null;

	/**
	 * List of columns that this constaint controls
	 * @var array
	 */
	protected $columns = array();

	/**
	 * @param	string	$sql	string used in sql statements
	 * @param	string	$validator	name of the validator for this type
	 * @param	DictionaryInterface	$attrs		dictionary of type attributes
	 * @return	AbstractType
	 */
	public function __construct($sql) 
	{
		$this->setSqlString($sql);
	}

	/**
	 * @return	string
	 */
	public function buildSql()
	{
		$sql = $this->getSqlString();
		if ($this->isUpperCase()) {
			$sql = strtoupper($sql);
		}
		else {
			$sql = strtolower($sql);
		}
		
		return $sql;
	}

	/**
	 * @return	AbstractType
	 */
	public function enableUpperCase()
	{
		$this->isUpperCase = true;
		return $this;
	}

	/**
	 * @return	AbstractType
	 */
	public function disableUpperCase()
	{
		$this->isUpperCase = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isUpperCase()
	{
		return $this->isUpperCase;
	}

	/**
	 * @return	string
	 */
	public function getSqlString()
	{
		return $this->sqlString;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string
	 * @return	null
	 */
	protected function setSqlString($sql)
	{
		if (empty($sql) || ! is_string($sql)) {
			throw new Exception("sql string must be a non empty string");
		}

		$this->sqlString = $sql;
	}

	/**
	 * @return	string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return	array
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * @param	string	$name	name of the column
	 * @return	AbstractConstraint
	 */
	protected function addColumn($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new Exception("column name must be a non empty string");
		}

		if (in_array($name, $this->columns)) {
			return $this;
		}

		$this->columns[] = $name;
		return $this;
	}

	protected function buildColumnString()
	{
		if (empty($this->columns)) {
			return '';
		}

		return implode(',', $this->columns);
	}
}
