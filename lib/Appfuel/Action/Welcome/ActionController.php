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
	Appfuel\Kernel\Mvc\AppContextInterface;

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
    public function process(AppContextInterface $context)
	{
		$view = $context->getView();

		$title = 'Welcome Page';
		$text  = 'Welcome to appfuel more text to come';
		$view->assign('title', $title)
		$view->assign('message', $text);
	}
}
