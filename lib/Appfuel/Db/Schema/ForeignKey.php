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
namespace Appfuel\Db\Schema;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Schema\ForeignKeyInterface;

/**
 * A foreign key is one or more columns that reference one or more columns
 * in another table. The table the foreign key belongs to is assumed to be 
 * the table object it it held in.
 */
class ForeignKey implements ForeignKeyInterface
{
	/**
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Name of the referring table
	 * @var string
	 */
	protected $refTable = null;

	/**
	 * List of columns that belong to the referring table
	 * @var array
	 */
	protected $refColumns = array();

	/**
	 * @param	array|DictionaryInterface	$data  list of column attrs
	 * @return	Column
	 */
	public function __construct($columns, $table, $refColumns)
	{
		$this->setColumnNames($columns);
		$this->setReferenceTableName($table);
		$this->setReferenceColumnNames($refColumns);
	}

	/**
	 * @return	string
	 */
	public function getColumnNames()
	{
		return $this->columns;
	}

	/**
	 * @return	bool
	 */
	public function isKey($name)
	{
		if (empty($name) || 
			! is_string($name) ||
			!  in_array($name, $this->columns)) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string
	 * @return	bool
	 */
	public function isReferenceKey($name)
	{
		if (empty($name) || 
			! is_string($name) ||
			!  in_array($name, $this->refColumns)) {
			return false;
		}

		return true;
	}

	/**
	 * @return	string
	 */
	public function getReferenceTableName()
	{
		return $this->refTable;
	}

	public function getReferenceColumnNames()
	{
		return $this->refColumns;
	}

	/**
	 * @param	string	$table	name of the reference table
	 * @return	null
	 */
	protected function setReferenceTableName($table)
	{
		if (empty($table) || ! is_string($table)) {
			throw new Exception("Reference table must be a non empty string");
		}

		$this->refTable = $table;
	}
	
	/**
	 * @param	mixed	string|array	$names
	 * @return	null
	 */
	protected function setReferenceColumnNames($names)
	{
		if (empty($names)) {
			throw new Exception("reference columns can not be empty");
		}

		$this->refColumns = $this->parseColumns($names);
	}

	/**
	 * @param	mixed	string|array	$names
	 * @return	null
	 */
	protected function setColumnNames($names)
	{
		if (empty($names)) {
			throw new Exception("columns can not be empty");
		}

		$this->columns = $this->parseColumns($names);
	}

	/**
	 * @param	mixed	string|array
	 * @return	array
	 */
	protected function parseColumns($names)
	{
		if (is_string($names)) {
			$columns = array($names);
		} 
		else if (is_array($names)) {
			foreach ($names as $index => $col) {
				if (empty($col) || ! is_string($col)) {
					throw new Exception("invalid columns at -($index)");
				}
			}
			$columns = $names;
		}
		else {
			throw new Exception("column(s) must be a string or array");

		}

		return $columns;
	}

}
