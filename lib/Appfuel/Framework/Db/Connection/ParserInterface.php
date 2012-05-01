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
namespace Appfuel\Framework\Db\Connection;


/**
 * Parses a connection string into name/value pairs that will be used to 
 * create a connection detail object
 */
interface ParserInterface
{
	/**
	 * @param	string	$connectionString
	 * @param	bool	$isDictionary		return the results as a dictionary
	 * @return	mixed	Dictionary | array | false on failure
	 */
	public function parse($connectionString, $isDictionary = true);
}
