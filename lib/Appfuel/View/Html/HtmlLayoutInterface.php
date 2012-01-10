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

use Appfuel\View\FileViewInterface;

/**
 * Interface to manage the html view
 */
interface HtmlLayoutInterface extends FileViewInterface
{
	public function getViewKey();
	public function setViewKey($key);
	public function getInlineJsContent();
	public function setView(ViewInterface $view);
	public function getView();
}
