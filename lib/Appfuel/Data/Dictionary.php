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
namespace Appfuel\Data;

use Appfuel\Framework\Data\DictionaryInterface;

/**
 * A dictionary is an unordered collection of zero or more elements of some 
 * type.
 */
class Dictionary implements DictionaryInterface
{
	/**
	 * Items stored in the bag
	 * @var array
	 */
	protected $items = array();

	/**
	 * @param	array	$data
	 * @return	Bag
	 */
	public function __construct(array $data = array())
	{
		$this->load($data);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * @param	string	$key	item key
	 * @param	mixed	$value	
	 * @return	Bag
	 */	
	public function add($key, $value)
	{
		if (! is_scalar($key)) {
			return $this;
		}

		$this->items[$key] = $value;
		return $this;
	}

	/**
	 * @param	string	$key
	 * @param	mixed	$default		return value when not found
	 * @return	mixed
	 */
	public function get($key, $default = NULL)
	{
		if (! array_key_exists($key, $this->items)) {
			return $default;
		}

		return $this->items[$key];
	}

	/**
	 * @param	string $key
	 * @return	bool
	 */
	public function exists($key)
	{
		return array_key_exists($key, $this->items);
	}

	/**
	 * Return the entire dataset
	 * 
	 * @return array
	 */
	public function getAll()
	{
		return $this->items;
	}

	/**
	 * @param	array	$data
	 * @return	Bag
	 */
	public function load(array $data)
	{
		foreach ($data as $key => $value) {
			$this->add($key, $value);
		}

		return $this;
	}
}

