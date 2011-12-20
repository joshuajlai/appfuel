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
namespace Appfuel\View\Html\Element;

/**
 * This class looks to model the html5 tag. It has attribute validation which
 * allows you to indicate which attributes are valid for a given tag. Also 
 * has added all the html5 global attributes sa valid attributes so they do 
 * not need to be declared with every class extending this. Currently this
 * class does not support the validation of the attribute values. The interface
 * always you to add and remove attributes and content. It also allows you to
 * enable or disable the closing tag as some tags do not require the closing
 * tag. There are seperate methods for building content, the attribute string
 * and the tag itself.
 */
interface HtmlTagInterface
{
	/**
	 * @return string
	 */
	public function getTagName();

	/**
	 * @param	string	name of the tag
	 * @return	Tag
	 */
	public function setTagName($name);
	
	/**
	 * @return bool
	 */
	public function isClosingTag();

	/**
	 * @return Tag
	 */
	public function enableClosingTag();

	/**
	 * @return Tag
	 */
	public function disableClosingTag();

	/**
	 * @return string
	 */
	public function getSeparator();

	/**
	 * @param	scalar	$char
	 * @return	Tag
	 */
	public function setSeparator($char);

	/**
	 * @return bool
	 */
	public function isAttributesEnabled();

	/**
	 * @return Tag
	 */
	public function enableAttributes();

	/**
	 * @return Tag
	 */
	public function disableAttributes();

	/**
	 * @return bool
	 */
	public function isAttributeValidation();

	/**
	 * @return Tag
	 */
	public function enableAttributeValidation();

	/**
	 * @return Tag
	 */
	public function disableAttributeValidation();

	/**
	 * @return array
	 */
	public function getAttributeWhiteList();

	/**
	 * remove all valid attributes from the list
	 *
	 * @return	Tag
	 */
	public function clearAttributeWhiteList();

	/**
	 * Add a valid attribute to the white list
	 *
	 * @param	string	$name
	 * @return	Tag
	 */
	public function addValidAttribute($name);

	/**
	 * @param	array	$names	list of valid attributes
	 * @return	Tag
	 */
	public function addValidAttributes(array $names);

	/**
	 * remove an attribute from the white list of valid attributes
	 *
	 * @param	string	$name	
	 * @return	Tag
	 */
	public function removeValidAttribute($name);

	/**
	 * Checks to see if attribute validation is enabled. When it is then it
	 * checks that the attribute is a valid string and is also located in the 
	 * white list of valid attributes. When attribute validation is disabled
	 * it only checks if the attribute name is a valid string. When attributes
	 * have been disabled it always returns false.
	 *
	 * @param	string $name	
	 * @return	bool
	 */
	public function isValidAttribute($name);

	/**
	 * @param	array	$attrs	list of attributes to add
	 * @return	Tag
	 */
	public function addAttributes(array $attrs);

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	Tag
	 */
	public function addAttribute($name, $value);

	/**
	 * @param	string	$name
	 * @return	Tag
	 */
	public function removeAttribute($name);

	/**
	 * returns the value assigned to the attribute name given.
	 *
	 * @param	string	$name
	 * @return	string
	 */
	public function getAttribute($name, $default = null);

	/**
	 * @return array
	 */
	public function getAttributes();

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function attributeExists($name);

	/**
	 * @return int
	 */
	public function attributeCount();

	/**
	 * Add content to the tag
	 * 
	 * @param	mixed	$data	
	 * @param	string	$action		what to do with the content
	 * @return	Tag
	 */
    public function addContent($data, $action = 'append');

	/**
	 * @return array
	 */
	public function getContent();

	/**
	 * @return int
	 */
	public function contentCount();

	/**
	 * Build the content by concatenating each item in the content array,
	 * use the separator between each item.
	 *
	 * @return string
	 */
	public function buildContent();

	/**
	 * Used to build a string of attr=value sets for the tag when 
	 * attributes are disabled it returns an empty string
	 *
	 * @return string
	 */
	public function buildAttributes();

	/**
	 * @return string
	 */
	public function build();

	/**
	 * @return string
	 */
	public function __toString();
}
