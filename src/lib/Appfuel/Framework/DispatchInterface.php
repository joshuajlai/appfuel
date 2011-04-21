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
namespace Appfuel\Framework;

/**
 * Handle dispatching the request and outputting the response
 */
interface DispatchInterface
{
    /**
     * Create a page controller and its corresponding layout manager 
     * according to the message passed in. When a layout manager is
     * given then use that and do not create the layout of view
     *
     * @param   MessageInterface    $msg
     * @reutrn  mixed   NULL|Command
     */  
    public function load(MessageInterface $msg);


    /**
     * Check to make sure the give page controller has the action available 
     * then execute that action with the given message and check to make
     * sure the reponse is of the correct type
     *
     * @param   Page\Command    $cmd        the page controller to be executed
     * @param   string          $action     the method to be executed
     * @param   MessageInterface    $msg    the parameter for the method      
     * @return  Page\Response
     */
    public function execute(ControllerInterface $ctr, MessageInterface $msg);
}
