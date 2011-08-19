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
namespace Appfuel\Framework\DataStructure;

/**
 * Provide generic key to value and value to mapping, provide all items
 * in the map (key and value) are scalar in nature. Also provides closures
 * in the form of keyToValue and valueToKey getters.
 */
Interface ArrayMapInterface
{
	/**
	 * @return	Closure
	 */
	public function getKeyToValueMapper();

	/**
	 * @return	Closure
	 */
	public function getValueToKeyMapper();

	/**
	 * @return array
	 */
	public function getMap();

	/**
	 * Returns a list of all columns
	 *
	 * @return	array
	 */
	public function getKeys();

	/**	
	 * @return	array
	 */
	public function getValues();

	/**
	 * @param	string	$member	  domain member to be mapped to column
	 * @return	string | false when not found or invalid
	 */
	public function keyToValue($key);
	
	/**
	 * @param	string	$member		domain member to be mapped to column
	 * @param	bool	$isStrict	check against type and value
	 * @return	string | false when not found or invalid
	 */
	public function valueToKey($value, $isStrict = true);
}
