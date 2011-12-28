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
namespace Appfuel\Action\Welcome;

use Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\View\ViewTemplate,
	Appfuel\View\Html\HtmlPageInterface,
	Appfuel\View\Compositor\FileCompositor,
	Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * The landing page seen when you first install appfuel and have not modified
 * any routes
 */
class ActionController extends MvcAction
{
	/**
	 * The action is public 
	 * @param	array $codes
	 * @return	bool
	 */
	public function isContextAllowed(array $codes)
	{
		return true;
	}

    /**
     * @param   AppContextInterface  $context
     * @return  null 
     */
    public function process(MvcContextInterface $context)
	{
		$view = $context->getView();
		$assignTitle = 'content.title';
		$title = 'Welcome to the Appfuel Framework';
		
		$assignMsg =  'content.msg';
		$msg = 'some text will need to go here';
		if (! ($view instanceof HtmlPageInterface)) {
			$assignTitle = 'title';
			$assignMsg = 'msg';
		}
		$view->assign($assignTitle, $title)
			 ->assign($assignMsg, $msg);

		$doc = $view->getHtmlDoc();
		$doc->setTitle($title);
		$yui = 'http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js';
		$doc->addJsBodyFile($yui);
	}
}
