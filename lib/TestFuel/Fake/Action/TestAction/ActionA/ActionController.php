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
namespace TestFuel\Fake\Action\TestAction\ActionA;

use Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\AppContextInterface;

/**
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
		$view = $context->getView();

		$params = array(
			'get' => array(
				'label-a' => 'value-a',
				'label-b' => 'value-b'
			)
		);
		$uriB = 'action-b/label-a/value-a/label-b/value-b';
		$uriC = 'action-c/label-c/value-c/label-d/value-d';

		$strategy = $context->getStrategy();
		$contextB = $this->callUri($uriB, $strategy);
		$contextC = $this->callUri($uriC, $strategy);

		$view->assign('action-b', $contextB->buildView());
		$view->assign('action-c', $contextC->buildView());
	}

}
