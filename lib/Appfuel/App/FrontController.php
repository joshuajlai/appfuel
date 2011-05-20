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
			$controller = new ErrorController();
		}
		$this->setErrorController($controller);
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
    public function dispatch(DictionaryInterface $msg)
    {
		
		/* 
		 * ensure the request is available first because if anything
		 * goes wrong we can check the responseType early allowing us 
		 * the ability to send back an error in the correct format
		 */
		$request = $msg->get('request');
        if (! $request instanceof RequestInterface) {
			$msg->setError('Can not dispatch with out a request object');
			return $this->dispatchError($msg);
        }

		/*
		 * we need the route to determine what action controller to build
		 */
		$route = $data->get('route');
		if (! $route instanceof RouteInterface) {
			$msg->setError('Can not dispatch with out a route object');
			return $this->dispatchError($msg);
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

		$actionBuilder = $this->getActionBuilder();
		$controller    = $actionBuilder->build($route, $responseType);
		if (! $controller) {
			$msg->setError($builder->getError());
			return $this->dispatchError($msg);
		}

		$msg = $this->intialize($controller, $msg);
		if ($msg->isError()) {
			return $this->dispatchError($msg);
		}

		$msg = $this->execute($controller, $msg);
		if ($msg->isError()) {
			return $this->dispatchError($msg);
		}

        return $msg;
    }

	public function dispatchError(Dictionary $msg)
	{
		$controller = $this->getErrorController();
		$msg = $this->initialize($controller, $msg);
		return $this->execute($controller, $msg);
	}

	/**
	 * Initialize any action controller and handle its exceptions
	 *
	 * @param	ControllerInterface
	 * @return	DictionaryInterface
	 */
	public function initialize(ControllerInterface $controller, 
							   DictionaryInterface $msg)
	{
		try {
			$msg = $controller->initialize($msg);
		} catch (Exception $e) {
			// handler intialization errors
		}
	
		return $msg;
	}
	
	/**
	 * Execute any action controller and handle its execptions
	 *
	 * @param	ControllerInterface		$controller
	 * @return	DictionaryInterface		
	 */
	public function execute(ControllerInterface $controller,
							DictionaryInterface $msg)
	{
        try {
            $msg = $controller->execute($data);
        } catch (Exception $e) {
			// handle controller exceptions
		}
	}
}
