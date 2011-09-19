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
namespace Appfuel\Framework\View;

/**
 * Functionality required to build views
 */
interface ViewBuilderInterface
{
	/**
	 * @return	HtmlDocTemplateInterface
	 */
	public function createHtmlTemplate();

	/**
	 * @return	ConsoleViewTemplateInterface
	 */
	public function buildConsoleView($namespace = null);
}
