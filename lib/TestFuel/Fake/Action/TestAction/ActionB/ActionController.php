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
namespace TestFuel\Fake\Action\TestAction\ActionB;

use Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\AppContextInterface;

/**
 * This action is designed to be called by ActionA
 * The goal of action A is to call action B and action C and turn their views
 * views into string and assign them into action A with label action-b and 
 * action c
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
     * @param   AppContextInterface           $context
     * @param   ConsoleViewTemplateInterface  $view
     * @return  mixed   null | AppContextInterface 
     */
    public function process(AppContextInterface $context)
	{
		$view  = $context->getView();
		$input = $context->getInput();
	
		$valueA = $input->get('get', 'label-a');
		$valueB = $input->get('get', 'label-b');

		$result = "processed label-a=$valueA label-b=$valueB";
		$view->assign('action-b:', $result);
	}

}
