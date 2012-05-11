<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use LogicException,
	RunTimeException;

/**
 * 
 */
class MvcDispatcher implements MvcDispatcherInterface
{
	/**
	 * Dispatch a request a context using the fluent interface
	 *
	 * @param	AppContextInterface $context
	 * @return	AppContextInterface
	 */
	public function dispatch(MvcContextInterface $context)
	{
		$key    = $context->getRouteKey();
		$detail = $this->getRouteDetail($key);
		if (! $detail instanceof MvcRouteDetailInterface) {
			$err  = "failed to dispatch: route -($key) ";
			$err .= "not found ";
			throw new RunTimeException($err, 404);
		}

		$input  = $context->getInput();
		$method = $input->getMethod();
		$name   = $detail->findActionName($input->getMethod());
		$ns     = $this->getNamespace($key);
		$class  = "$ns\\$name";
		$action = new $class();
        if (! ($action instanceof MvcActionInterface)) {
            $err  = 'mvc action does not implement Appfuel\Kernel\Mvc\Mvc';
            $err .= 'ActionInterface';
            throw new LogicException($err);
        }

		/*
		 * Any acl codes are checked againt the route detail's acl access
		 */
		if (! $detail->isAccessAllowed($context->getAclCodes(), $method)) {
			$err = 'user request is not allowed: insufficient permissions';
			throw new RunTimeException($err);
		}

		$action->process($context);
	}

	/**
	 * @param	MvcRouteDetailInterface $detail
	 * @param	string	$key
	 * @return	MvcActionInterface
	 */
	protected function createAction(MvcRouteDetailInterface $detail, $key)
	{
        $class = $detail->getActionClass();
		if (empty($class)) {

		}
		
		return new $class();
	}

	/**
	 * @param	MvcRouteDetailInterface $detail
	 * @param	string	$method
	 * @param	string	$key
	 * @return	MvcActionInterface
	 */
	protected function createRestAction(MvcRouteDetailInterface $detail, 
										$method, 
										$key)
	{
		$name   = $detail->getRestAction($method);
		if (false === $class) {
			$err  = "http method $method is not supported ";
			$err .= "by route -($key)";
			throw new RunTimeException($err, 404);
		}
		$ns    = $this->getNamespace($key);
		$class = "$ns\\$name";
		return new $class();  
	}

	/**
	 * @param	string	$key
	 * @return	MvcRouteDetailInterface
	 */
	protected function getRouteDetail($key)
	{
		return MvcRouteManager::getRouteDetail($key);
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	protected function getNamespace($key)
	{
		return MvcRouteManager::getNamespace($key);
	}
}
