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
namespace Appfuel\App;


use Appfuel\Registry,
	Appfuel\Framework\Exception,
	Appfuel\Framework\DispatchInterface,
    Appfuel\Framework\MessageInterface,
    Appfuel\Framework\Doc\DocumentInterface,
    Appfuel\Framework\Render\RenderInterface,
	Appfuel\Framework\ControllerInterface;

/**
 * Handle dispatching the request and outputting the response
 */
class Dispatcher implements DispatchInterface
{
    /**
     * Create a page controller and its corresponding layout manager 
     * according to the message passed in. When a layout manager is
     * given then use that and do not create the layout of view
     *
     * @param   MessageInterface    $msg
     * @reutrn  mixed   NULL|Command
     */  
    public function load(MessageInterface $msg)
    {   
        if (! $msg->isRoute()) {
            throw new Exception("Dispatcher Error: route not set");
        }   
    
		$defaultNs        = __NAMESPACE__ . '\Action';
		$defaultClassName = 'Controller';

		$namespace = Registry::get('controller_namespace', $defaultNs);
		$className = Registry::get('controller_class_name', $defaultClassName);
        
		$route = $msg->get('route'); 
        $class = "{$namespace}\\{$route->getControllerClass()}\\{$className}";
    
        $controller = new $class();
        
        /* build and configure the page document */
        $builder = $controller->createDocBuilder();
		$doc     = $builder->buildDoc($msg->get('responseType', 'html'));
         if (FALSE === $doc) {
            throw new Exception(
                "Document build reports an unsupport document format
                 can not dispatch this message"
            );
        }
        $msg->add('doc', $doc);

        return $controller;
    }


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
    public function execute(ControllerInterface $ctr, MessageInterface $msg)
    {
        if (! $msg->isRoute()) {
            throw new Exception("Dispatcher Error: route not set");
        }

        $route = $msg->get('route');
		
        $msg = $ctr->execute($msg);
        if (! $msg instanceof MessageInterface) {
            throw new Exception(
                "Dispatcher: Action contollers must return a message object"
            );
        }
	
        if (! $msg->isDoc()) {
            throw new Exception("Action contollers must return a document");
        }

        return $msg;
    }
}
