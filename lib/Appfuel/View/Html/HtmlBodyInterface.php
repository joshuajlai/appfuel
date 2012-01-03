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
	Appfuel\View\ViewTemplate,
	Appfuel\View\ViewInterface,
	Appfuel\View\Html\Tag\BodyTag,
	Appfuel\View\Html\Tag\ScriptTag,
	Appfuel\View\Html\Tag\GenericTagInterface;

/**
 * Template used to generate generic html documents
 */
interface HtmlBodyInterface extends	ViewInterface
{
	/**
	 * @return	HtmlTagInterface
	 */
	public function getBodyTag();

	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlPage
	 */
	public function setBodyTag(GenericTagInterface $tag);

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
}
