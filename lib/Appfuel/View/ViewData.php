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
namespace Appfuel\View;

use InvalidArgumentException,
	Appfuel\DataStructure\Dictionary;

/**
 * Container used to hold assignments into the view. Unlike a dictionary
 * this utilizes the concept of namespaces. When no namespace is given then
 * the assignment is kept in global
 */
class ViewData implements ViewDataInterface
{
	/**
	 * List of name => value pairs separated into namespaces
	 * @var array
	 */
	protected $data = array(
		'global' => array(),
	);

	/**
	 * @param	string	$key
	 * @param	mixed	$value
	 * @return	ViewTemplate
	 */
	public function assign($key, $value, $ns = null)
	{
		if (! is_string($key) || strlen($key) === 0) {
			$err = "assignment key must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		if (null === $ns) {
			$ns = 'global';
		}
		else if (! is_string($ns) || strlen($ns) === 0) {
			$err = "namespace given must be non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->data[$ns][$key] = $value;
		return $this;
	}

	/**
	 * @param	string	$key
	 * @param	mixed	$default
	 * @param	string	$ns
	 * @return	mixed
	 */
	public function get($key, $default = null, $ns = null)
	{
		if (! is_string($key) || strlen($key) === 0) {
			return $default;
		}

		if (null === $ns) {
			$ns = 'global';
		}

		if (! isset($this->data[$ns]) || ! isset($this->data[$ns][$key])) {
			return $default;
		}

		return $this->data[$ns][$key];
	}

	/**
	 * @param	string	$ns
	 * @return	array
	 */
	public function getAll($ns = null)
	{
		if (null === $ns) {
			$ns = 'global';
		}

		if (! isset($this->data[$ns])) {
			return false;
		}

		return $this->data[$ns];
	}

	/**
	 * @return	array
	 */
	public function getAllNamespaces()
	{
		return $this->data;
	}
}
