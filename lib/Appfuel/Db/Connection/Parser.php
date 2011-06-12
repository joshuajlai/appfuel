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
namespace Appfuel\Db\Connection;

use Appfuel\Data\Dictionary,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Connection\ParserInterface,

/**
 * Parse a connection string into it's individual components
 */
class Parser implements ParserInterface
{
	
	/**
	 * @param	string	$conn			the connection string
	 * @param	bool	$isDictionary	reutrns a dictionary instead of array
	 * @return	mixed Dictionary | array | false on failure
	 */
	public function parse($conn, $format = 'dictionary')
	{
		if (! is_string($conn) && ! empty($conn)) {
			return false;
		}

		$parts = explode(';', $conn);
	}
}
