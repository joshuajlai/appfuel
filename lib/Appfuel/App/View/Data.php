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
namespace Appfuel\App\View;

use Appfuel\Data\Dictionary;

/**
 * Handles all assignments of data to be used in a template
 */
class Data extends Dictionary
{
	/**
	 * Alias for add to add a key value pair to the template
	 * 
	 * @param	scalar	$key	label used to identify the value
	 * @param	mixed	$value
	 * @return	Data
	 */
	public function assign($key, $value)
	{
		return $this->add($key, $value);
	}
}
