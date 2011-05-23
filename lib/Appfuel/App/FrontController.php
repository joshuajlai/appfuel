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
	Appfuel\Framework\App\Action\ActionBuilderInterface,
	Appfuel\Framework\App\Route\RouteInterface,
    Appfuel\Framework\App\MessageInterface,
    Appfuel\Framework\View\DocumentInterface,
	Appfuel\App\Route\ErrorRoute;

/**
 * Handle dispatching the request and outputting the response
 */
class FrontController implements FrontControllerInterface
{
	/**
	 * Used to build the error controller needed to process errors
	 * @var	ErrorController
	 */
	protected $errorRoute = null;

	/**
	 * When so controller is passed in create the default error controller
	 *
	 * @param	ActionInterface		$controller
	 * @return	FrontController
	 */
	public function __construct(RouteInterface $route = null)
	{
		if (null === $route) {
			$route = new ErrorRoute();
		}
		$this->setErrorRoute($route);
	}

	/**
	 * @return	ActionInterface
	 */
	public function getErrorRoute()
	{
		return $this->errorRoute;
	}

	/**
	 * @param	ActionInterface	 $controller
	 */
	public function setErrorRoute(RouteInterface $route)
	{
		$this->errorRoute = $route;
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
			$msg->setError('request is missing from the message');
			return false;
        }

		if (! $msg->isRoute()) {
			$msg->setError('route is missing from the message');
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
		try {
			$controller = $this->buildController($msg);
		} catch (Exception $e) {
			$msg->setError($e->getMessage());
			return $this->dispatchError($msg);
		}

		$msg = $this->initialize($controller, $msg);
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
	 * @param	RouteInterface $route
	 * @param	string		   $responseType
	 * @return	ControllerInterface
	 */
	public function buildController(MessageInterface $msg)
	{
		$errText = 'buildController failed:';
		if (! $this->isSatisfiedBy($msg)) {
			throw new Exception("$errText {$msg->getError()}");
		}

		$msg     = $this->assignResponseType($msg);
		$builder = $this->createActionBuilder($route);
		if (! $builder instanceof ActionBuilderInterface) {
			throw new Exception("$errText ActionBuilder has invalid interface");
		}

		$controller = $builder->buildController($msg);
		if ($builder->isError()) {
			$errText .= ' ' . $builder->getError();
			throw new Exception($errText);
		}

		if (! $controller instanceof ControllerInterface) {
			$errText .= " Action Controller using an invalid interface";
			throw new Exception($errText);
		}

		return $controller;
	}

	/**
	 * Response type is assigned to the message by looking at both the
	 * route and the user input
	 *
	 * @param	MessageInterface	$msg
	 * @return	MessageInterface
	 */
	public function assignResponseType(MessageInterface $msg)
	{
		$route   = $msg->getRoute();
		$request = $msg->getRequest();
		$responseType = $msg->calculateResponseType($route, $request);
		$msg->setResponseType($responseType);

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
			$builder = new $class($route);
			return $builder;
		} catch (Exception $e) {}

		$namespace = $route->getSubModuleNamespace();
		$class     = "$namespace\\ActionBuilder";
		try {
			$builder = new $class($route);
			return $builder;
		} catch (Exception $e) {}

		$namespace = $route->getModuleNamespace();
		$class     = "$namespace\\ActionBuilder";
		try {
			$builder = new $class($route);
			return $builder;
		} catch (Exception $e) {}

		$namespace = $route->getRootActionNamespace();
		$class     = "$namespace\\ActionBuilder";
		try {
			$builder = new $class($route);
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
		$route = $this->getErrorRoute();

		$msg->setRoute($route);
		$responseType = $msg->loadResponseType();

		$controller = $this->buildController($route, $responseType);

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
			$tmp = $ctr->initialize($msg);
			if ($tmp instanceof MessageInterface) {
				$msg = $tmp;
			}
		} catch (Exception $e) {
			$msg->setError($e->getMessage());
		}

		return $msg;
	}
	
	/**
	 * See interface for details
	 *
	 * @param	ControllerInterface	$controller
	 * @param	MessageInterface	$msg	
	 * @return  MessageInterface		
	 */
	public function execute(ControllerInterface $ctr, MessageInterface $msg)
	{
        try {
            $tmp = $ctr->execute($msg);
			if ($tmp instanceof MessageInterface) {
				$msg = $tmp;
			}
        } catch (Exception $e) {
			$msg->setError($e->getMessage());
		}

		return $msg;
	}
}
