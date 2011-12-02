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
namespace TestFuel\Fake\Action\TestFront\ActionA;

use Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\AppContextInterface,
	Appfuel\View\ViewTemplateInterface;

/**
 * Mvc Action used to test the front controller
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
		$view = $context->getView();

		$label   = 'my-assignment';
		$default = 'this action has been executed';
		$value   = $view->getAssigned($label, null);
		if (! empty($value) && is_string($value)) {
			$value .= " $default";
		}
		else {
			$value = $default;
		}

		$view->assign($label, $value);
	}

}
