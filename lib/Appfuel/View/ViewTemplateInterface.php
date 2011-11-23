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
 * Interface needed by the framework to use view templates
 */
interface ViewTemplateInterface
{
	/**
	 * Load will assign a list of key/value pairs into the template
	 * 
	 * @param	array $data
	 * @return	ViewTemplateInterface
	 */
	public function load(array $data);
	
	/**
	 * Assign a key value pair into the template
	 * 
	 * @throws	Appfuel\Framework\Exception
	 * @param	scalar	$key
	 * @param	mixed	$value
	 * @return	ViewTemplateInterface
	 */
	public function assign($key, $value);

	/**
	 * Get a value that was assigned by key into the template. When the
	 * key does not exist return default
	 *
	 * @param	scalar	$key
	 * @param	mixed	$default
	 * @return	mixed
	 */
	public function getAssigned($key, $default = null);
	
	/**
	 * Determines if a key has been assigned into a template
	 *
	 * @param	scalar	$key
	 * @return	bool
	 */
	public function isAssigned($key);

	/**
	 * Returns all assigned key value pairs
	 * 
	 * @return	array
	 */
	public function getAllAssigned();

	/**
	 * Use the formatter to convert the assigned data into a string.
	 * merge or format the passed in data depending on the isPrivate flag.
	 * When isPrivate is true format and return only the given from the
	 * first paramater. When isPrivate is false and data is passed in then
	 * merge it with the assigned data and format it into a string
	 *
	 * @param	array	$data		additional or private data to build
	 * @param	bool	$isPrivate	determines if only $data is used
	 * @return	string
	 */
	public function build(array $data = null, $isPrivate = false);
	
	/**
	 * Return the template as a string. Calls build
	 * 
	 * @return	string
	 */
	public function __toString();
}
