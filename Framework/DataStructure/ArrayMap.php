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

use Appfuel\Framework\Exception;

/**
 * Provide generic key to value and value to mapping, provide all items
 * in the map (key and value) are scalar in nature. Also provides closures
 * in the form of keyToValue and valueToKey getters.
 */
class ArrayMap implements ArrayMapInterface
{
	/**
	 * List of key value pairs
	 * @var array
	 */
	protected $data = array();

	/**
	 * @param	array $map
	 * @return	Mapper
	 */
	public function __construct(array $data)
	{
		$this->setMap($data);
	}

	/**
	 * @return	Closure
	 */
	public function getKeyToValueMapper()
	{
		$map = $this->getMap();
		return function ($key) use (&$map) {
			if (empty($key) || ! is_scalar($key)) {
				return false;
			}

			if (! isset($map[$key])) {
				return false;
			}

		return $map[$key];

		};
	}

	/**
	 * @return	Closure
	 */
	public function getValueToKeyMapper()
	{
		$map = $this->getMap();
		return function ($value, $isStrict = true) use (&$map) {
			if (empty($value) || ! is_scalar($value)) {
				return false;
			}

			$isStrict =(bool) $isStrict;
			return array_search($value, $map, $isStrict);
		};
	}

	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->data;
	}

	/**
	 * Returns a list of all columns
	 *
	 * @return	array
	 */
	public function getKeys()
	{
		return array_keys($this->data);
	}

	/**	
	 * @return	array
	 */
	public function getValues()
	{
		return array_values($this->data);
	}

	/**
	 * @param	string	$member	  domain member to be mapped to column
	 * @return	string | false when not found or invalid
	 */
	public function keyToValue($key)
	{
		if (empty($key) || ! is_scalar($key)) {
			return false;
		}

		if (! isset($this->data[$key])) {
			return false;
		}

		return $this->data[$key];
	}
	
	/**
	 * @param	string	$member	  domain member to be mapped to column
	 * @return	string | false when not found or invalid
	 */
	public function valueToKey($value, $isStrict = true)
	{
		if (empty($value) || ! is_scalar($value)) {
			return false;
		}

		$isStrict =(bool) $isStrict;
		return array_search($value, $this->data, $isStrict);
	}
	
	/**
	 * Validate that both column and members are valid strings then assign
	 *
	 * @return	null
	 */
	protected function setMap(array $data)
	{
		$err = "must be a non empty string";
		foreach ($data as $key => $value) {
			if (empty($key) || ! is_scalar($key)) {
				throw new Exception("map key ($key) $err");
			}

			if (empty($value) || ! is_scalar($value)) {
				throw new Exception("map value $err");
			}
		}

		$this->data = $data;
	}
}
