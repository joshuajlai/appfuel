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
interface GenericTagInterface
{
	/**
	 * @return string
	 */
	public function getTagName();

	/**
	 * Name used in the built tag string
	 *
	 * Requirements
	 * 1) must be a non empty string
	 *    throw an InvalidArgumentException when empty or not a string
	 * 2) return HtmlTagInterface
	 *
	 * @throws	InvalidArgumentException
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
	 * @param	array	$attrs	list of attributes to add
	 * @return	Tag
	 */
	public function loadAttributes(array $attrs);

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	Tag
	 */
	public function addAttribute($name, $value = null);

	/**
	 * returns the value assigned to the attribute name given.
	 *
	 * @param	string	$name
	 * @return	string
	 */
	public function getAttribute($name, $default = null);

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function isAttribute($name);

	/**
	 * @return string
	 */
	public function getAttributeString();

	/**
	 * Requirements	
	 * 1) delegate to TagContentInterface::load(list $data)
	 * 2) use a fluent interface
	 *
	 * @param	array	$list
	 * @return	GenericTagInterface
	 */
	public function loadContent(array $list);

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
	public function getContent($index = null);

	/**
	 * Wrapper that delegates to TagContentInterface::clear
	 *
	 * @return	bool
	 */
	public function clearContent($index = null);

	/**
	 * Build the content by concatenating each item in the content array,
	 * use the separator between each item.
	 *
	 * @return string
	 */
	public function getContentString();

	/**
	 * Number of content blocks added to the TagContentInterface
	 *
	 * @return	int
	 */
	public function getContentCount();

	/**
	 * @return	string
	 */
	public function getContentSeparator();

	/**
	 * Requirements:
	 * 1) delegate to TagContentInterface::setSeparator
	 * 2) must be a fluent interface
	 *
	 * @return	GenericTagInterface
	 */
	public function setContentSeparator($char);

	/**
	 * @return string
	 */
	public function build();

	/**
	 * @param	string|TagContentInterface	$content
	 * @param	string|TagAttributesInterface $attrs
	 * @return	string
	 */
	public function buildTag($content, $attrs = '');

	/**
	 * @return string
	 */
	public function __toString();
}
