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

/**
 * Container used to hold assignments into the view. Unlike a dictionary
 * this utilizes the concept of namespaces. When no namespace is given then
 * the assignment is kept in global
 */
interface ViewDataInterface
{
	/**
	 * @param	string	$key
	 * @param	mixed	$value
	 * @return	ViewTemplate
	 */
	public function assign($key, $value, $ns = null);

	/**
	 * @param	string	$key
	 * @param	mixed	$default
	 * @param	string	$ns
	 * @return	mixed
	 */
	public function get($key, $default = null, $ns = null);

	/**
	 * @param	string	$ns
	 * @return	array
	 */
	public function getAll($ns = null);

	/**
	 * @return	array
	 */
	public function getAllNamespaces();
}
