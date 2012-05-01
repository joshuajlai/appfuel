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
namespace Appfuel\Framework\Db\Schema;


/**
 * Table objects are immutable and make all assignments in the constructor
 * via an array or dictionary object. 
 */
interface TableInterface extends SchemaObjectInterface
{
	/**
	 * Name of the table
	 * @return	string
	 */
	public function getName();

	/**
	 * @param	string	$columnName
	 * @return	bool
	 */
	public function isColumn($columnName);

	/**
	 * @param	string	$columnName
	 * @return	ColumnInterface
	 */
	public function getColumn($columnName);

	/**
	 * @return	array of ColumnInterface's
	 */
	public function getAllColumns();

	/**
	 * @return	array of strings
	 */
	public function getAllColumnNames();
}
