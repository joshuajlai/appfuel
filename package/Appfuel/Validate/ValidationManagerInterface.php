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
	 * @return	null
	 */
	static public function clearValidatorMap();

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function mapValidator($key);
	
	/**
	 * @param	string	$key
	 * @param	ValidatorInterface $validator
	 * @return	null
	 */
	static public function addValidatorToCache($key, ValidatorInterface $val);

	/**
	 * @param	string	$key
	 * @return	ValidatorInterface | false
	 */
	static public function getValidatorFromCache($key);

	/**
	 * @return	null
	 */
	static public function clearValidatorCache();

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function getValidator($key);

	/**
	 * @return	array
	 */
	static public function getFilterMap();

	/**
	 * @return	null
	 */
	static public function clearFilterMap();

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setFilterMap(array $map);

	/**
	 * @param	string	$key
	 * @param	ValidatorInterface	$validator
	 * @return	null
	 */
	static public function addFilterToCache($key, FilterInterface $filter);

	/**
	 * @param	string	$key
	 * @return	ValidatorInterface | false
	 */
	static public function getFilterFromCache($key);

	/**
	 * @return	null
	 */
	static public function clearFilterCache();

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function getFilter($key);

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
