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
	Appfuel\Framework\App\Route\RouteInterface,
    Appfuel\Framework\App\MessageInterface,
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
	 * see interface for details
	 *
	 * @param	MessageInterface	$msg
	 * @return	bool
	 */
	public function isSatisfiedBy(MessageInterface $msg)
	{        
		if (! $msg->isRequest()) {
			$msg->setError('Can not dispatch with out a request object');
			return false;
        }

		if (! $msg->isRoute()) {
			$msg->setError('Can not dispatch with out a route object');
			return false;
		}

		return true;
	}

    /**
	 * Ensure the message is correct, create the action builder then intialize,
	 * execute and return the message. Check the message for errors and dispatch
	 * to the error controller when they occur
	 * 
     * @param   MessageInterface $msg
     * @return  MessageInterface
     */
    public function dispatch(MessageInterface $msg)
    {
		if (! $this->isSatisfiedBy($msg)) {
			return $this->dispatchError($msg);
		}

		$request      = $msg->get('request');
		$route        = $data->get('route');
        $responseType = $msg->loadResponseType();
		
		$actionBuilder = $this->createActionBuilder($route);
		$controller    = $actionBuilder->buildController($responseType);
		
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

	/**
	 * See interface for details
	 *
	 * @param	RouteInterface	$route 
	 * @return	ActionBuilder	
	 */
	public function createActionBuilder(RouteInterface $route)
	{
		$namespace = $route->getActionNamespace();
		$class     = "$namespace\\ActionBuilder";
		$builder   = null;
		try {
			$builder = new $class();
			return $builder;
		} catch (Exception $e) {}

		$namespace = $route->getSubModuleNamespace();
		$class     = "$namespace\\ActionBuilder";
		try {
			$builder = new $class();
			return $builder;
		} catch (Exception $e) {}

		$namespace = $route->getModuleNamespace();
		$class     = "$namespace\\ActionBuilder";
		try {
			$builder = new $class();
			return $builder;
		} catch (Exception $e) {}

		$namespace = $route->getRootActionNamespace();
		$class     = "$namespace\\ActionBuilder";
		try {
			$builder = new $class();
			return $builder;
		} catch (Exception $e) {}

		return false;
	}

	/**
	 * See interface for details
	 *
	 * @param	MessageInterface	$msg	
	 * @return	MessageInterface		
	 */
	public function dispatchError(MessageInterface $msg)
	{
		$controller = $this->getErrorController();
		$msg = $this->initialize($controller, $msg);
		return $this->execute($controller, $msg);
	}

	/**
	 * See interface for details
	 *
	 * @param	ControllerInterface	$controller
	 * @param	MessageInterface	$msg	
	 * @return	MessageInterface		
	 */
	public function initialize(ControllerInterface $ctr, MessageInterface $msg)
	{
		try {
			$msg = $ctr->initialize($msg);
		} catch (Exception $e) {
			// handler intialization errors
		}
	
		return $msg;
	}
	
	/**
	 * See interface for details
	 *
	 * @param	ControllerInterface	$controller
	 * @param	MessageInterface	$msg	
	 * @return	MessageInterface		
	 */
	public function execute(ControllerInterface $ctr, MessageInterface $msg)
	{
        try {
            $msg = $controller->execute($data);
        } catch (Exception $e) {
			// handle controller exceptions
		}
	}
}
