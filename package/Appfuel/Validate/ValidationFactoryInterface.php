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

use Appfuel\Validate\Filter\FilterInterface;

/**
 * Creates validators and filters based on a static map.
 */
interface ValidationFactoryInterface
{
	/**
	 * @return	string
	 */
	static public function getCoordinatorClass();

	/**
	 * @param	string	$class
	 * @return	null
	 */	
	static public function setCoordinatorClass($class);

	/**
	 * @return	null
	 */
	static public function clearCoordinatorClass();

	/**
	 * @return	CoordinatorInterface
	 */
	static public function createCoordinator();

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
	 * @return	null
	 */
	static public function clearValidatorMap();

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function mapValidator($key);
	
	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function createValidator($key);

	/**
	 * @return	array
	 */
	static public function getFilterMap();

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setFilterMap(array $map);

	/**
	 * @return	null
	 */
	static public function clearFilterMap();

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function createFilter($key);

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function mapFilter($key);

	/**
	 * Clears the validator map, validator cache, filter map and fitler cache
	 * @return	null
	 */
	static public function clear();
}
