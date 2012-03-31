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
		$detail  = $this->getRouteDetail($context->getRouteKey());
		if (! $detail instanceof MvcRouteDetailInterface) {
			$err  = "failed to dispatch: route -({$context->getRouteKey()}) ";
			$err .= "not found ";
			throw new RunTimeException($err, 404);
		}

        $class  = $detail->getActionClass();
		$action = new $class();
        if (! ($action instanceof MvcActionInterface)) {
            $err  = 'mvc action does not implement Appfuel\Kernel\Mvc\Mvc';
            $err .= 'ActionInterface';
            throw new LogicException($err);
        }

		/*
		 * Any acl codes are checked againt the route detail's acl access
		 */
		if (! $detail->isAccessAllowed($context->getAclCodes())) {
			$err = 'user request is not allowed: insufficient permissions';
			throw new RunTimeException($err);
		}

		$action->process($context);
	}

	/**
	 * @param	string	$key
	 * @return	MvcRouteDetailInterface
	 */
	public function getRouteDetail($key)
	{
		return MvcRouteManager::getRouteDetail($key);
	}
}
