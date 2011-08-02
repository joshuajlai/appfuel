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
namespace Appfuel\Framework\Validate\Filter;

use Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * Filter raw input into a known clean value
 */
interface FilterInterface
{
    /**
     * @return mixed | special token string on failure
     */
	public function filter($raw, DictionaryInterface $params);

	/**
	 * Returns a string stoken not likely to occur in a typical value to 
	 * indicate a failure has occured
	 *
	 * @return	string
	 */
	public function failedFilterToken();
}
