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
namespace Appfuel\Action\Welcome;

use Appfuel\View\Html\HtmlViewTemplate;

/**
 * Template used to generate generic html documents
 */
class HtmlView extends HtmlViewTemplate
{
	/**
	 * @param	string				$path	relative path to template file
	 * @param	array				$data	data to be assigned
	 * @return	HtmlTemplate
	 */
	public function __construct()
	{
		$tpl = 'view/welecom/welcome-view.phtml';
		$htmlDoc = 'Appfuel\View\Html\HtmlDocTemplate';
		$requires = array();
		$enableFrameworkSeed = true;
		$layout	  = null;
		
		
	}
}
