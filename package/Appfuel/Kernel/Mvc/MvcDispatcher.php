<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

use LogicException,
	DomainException,
	RunTimeException;

/**
 * Uses the route detail to find and create the mvc action controller based
 * based on the input method (get, post, put, delete or cli). Validate acl 
 * access with acl codes found in the context and the input method. Optionally,
 * validate input parameters using a parameter specification found in the 
 * route detail. Finally process the mvc action passing in the context.
 */
class MvcDispatcher implements MvcDispatcherInterface
{
	/**
	 * @param	MvcContextInterface $context
	 * @return	null	
	 */
	public function dispatch(MvcContextInterface $context)
	{
		$key    = $context->getRouteKey();
		$detail = $this->getRouteDetail($key);
		$input  = $context->getInput();
		$method = $input->getMethod();

		if (! $detail instanceof MvcRouteDetailInterface) {
			$err  = "failed to dispatch: route -($key) ";
			$err .= "not found ";
			throw new RunTimeException($err, 404);
		}

		$action = $this->createMvcAction($key, $method, $detail);
        if (! ($action instanceof MvcActionInterface)) {
            $err  = 'mvc action does not implement Appfuel\Kernel\Mvc\Mvc';
            $err .= 'ActionInterface';
            throw new LogicException($err);
        }

		if (! $detail->isAccessAllowed($context->getAclCodes(), $method)) {
			$err = 'user request is not allowed: insufficient permissions';
			throw new RunTimeException($err);
		}

		if ($detail->isInputValidation() && $detail->isValidationSpecList()) {
			if (! $input->isSatisfiedBy($detail->getValidationSpecList())) {
				if ($detail->isThrowOnValidationError()) {
					$errors = $input->getErrorString();
					$code   = $detail->getValidationErrorCode();
					throw new DomainException($errors, $code);
				}
			}
		}

		$action->process($context);
	}

	/**
	 * @param	string	$key
	 * @param	MvcRouteDetailInterface $detail
	 * @return	MvcActionInterface
	 */
	protected function createMvcAction($key,
									   $method, 
									   MvcRouteDetailInterface $detail)
	{
		$name   = $detail->findActionName($method);
		$ns     = $this->getNamespace($key);
		$class  = "$ns\\$name";
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
