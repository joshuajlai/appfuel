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
namespace Appfuel\App;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Data\DictionaryInterface,
	Appfuel\Data\Dictionary;

/**
 * Global Registry used by the framework to hold config data and handle 
 * startup and intialization
 */
class Registry
{
	/**
	 * Dictionary of data used by the application
	 * @var Dictionary
	 */
	static protected $dict = null;

	/**
	 * Reset the registry with a new dataset and if one is not given
	 * then create an empty one
	 *
	 * @param	BagInterface	$bag
	 * @return	null
	 */
	static public function initialize($data = null)
	{
		/*
		 * check is there is an array data with the initialization or if
		 * a Bag structure is manually being passed in otherwise create an 
		 * empty bag
		 */
		if (is_array($data)) {
			$dict = new Dictionary($data);
		} else if ($data instanceof DictionaryInterface) {
			$dict = $data;
		} else {
			$dict = new Dictionary();
		}

		self::$dict = $dict;
	}

	/**
	 * @return null
	 */
	static public function clear()
	{
		self::$dict = new Dictionary();
	}

	/**
	 * @return int
	 */
	static public function count()
	{
		return self::getDictionary()
					->count();
	}

	/**
	 * @param	string	$key
	 * @param	mixed	$default	returned when key is not found
	 * @return	mixed
	 */
	static public function get($key, $default = NULL)
	{
		if (! is_scalar($key)) {
			return $default;
		}

		return self::getDictionary()
					->get($key, $default);
	}

	/**
	 *	@param	string	$key
	 *	@param	mixed	$value
	 *  @return	null
	 */
	static public function add($key, $value)
	{
		if (! is_scalar($key)) {
			return false;
		}

		self::getDictionary()
			->add($key, $value);

		return true;
	}

	/**
	 * Get all data
	 *
	 * @return array
	 */
	static public function getAll()
	{
		return self::getDictionary()
					->getAll();
	}

	/**
	 * Load a dataset into the bag
	 * @param	array	$data	
	 * @return	null
	 */
	static public function load(array $data)
	{
		self::getDictionary()
			->load($data);

		return true;
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function exists($key)
	{
		return self::getDictionary()
					->exists($key);
	}

	/**
	 * Collect a series of name value pairs 
	 * 
	 * @param	array	$list		of keys to collect
	 * @return	array
	 */
	static public function collect(array $list, $array = false)
	{
		$result = array();
		foreach ($list as $index => $key) {
			if (! self::exists($key)) {
				continue;
			}
			$result[$key] = self::get($key);
		}

		if (true === $array) {
			return $result;
		}

		return new Dictionary($result);
	}

	/**
	 * @return BagInterface
	 */
	static protected function getDictionary()
	{
		if (! self::$dict instanceof DictionaryInterface) {
			throw new Exception(
				"Can not use the registry before it has been initialized"
			);
		}

		return self::$dict;
	}
}
