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
 * Handles all tags associated with the html head.
 */
interface HeadTagInterface extends GenericTagInterface
{
	/**
	 * @return	GenericTagInterface
	 */
	public function getTitle();

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function setTitle(GenericTagInterface $tag);

	/**
	 * @param	string	$text
	 * @param	string	$action	
	 * @return	HeadTag
	 */
	public function setTitleText($text, $action = 'append');

	/**
	 * @param	string	$char
	 * @return	HeadTag
	 */
	public function setTitleSeparator($char);

	/**
	 * @return	bool	
	 */
	public function isTitle();

	/**
	 * @return	GenericTagInterface
	 */
	public function getBase();

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function setBase(GenericTagInterface $tag);

	/**
	 * @return	bool	
	 */
	public function isBase();

	/**
	 * @throws	InvalidArgumentException
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function addMeta(GenericTagInterface $tag);

	/**
	 * @return	array
	 */
	public function getMeta();

	/**
	 * @return	bool
	 */
	public function isMeta();

	/**
	 * @throws	InvalidArgumentException
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function addLink(GenericTagInterface $tag);

	/**
	 * @return	array
	 */
	public function getLinks();

	/**
	 * @return	bool
	 */
	public function isLinks();

	/**
	 * @return	GenericTagInterface
	 */
	public function getStyle();

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function setStyle(GenericTagInterface $tag);

	/**
	 * @return	bool	
	 */
	public function isStyle();

	/**
	 * @throws	InvalidArgumentException
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function addScript(GenericTagInterface $tag);

	/**
	 * @return	array
	 */
	public function getScripts();

	/**
	 * @return	bool
	 */
	public function isScripts();

	/**
	 * @return	GenericTagInterface
	 */
	public function getInlineScript();

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function setInlineScript(GenericTagInterface $tag);

	/**
	 * @return	bool	
	 */
	public function isInlineScript();

	/**
	 * @return	array
	 */
	public function getContentOrder();

	public function setContentOrder(array $items);
}
