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
namespace TestFuel\Fake\Action\TestDispatch\ActionA;

use Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\AppContextInterface,
	Appfuel\View\ViewTemplateInterface,
	Appfuel\View\AjaxTemplateInterface;

/**
 * This fake action controller is used test the MvcAction and ActionFactory
 */
class ActionController extends MvcAction
{
	/**
	 * @param	array $codes
	 * @return	bool
	 */
	public function isContextAllowed(array $codes)
	{
		return true;
	}

    /**
     * @param   AppContextInterface           $context
     * @param   ConsoleViewTemplateInterface  $view
     * @return  mixed   null | AppContextInterface 
     */
    public function process(AppContextInterface $context)
	{
		$strategy = $context->get('app-strategy', 'html');
		$view = $context->getView();
		$view->assign('common-a', 'value-a')
			 ->assign('common-b', 'value-b');
			 

		switch($strategy) {
			case 'console':  $label = 'console-foo';break;
			case 'ajax':     $label = 'ajax-foo';	break;
			case 'html':	 $label = 'html-foo';	break;
		}

		$view->assign($label, 'bar');
	}
}
