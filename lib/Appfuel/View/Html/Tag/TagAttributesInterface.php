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
namespace Appfuel\View\Html\Tag;

/**
 */
interface TagAttributesInterface
{
	/**
	 * Flag used to determine if when adding an attribute that attribute 
	 * will be checked against a white list of allowed attributes
	 *
	 * @return bool
	 */
	public function isValidation();

	/**
	 * Turns on attribute validation. 
	 * 
	 * Requirements:
	 * 1) must always return a TagAttributeInterface
	 * 2) isValidation must always return true after this method is run
	 *
	 * @return TagAttributesInterface
	 */
	public function enableValidation();

	/**
	 * Turns off attribute validation. 
	 * 
	 * Requirements:
	 * 1) must always return a TagAttributeInterface
	 * 2) isValidation must always return false after this method is run
	 *
	 * @return TagAttributeInterface
	 */
	public function disableValidation();

	/**
	 * Returns a list of valid attributes
	 * 
	 * Requirements:
	 * 1) must always return an array
	 * 2) the returned array can be empty
	 * 3) when isGlobal is true merge the valid attributes into the global
	 *	  when isGloabl is false only return the valid attributes
	 * 
	 * @param	bool $isGlobal	default true
	 * @return	array
	 */
	public function getWhiteList($isGlobal = true);

	/**
	 * remove all valid attributes from the list
	 *
	 * Requirements:
	 * 1) always return the TagAttributesInterface
	 * 2) getWhiteList(false) should return an empty array after this is called
	 *
	 * @return	TagAttributesInterface
	 */
	public function clearWhiteList();

	/**
	 * Returns a list of html5 global attributes
	 *
	 * Its is recommended to keep the global attributes list static since it
	 * does not ever change
	 *
	 * @return	array
	 */
	public function getGlobalAttributes();

	/**
	 * Adds the name of the attribute that will be considered a valid 
	 * attribute.
	 * 
	 * Requirements:
	 * 1) It is an InvalidArgumentException to have an attribute name that
	 *	  is a non empty string. This includes additional whitespaces.
	 * 2) the name should be trimed to remove whitespaces from left and right
	 * 3) duplicate names are not allowed but do not raise any Exceptions
	 * 4) allways return a TagAttributesInterface, accept in the case of an
	 *	  InvalidArgumentException
	 *
	 * @throws	InvalidArgumentException
	 * @param	string	$name
	 * @return	TagAttribtuesInterface
	 */
	public function addToWhiteList($name);

	/**
	 * Requirements:
	 * 1) delegate each item to TagAttributesInterface::addToWhiteList
	 * 2) always return a TagAttributesInterface
	 *
	 * @param	array	$names	list of valid attributes
	 * @return	Tag
	 */
	public function loadWhiteList(array $list);

	/**
	 * Used by TagAttibutes::add to determine if an attrubute can be added
	 *
	 * Requirements:
	 * 1) if not an non empty string return false
	 * 2) if validation is disabled always return true
	 * 3) if in global attributes or valid attributes return true otherwise
	 *    return false
	 * 
	 * @param	string $name	
	 * @return	bool
	 */
	public function isValid($name);

	/**
	 * Adds an attribute to the list.
	 * 
	 * Requirements:
	 * 1) it is an InvalidArgumentException for any empty string or non string
	 *    to be added as the $name param
	 * 2) if validation is disabled then non empty string can be added with
	 *    any string value or null
	 * 3) if validation is enabled and $name fails validation then it is a 
	 *	  RunTimeException
	 * 4) if validation is enabled and $name passes then name maybe added to 
	 *	  the list
	 * 5) return TagAttributesInterface
	 *
	 * Note: any null value indicated that $name is an enumerated attribute
	 *
	 * @param	string	$name
	 * @param	string	$value	default is null
	 * @return	Tag
	 */
	public function add($name, $value = null);
	
	/**
	 * Requirements:
	 * 1) Delegate each item to TagAttributesInterface::add
	 * 2) return TagAttributesInterface 
	 *
	 * @param	array	$list
	 * @return	TagAttributesInterface
	 */
	public function load(array $list);

	/**
	 * Get an assigned attribte
	 * 
	 * Requirements:
	 * 1) if $name is empty of not a string return $default
	 * 2) if $name does not exist as an attribute return $default
	 * 3) if $name exists return name
	 * 
	 * @param	string	$name
	 * @param	mixed	$default default null
	 * @return	string | $default
	 */
	public function get($name, $default = null);

	/**
	 * Get a list of all attributes that have been added
	 *
	 * @return array
	 */
	public function getAll();

    /**
     * Clear out any current attributes
     * 
	 * Requirements:
	 * 1) TagAttributesInterface::getAll should return an empty array after 
	 *    this is called
	 * 2) return TagAttributesInterface
	 *
     * @return  TagAttributesInterface
     */
    public function clear();

	/**
	 * Determine if an attribute has been added
	 * 
	 * Requirements:
	 * 1) if $name is empty of not a string return false
	 * 2) if $name is not located in the list of added attributes return false
	 * 3) if $name is located in the list of added attributes return true
	 *
	 * @param	string	$name
	 * @return	bool
	 */
	public function exists($name);

	/**
	 * Return the number of attributes added
	 *
	 * Requirements:
	 * 1) the initial count is always zero
	 * 2) each attribute that is added increments the count by one
	 * 3) always return the current number of attributes added
	 *
	 * @return int
	 */
	public function count();

	/**
	 * Build a string of html5 attributes from the list of added attributes
	 *
	 * Requirements:
	 * 1) when no attributes are added return an empty string
	 * 2) when an attribute has a value of null treat it as an enumerated 
	 *    attribute an print the name with no quotes
	 * 3) return a single string of attributes where each attribute name/value
	 *    or name is separated by a space
	 *
	 * @return string
	 */
	public function build();

	/**
	 * Allow attributes to be used in the context of a string
	 * 
	 * Requirements
	 * 1) delagate to TagAttributesInterface::build 
	 * 2) make sure no exceptions are thrown
	 * 3) always return a string
	 *
	 * @return string
	 */
	public function __toString();
}
