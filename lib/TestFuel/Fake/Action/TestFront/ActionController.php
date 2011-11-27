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
namespace TestFuel\Fake\Action\User\Create;

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
    public function processConsole(AppContextInterface $context,
                                    ViewTemplateInterface $view)
	{
		$this->commonAssignments($view);
		$view->assign('console-foo', 'bar');
		return $view;
	}

    /**
     * @param   AppContextInterface           $context
     * @param   ConsoleViewTemplateInterface  $view
     * @return  mixed   null | AppContextInterface 
     */
    public function processAjax(AppContextInterface $context,
                                AjaxTemplateInterface $view)
	{
		$this->commonAssignments($view);
		$view->assign('ajax-foo', 'bar');
		return $view;
	}

    /**
     * @param   AppContextInterface           $context
     * @param   ConsoleViewTemplateInterface  $view
     * @return  mixed   null | AppContextInterface 
     */
    public function processHtml(AppContextInterface $context,
                                ViewTemplateInterface $view)
	{
		$this->commonAssignments($view);
		$view->assign('html-foo', 'bar');
		return $view;
	}

	/**
	 * Shows how you might resuse content assignments across all process types
	 *
	 * @param	ViewTemplateInterface $view
	 * @return	null
	 */
	protected function commonAssignments(ViewTemplateInterface $view)
	{
		$view->assign('common-a', 'value-a')
			 ->assign('common-b', 'value-b');
	}
}
