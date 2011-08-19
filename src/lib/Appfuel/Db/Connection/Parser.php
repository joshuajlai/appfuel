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

use Appfuel\Framework\Exception,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\Db\Connection\ParserInterface;

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
	public function parse($conn, $isDictionary = true)
	{
		if (! is_string($conn) || empty($conn)) {
			return false;
		}
		
		$parts   = explode(';', $conn);
		if (! $parts) {
			return false;
		}

		$result = array();
		foreach ($parts as $section) {
			if (! is_string($section) || empty($section)) {
				continue;
			}

			$keyValue = explode('=', $section);
			if (! is_array($keyValue) || empty($keyValue)) {
				continue;
			}

			/* ensures key=value is key 0,1 */
			if (! isset($keyValue[0]) || ! isset($keyValue[1])) {
				continue;
			}
			$key   = $keyValue[0];
			$value = $keyValue[1];

			$result[$key] = $value;
		}

		if (true === $isDictionary) {
			$result = new Dictionary($result);
		}

		return $result;
	}
}
