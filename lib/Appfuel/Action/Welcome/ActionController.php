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
	Appfuel\View\Html\HtmlDocTemplate,
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
		$title = 'Welcome Page';
		$htmlDoc = new HtmlDocTemplate();
		$htmlDoc->setTitle($title);

		$compositor = new FileCompositor();
		$compositor->setFile(
			'appfuel/html/tpl/view/welcome/welcome-view.phtml'
		);

		$view = new ViewTemplate(null, $compositor);
		
		$view->assign('title', 'Welcome To Appfuel Framework');
		$view->assign('msg', 'some text will need to go here');
		
		$htmlDoc->addBodyContent($view);
		$context->setView($htmlDoc);
	}
}
