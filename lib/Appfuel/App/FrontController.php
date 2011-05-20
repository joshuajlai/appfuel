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
	Appfuel\Framework\App\FrontControllerInterface,
	Appfuel\Framework\App\Action\ControllerInterface,
    Appfuel\Framework\Data\DictionaryInterface,
    Appfuel\Framework\View\DocumentInterface,
	Appfuel\App\Action\Error\Handler\Invalid\Controller as ErrorController;

/**
 * Handle dispatching the request and outputting the response
 */
class FrontController implements FrontControllerInterface
{
	/**
	 * Used to process errors that occur during dispatching
	 * @var	ErrorController
	 */
	protected $errorController = null;

	/**
	 * When so controller is passed in create the default error controller
	 *
	 * @param	ActionInterface		$controller
	 * @return	FrontController
	 */
	public function __construct(ControllerInterface $controller = null)
	{
		if (null === $controller) {
			$this->setErrorController(new ErrorController());
		}
	}

	/**
	 * @return	ActionInterface
	 */
	public function getErrorController()
	{
		return $this->errorController;
	}

	/**
	 * @param	ActionInterface	 $controller
	 */
	public function setErrorController(ControllerInterface $controller)
	{
		$this->errorController = $controller;
		return $this;
	}

    /**
     * Dispatch a message by using the route to create a controller 
	 * command object, use the request or rotue to determine what document
	 * the controller will pocess. Execute the message and check for the 
	 * exists of the document which is needed by other systems
	 * 
     * @param   MessageInterface $msg
     * @return  MessageInterface
     */
    public function dispatch(DictionaryInterface $data)
    {
		
		/* 
		 * ensure the request is available first because if anything
		 * goes wrong we can check the responseType early allowing us 
		 * the ability to send back an error in the correct format
		 */
		$request = $data->get('request');
        if (! $request instanceof RequestInterface) {
			// handle error for missing request
        }

		/*
		 * we need the route to determine what action controller to build
		 */
		$route = $data->get('route');
		if (! $route instanceof RouteInterface) {
			// handle error for missing route
		}

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

		$viewManager = $controller->createViewManager();
		if (! $viewManager instanceof ViewManagerInterface) {
			// handle incorrect view manager
		}

		$this->controller->setViewManager($viewManager);
		$viewManager->setDoc($doc);
		$controller->setViewManager($viewManager);
		
		$msg->add('doc', $doc);


		try {
			$controller->initialize($data);
		} catch (Exception $e) {
			// handler intialization errors
		}

        try {
            $msg = $controller->execute($data);
        } catch (Exception $e) {
			// handle controller exceptions
		}

        return $data;
    }
}
