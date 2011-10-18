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
 */
interface ForeignKeyInterface 
{
	/**
	 * @return	array	
	 */
	public function getColumnNames();

	/**
	 * @param	string	name of the column thats part of the key
	 * @return	bool
	 */
	public function isKey($name);

	/**
	 * @return	string
	 */
	public function getReferenceTableName();

	/**
	 * @return	array
	 */
	public function getReferenceColumnNames();

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function isReferenceKey($name);
}
