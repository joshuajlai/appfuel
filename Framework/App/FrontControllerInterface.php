<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Framework\App;

use Appfuel\Framework\App\Action\ControllerInterface,
    Appfuel\Framework\App\Route\RouteInterface,
    Appfuel\Framework\App\ContextInterface,
    Appfuel\App\Action\Error\Handler\Invalid\Controller as ErrorController;


/**
 * Handle dispatching the request and outputting the response
 */
interface FrontControllerInterface
{

	/**
     * Ensures the message contains a route and request which are required
     * to dispatch to an action controller
     *
     * @param   MessageInterface    $msg
     * @return  bool
     */
	public function isSatisfiedBy(ContextInterface $msg);

    /**
     * Dispatch
     * Use the route destination to create the controller and execute the
     * its method. Check the return of method, if its a message with a 
     * distination different from the previous then dispath that one
     *
     * @param   Dictionary $data
     * @return  Dictionary
     */
    public function dispatch(ContextInterface $msg);

    /**
	 * The dispatch does not have a controller so this method is used to 
	 * create the action build. It needs to search for the builder in the 
	 * following order: 
	 *	1)  action namespace (the controller namespace)
	 *  2)	sub module namesapce (parent of the controller)
	 *  3)  module namespace (parent of all sub modules)
	 *  4)  root namespace  (the namespace that holds all the actions)
	 * 
	 * This allows you to extend the builder from most general to view specific
	 *
     * @param   RouteInterface  $route 
     * @return  ActionBuilder   
     */
    public function createActionBuilder(RouteInterface $route);

	/**
	 * Initialize is called on the action controller and errors should be
	 * handled and put back into the message so dispatch can deal with them.
	 *
	 * @param	ControllerInterface $ctr
	 * @param	ContextInterface $con
	 * @return	ContextInterface
	 */
	public function initialize(ControllerInterface $ctr, ContextInterface $con);
	
	/**
	 * execute is called on the action controller and errors should be
	 * handled and put back into the message so dispatch can deal with them.
	 *
	 * @param	ControllerInterface $ctr
	 * @param	ContextInterface $con
	 * @return	ContextInterface
	 */
	public function execute(ControllerInterface $ctr, ContextInterface $con);
}
