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
    Appfuel\Framework\App\ContextInterface,
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
	public function isSatisfiedBy(ContextInterface $context)
	{        
		if (! $context->isRequest()) {
			$context->setError('request is missing from the message');
			return false;
        }

		if (! $context->isRoute()) {
			$context->setError('route is missing from the message');
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
    public function dispatch(ContextInterface $context)
    {
		try {
			$controller = $this->buildController($context);
		} catch (Exception $e) {
			$msg->setError($e->getMessage());
			return $this->dispatchError($context);
		}

		$context = $this->initialize($controller, $context);
		if ($context->isError()) {
			return $this->dispatchError($context);
		}

		$context = $this->execute($controller, $context);
		if ($context->isError()) {
			return $this->dispatchError($context);
		}

        return $context;
    }
	
	/**
	 * @param	RouteInterface $route
	 * @param	string		   $responseType
	 * @return	ControllerInterface
	 */
	public function buildController(ContextInterface $context)
	{
		$errText = 'buildController failed:';
		if (! $this->isSatisfiedBy($context)) {
			throw new Exception("$errText {$context->getError()}");
		}

		$context     = $this->assignResponseType($context);
		$builder = $this->createActionBuilder($route);
		if (! $builder instanceof ActionBuilderInterface) {
			throw new Exception(
				"$errText ActionBuilder has invalid interface"
			);
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
	public function assignResponseType(ContextInterface $context)
	{
		$route   = $context->getRoute();
		$request = $context->getRequest();
		$responseType = $context->calculateResponseType($route, $request);
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
	public function dispatchError(ContextInterface $context)
	{
		$route = $this->getErrorRoute();

		$context->setRoute($route);
		$responseType = $msg->loadResponseType();

		$controller = $this->buildController($route, $responseType);

		$context = $this->initialize($controller, $context);
		return $this->execute($controller, $context);
	}

	/**
	 * See interface for details
	 *
	 * @param	ControllerInterface	$controller
	 * @param	MessageInterface	$msg	
	 * @return	MessageInterface		
	 */
	public function initialize(ControllerInterface $ctr, ContextInterface $con)
	{
		try {
			$tmp = $ctr->initialize($con);
			if ($tmp instanceof MessageInterface) {
				$con = $tmp;
			}
		} catch (Exception $e) {
			$con->setError($e->getMessage());
		}

		return $con;
	}
	
	/**
	 * See interface for details
	 *
	 * @param	ControllerInterface	$controller
	 * @param	MessageInterface	$msg	
	 * @return  MessageInterface		
	 */
	public function execute(ControllerInterface $ctr, ContextInterface $con)
	{
        try {
            $tmp = $ctr->execute($con);
			if ($tmp instanceof MessageInterface) {
				$con = $tmp;
			}
        } catch (Exception $e) {
			$con->setError($e->getMessage());
		}

		return $con;
	}
}
