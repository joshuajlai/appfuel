<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Appfuel\StdLib\Ds\AfList;

use Appfuel\StdLib\Ds\AfIterator\Basic as BasicIterator;

/**
 * Basic List
 * 
 * @package		Appfuel
 * @category	Stdlib
 * @subcategory	AfList
 */
class Basic extends BasicIterator
{
	/**
	 * @param	scalar $key		
	 * @param	mixed  $default
	 * @return	mixed
	 */
	public function get($key, $default = NULL)
	{
		if (! $this->isKey($key)) {
			return $default;
		}

		return $this->data[$key];
	}

	/**
	 * @param	$key	used to identified item in list
	 * @return	bool
	 */
	public function isKey($key)
	{
		if (! $this->isKeyType($key)) {
			throw new Exception("Fail function call isItem:
				key given must be a scalar value"
			);
		}

		return array_key_exists($key, $this->data);
	}
}
