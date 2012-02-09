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

use InvalidArgumentException;

/**
 * Value object used to describe the properties needed by the html page 
 * builder and configure an html page.
 */
interface HtmlPageDetailInterface
{
	/**
	 * @return	bool
	 */
	public function isTagFactory();

	/**
	 * @return	string
	 */
	public function getTagFactory();

	/**
	 * @return	bool
	 */
	public function isHtmlPageClass();

	/**
	 * @return	string
	 */
	public function getHtmlPageClass();

	/**
	 * @return	bool
	 */
	public function isHtmlDoc();

	/**
	 * @return	string
	 */
	public function getHtmlDoc();

	/**
	 * @return	bool
	 */
	public function isHtmlConfig();

	/**
	 * @return	string
	 */
	public function getHtmlConfig();

	/**
	 * @return	bool
	 */
	public function isLayoutTemplate();

	/**
	 * @return	string
	 */
	public function getLayoutTemplate();

	/**
	 * @return	bool
	 */
	public function isInlineJsTemplate();
	
	/**
	 * @return	mixed
	 */
	public function getInlineJsTemplate();

	/**
	 * @return	bool
	 */
	public function isViewTemplate();

	/**
	 * @return	mixed
	 */
	public function getViewTemplate();
}
