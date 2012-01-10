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
namespace TestFuel\Fake\Action\Welcome\Foo;

use Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * The only goal of foo is to assign 'foo' => bar into the view
 */
class ActionController extends MvcAction
{
    /**
     * @param   MvcContextInterface $context
     * @return  null
     */
    public function process(MvcContextInterface $context)
	{
		$view = $context->getView();
		$view->assign('foo', 'bar');
	}
}
