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
interface StringParserInterface
{
	/**
	 * @param	string	$str	column definition
	 * @return	Appfuel\Framework\DataStructure\DictionaryInterface
	 */
	public function parseColumn($str);
}
