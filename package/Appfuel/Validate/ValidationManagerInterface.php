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

/**
 * Creates validators and filters based on a static map.
 */
interface ValidationManagerInterface
{
	/**
	 * @return	array
	 */
	static public function getValidatorMap();

	/**
	 * @param	array	$map	
	 * @return	null
	 */
	static public function setValidatorMap(array $map);

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function mapValidator($key);

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function getValidator($key);
}
