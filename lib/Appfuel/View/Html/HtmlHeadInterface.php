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
namespace Appfuel\View\Html;

use InvalidArgumentException,
	Appfuel\View\Html\Tag\HeadTag,
	Appfuel\View\Html\Tag\ScriptTag,
	Appfuel\View\Html\Tag\TagContent,
	Appfuel\View\Html\Tag\HeadTagInterface,
	Appfuel\View\Html\Tag\GenericTagInterface,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;

/**
 * Template used to generate generic html documents
 */
interface HtmlHeadInterface
{
	/**
	 * @return	HtmlTagInterface
	 */
	public function getHeadTag();

	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlPage
	 */
	public function setHeadTag(HeadTagInterface $tag);

	/**	
	 * @param	string	$name
	 * @return	string	$value
	 */
	public function addAttribute($name, $value = null);

	/**
	 * @param	string	$name
	 * @param	string	$default
	 * @return	mixed
	 */
	public function getAttribute($name, $default = null);

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function isAttribute($name);

	/**
	 * @return	bool
	 */
	public function isJs();

	/**
	 * @return	HtmlBody
	 */
	public function enableJs();

	/**
	 * @return	HtmlBody
	 */
	public function disableJs();
	
	/**
	 * @return	bool
	 */
	public function isCss();

	/**
	 * @return	HtmlHead
	 */
	public function enableCss();

	/**
	 * @return	HtmlHead
	 */
	public function disableCss();
	
	/**
	 * @return	ScriptTag
	 */
	public function getInlineScriptTag();

	/**
	 * @param	GenericTagInterface	 $tag
	 * @return	HtmlBody
	 */
	public function setInlineScriptTag(GenericTagInterface $tag);

	/**
	 * @param	mixed	string | object supporting __toString
	 * @return	HtmlBody
	 */
	public function addInlineScriptContent($data);

	/**
	 * @param	int	$index 
	 * @return	string | array
	 */
	public function getInlineScriptContent($index = null);

	/**
	 * @return	string
	 */
	public function getInlineScriptContentString();

	/**
	 * @param	mixed	$src
	 * @return	HtmlBody
	 */
	public function addScript($src);

	/**
	 * @return	array
	 */
	public function getScripts();

	/**
	 * @return	int
	 */
	public function getScriptCount();

	/**
	 * @return	string
	 */
	public function build();
}
