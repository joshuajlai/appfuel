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
 * A table's primary key can be made of one or more columns so we have a 
 * simple interface to hide those columns behind
 */
interface PrimaryKeyInterface 
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
}
