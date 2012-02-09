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
namespace Appfuel\Kernel\Mvc;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\KernelRegistry,
	Appfuel\Error\ErrorStackInterface,
	Appfuel\View\ViewTemplateInterface;

/**
 * Provide a fluent interface used to build the context required for 
 * for dispatching. Also resolves the route key to an action namespace 
 * with the KernelRegistry. This is used by the front controller to dispatch
 * the intial request and also used by mvc actions to call other actions.
 */
class MvcActionDispatcher implements MvcActionDispatcherInterface
{
}
