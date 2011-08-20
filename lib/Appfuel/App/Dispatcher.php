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


use Appfuel\Framework\Exception,
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
    public function load($responseType, $namespace)
    {   
        if (! $msg->isRoute()) {
            throw new Exception("Dispatch Error: route not set");
        }   
    
		if (! $msg->isRequest()) {
			throw new Exception("Dispatch Error: request not set");
		}
		$request = $msg->get('request');
		$route   = $msg->get('route');
 
        $namspace   = $route->getNamespace();
		$ctrClass   = "$namespace\\Controller";
        $controller = new $ctrClass();
   
		$reponseType = $route->getResponseType();
		if ($request->isResponseType()) {
			$responseType = $request->getResponseType();
		}

		if (! $controller->isSupportedDoc($responseType)) {
			throw new Exception("Dispatch Error: can not build $responseType");
		}

		$doc = $controller->initialize($responseType);
		$msg->add('doc', $doc);

        return $controller;
    }
}
