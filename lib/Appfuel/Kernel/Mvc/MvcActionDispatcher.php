<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\KernelRegistry,
	Appfuel\Error\ErrorStackInterface,
	Appfuel\View\ViewTemplateInterface;

/**
 * Provide a fluent interface used to build the context required for 
 * for dispatching. Also resolves the route key to an action namespace 
 * with the KernelRegistry. This is used by the front controller to dispatch
 * the intial request and also used by mvc actions to call other actions.
 */
class MvcActionDispatcher implements MvcActionDispatcherInterface
{
    /**
     * The class name used to create the action controller class found in
     * the namespace the route maps too
     * @var string
     */
    static protected $actionClassName = 'ActionController';

    /**
     * @param   string  $name
     * @return  null
     */
    static public function setActionClassName($name)
    {  
        if (! is_string($name) || ! ($name = trim($name))) {
            $err = 'class name must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        self::$actionClassName = $name;
    }

    /**
     * @return  string
     */
    static public function getActionClassName()
    {  
        return self::$actionClassName;
    }

	/**
	 * Dispatch a request a context using the fluent interface
	 *
	 * @param	AppContextInterface $context
	 * @return	AppContextInterface
	 */
	public function dispatch(MvcContextInterface $context)
	{
		$routeKey    = $context->getRouteKey();
		$routeDetail = $context->getRouteDetail();
		$namespace   = $this->getActionNamespace($routeKey);
		if (false === $namespace) {
			$err = "mapping error: mvc action not found for -($routeKey)";
			throw new LogicException($err);
		}

		$action = $this->createMvcAction($routeKey, $namespace);
		
		/*
		 * Acl codes are simple way of giving the action controllers an easy
		 * way to restrict access. The role codes are completely controlled by
		 * the developer the dispatcher simply asks the question is this 
		 * context allowed to be processed based on these codes
		 */
		if (! $routeDetail->isAllowed($context->getAclCodes())) {
			$err = 'user request is not allowed: insufficient permissions';
			throw new RunTimeException($err);
		}

		return $action->process($context);
	}

    /**
     * @param   string  $namespace
     * @return  HtmlViewInterface
     */
    protected function createMvcAction($routeKey, $namespace)
    {
        $class	   = "$namespace\\$className";
        $action = new $class($routeKey);
        if (! ($action instanceof MvcActionInterface)) {
            $err  = 'mvc action does not implement Appfuel\Kernel\Mvc\Mvc';
            $err .= 'ActionInterface';
            throw new LogicException($err);
        }

        return $action;
    }

	/**
	 * @return	string
	 */
	protected function getActionNamespace($route)
	{
		return KernelRegistry::getActionNamespace($route);
	}
}
