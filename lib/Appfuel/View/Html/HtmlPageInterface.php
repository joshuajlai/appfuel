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

use Appfuel\View\ViewInterface,
	Appfuel\View\Html\Tag\HtmlTagInterface,
	Appfuel\View\Html\Tag\GenericTagInterface;

/**
 * Template used to generate generic html documents
 */
interface HtmlPageInterface extends ViewInterface
{
    /**
     * @param   string  $name
     * @param   string  $value
     * @return  HtmlPage
     */
    public function addHtmlAttribute($name, $value = null);

	/**
	 * @param	ViewInterface $view
	 * @return	HtmlPage
	 */
	public function setView(ViewInterface $view);

	/**	
	 * @return	ViewInterface
	 */
	public function getView();
}
