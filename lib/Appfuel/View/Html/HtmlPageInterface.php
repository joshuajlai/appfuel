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

use Appfuel\View\ViewInterface;

/**
 * Template used to generate generic html documents
 */
interface HtmlPageInterface extends ViewInterface
{
	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlPage
	 */
	public function setHtmlDoc(HtmlDocInterface $doc);

	/**	
	 * @return	HtmlDocInterface | null when not set
	 */
	public function getHtmlDoc();

	/**
	 * @param	FileViewInterface $view
	 * @return	HtmlPage
	 */
	public function setHtmlContent(ViewInterface $view);

	/**	
	 * @return	FileViewInterface	| null when not set
	 */
	public function getHtmlContent();

	/**
	 * @param	FileViewInterface $view
	 * @return	HtmlPage
	 */
	public function setInlineJs(ViewInterface $js);

	/**	
	 * @return	FileViewInterface	| null when not set
	 */
	public function getInlineJs();
}
