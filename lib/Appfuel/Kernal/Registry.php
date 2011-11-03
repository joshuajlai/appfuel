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
namespace Appfuel\Kernal;

use Appfuel\DataStructure\DictionaryInterface,
	Appfuel\DataStructure\Dictionary;

/**
 * Decouples the kernal settings from the kernal
 */
class Registry
{
	/**
	 * Dictionary of data used by the application
	 * @var Dictionary
	 */
	static protected $dict = null;

	/**
	 * Holds a list of domain key to domain class mappings
	 * @var array
	 */
	static protected $dmap = array();

	/**
	 * Reset the registry with a new dataset and if one is not given
	 * then create an empty one
	 *
	 * @param	BagInterface	$bag
	 * @return	null
	 */
	static public function initialize($data = null, array $domainMap = null)
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

		if (null !== $domainMap) {
			self::$dmap = $domainMap;
		}
	}

	/**
	 * @return	array
	 */
	static public function getDomainMap()
	{
		return	self::$dmap;
	}

	/**
	 * @param	string	$key	domain key
	 * @return	string
	 */
	static public function getDomainClass($key)
	{
		if (empty($key) || !is_string($key) || ! isset(self::$dmap[$key])) {
			return false;
		}

		return self::$dmap[$key];
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
