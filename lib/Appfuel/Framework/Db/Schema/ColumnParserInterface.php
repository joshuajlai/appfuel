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
 * Parser space delimited strings into dictionary objects
 */
interface ColumnParserInterface
{	
	/**
	 * Flag used to determine if an error message has been set
	 * 
	 * @return	bool
	 */
	public function isError();
	
	/**
	 * @return	string
	 */
	public function getError();

	/**
	 * Extract the column name while taking into consideration database 
	 * identifiers. Returns an array with keys 'column-name' and 
	 * 'input-string'. The column name is to be removed from the input string
	 * and the new input string is returned with its key 'input-string'.
	 *
	 * @param	string	$column
	 * @return	array
	 */
	public function extractColumnName($column);

	/**
	 * @param	string	$str	column definition
	 * @return	Appfuel\Framework\DataStructure\DictionaryInterface
	 */
	public function parseColumn($str);
}
