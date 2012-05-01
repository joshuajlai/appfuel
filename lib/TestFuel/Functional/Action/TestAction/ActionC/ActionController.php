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
namespace TestFuel\Fake\Action\TestAction\ActionC;

use Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * This action is designed to be called by ActionA
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
    public function process(MvcContextInterface $context)
	{
		$view  = $context->getView();
		$input = $context->getInput();
	
		$valueC = $input->get('get', 'label-c');
		$valueD = $input->get('get', 'label-d');

		$result = "processed label-a=$valueC label-b=$valueD";
		$view->assign('action-c:', $result);
	}
}
