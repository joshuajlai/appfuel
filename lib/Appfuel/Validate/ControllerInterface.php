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
namespace Appfuel\Validate;

/**
 * The controller is the facade that exposes a uniform interface for handling 
 * filters getting errors and clean data.
 */
interface ControllerInterface
{
    /**
	 * Will add one or more filters for a field to validator. Each field has
	 * its own validator and each validator can accept many filters.
	 * 
	 * @param	string	$field	field filters will be added for
	 * @param	mixed	$fltr	filter name or class the implements 
	 *							Filter\FilterInterface
	 * @param	array	$params optional paramters used by the filter
	 * @param	string	$err	optional error string for when filter fails
     * @return	ControllerInterface
     */
	public function addFilter($field, $fltr, array $params = null, $err = null);
	
	/**
	 * Set the source for the coordinator to the rawData and call isValid 
	 * method on all validators. If any should report errors return false
	 * otherwise return true
	 *
	 * @param	array $rawData
	 * @return	bool
	 */
	public function isSatisfiedBy(array $rawData);
	
	/**
	 * Flag used to determine is errors have been reported to the coordinator
	 * 
	 * @return	bool
	 */
	public function isError();
	
	/**
	 * @return	array
	 */
	public function getErrors();
	
	/**
	 * @param	string	$field
	 * @param	mixed	$default	return this value when clean data not found
	 * @return	mixed	
	 */
	public function getClean($field, $default = null);
	
	/**
	 * @return	array
	 */
	public function getAllClean();
}
