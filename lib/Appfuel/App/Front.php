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
	Appfuel\Framework\Controller\FrontInterface,
	Appfuel\Framework\Controller\ActionInterface,
    Appfuel\Framework\MessageInterface,
    Appfuel\Framework\Doc\DocumentInterface;

/**
 * Handle dispatching the request and outputting the response
 */
class Front implements FrontInterface
{
    /**
     * Dispatch
     * Use the route destination to create the controller and execute the
     * its method. Check the return of method, if its a message with a 
     * distination different from the previous then dispath that one
     *
     * @param   MessageInterface $msg
     * @return  MessageInterface
     */
    public function dispatch(MessageInterface $msg)
    {
		
		/* 
		 * ensure the request is available first because if anything
		 * goes wrong we can check the responseType early allowing us 
		 * the ability to send back an error in the correct format
		 */
        if (! $msg->isRequest()) {
			// handle error for missing request
        }
		$request = $msg->get('request');

		/*
		 * we need the route to determine what action controller to build
		 */
		if (! $msg->isRoute()) {
			// handle error for missing route
		}
		$route = $msg->get('route');

		/*
		 * the route and request are used to determine which type of document
		 * to process and put into the message. We first look into the request
		 * and if the reponse type is available otherwise we fallback on the
		 * rotues values.
		 */
        $reponseType = $route->getResponseType();
        if ($request->isResponseType()) {
            $responseType = $request->getResponseType();
        }

        $namspace = $route->getNamespace();
        $ctrClass = "$namespace\\Controller";
        
		try {
			$controller = new $ctrClass();
		} catch (\Exception $e) {
			// handle controller can not be created
		}

		if (! $controller instanceof ActionInterface) {
			// handle controller does not implement the correct interface
		}
	
		/*
		 * controller is available and using the correct interface 
		 */
        if (! $controller->isSupportedDoc($responseType)) {
			// handle invalid document type
        }
		$msg->add('responseType', $responseType);

		try {
			$controller->initialize($msg);
		} catch (Exception $e) {
			// handler intialization errors
		}

        try {
            $msg = $controller->execute($msg);
        } catch (Exception $e) {
			// handle controller exceptions
		}

		try {
			$controller->cleanUp($msg);
		} catch (Exception $e) {

		}

		if (! $msg->isDoc()) {
			// process logical errors in the messag
		}

	
        return $msg;
    }
}
