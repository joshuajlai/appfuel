<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Validate;

use InvalidArgumentException;

/**
 * Value object used to determine how a field is validated/filtered 
 */
interface FieldSpecInterface
{
	/**
	 * @return	string
	 */
	public function getField();

	/**
	 * @return	string
	 */
	public function getLocation();

	/**
	 * @return	string
	 */
	public function getFilters();

	/**
	 * @return	string
	 */
	public function getError();
}
