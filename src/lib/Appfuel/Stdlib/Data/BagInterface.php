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
namespace 	Appfuel\StdLib\Data;

/**
 * An bag object is an unordered collection of zero or more elements of some 
 * type.
 */
interface BagInterface extends \Countable
{
	/**
	 * @param	array	$data
	 * @return	Bag
	 */
	public function __construct(array $data = array());

	/**
	 * @param	string	$key	item key
	 * @param	mixed	$value	
	 * @return	Bag
	 */	
	public function add($key, $value);

	/**
	 * @param	string	$key
	 * @param	mixed	$default		return value when not found
	 * @return	mixed
	 */
	public function get($key, $default = NULL);

	/**
	 * @return array
	 */
	public function getAll();

	/**
	 * @param	array	$data
	 * @return	Bag
	 */
	public function load(array $data);
}

