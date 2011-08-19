<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Framework\Env;

/**
 * Wrapper for the using the pair date_default_timezone_(get/set)
 */
class Timezone 
{
	/**
	 * @param	string	$zone	timezone identifier
	 * @return	bool	return false if timezone identifier is invalid
	 */
	public function setDefault($zone)
	{
		return date_default_timezone_set($zone);
	}

	/**
	 * @return string
	 */
	public function getDefault()
	{
		return date_default_timezone_get();
	}
}
