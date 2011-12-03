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

use Appfuel\View\AjaxTemplateInterface,
	Appfuel\View\ViewTemplateInterface;

/**
 */
class MvcAction implements MvcActionInterface
{
	/**
	 * Used to make a call to other mvc actions
	 * @var MvcActionDispatcherInterface
	 */
	protected $dispatcher = null;

	/**
	 * @param	MvcActionDispatcherInterface $dispatcher
	 * @return	null
	 */
	public function setDispatcher(MvcActionDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * @param	MvcActionDispatcher
	 * @return	null
	 */
	public function getDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * @param	array	$codes
	 * @return	bool
	 */
	public function isContextAllowed(array $codes)
	{
		return false;
	}

	/**
	 * @param	AppContextInterface $context
	 * @return	null
	 */
	public function process(AppContextInterface $context)
	{}

	/**
	 * 
	 * @param	string	$route
	 * @param	AppContextInterface $context
	 * @return	AppContextInterface
	 */
	public function callContext($route, AppContextInterface $context)
	{
		$err = 'Failed to call action ';
		if (! is_string($route)) {
			$err .= 'route must be a string';
			throw new InvalidArgumentException($err);
		}

		$dispatcher = $this->getDispatcher();
		if (null === $dispatcher) {
			$err .= "-($route): dispatcher must be set";
			throw new RunTimeException($err);
		}

		$strategy = $context->get('app-strategy', null);
		if (empty($strategy) || ! is_string($strategy)) {
			$err .= "strategy is not a string or not set into context. ";
			$err .= "searched context for strategy with 'app-strategy'";
			throw new RunTimeException($err);
		}

		$result = $dispatch->clear()
						   ->setStrategy($strategy)
						   ->setRoute($route)
						   ->runDispatch($context);
		
		if ($result instanceof AppContextInterface) {
			$context = $result;
		}

		return $context;
	}

	/**
	 * Manually configure the dispatcher to call another mvc action. Note
	 * the route is part of the uri, which can be a RequestUri or a string.
	 * if the mvc action returns a context it will override the context passed
	 * in.
	 *
	 * @param	mixed	string|RequestUri $uri
	 * @param	string	$strategy
	 * @param	string	$method
	 * @param	array	input
	 * @return	AppContextInterface
	 */
	public function callManual($uri, $strategy, $method, array $input)
	{
		$dispatcher = $this->getDispatcher();
		if (null === $dispatcher) {
			$err = "Failed to call action -($route): dispatcher must be set";
			throw new RunTimeException($err);
		}

		$useUri  = true;
		$context = $dispatcher->clear()
							  ->setStrategy($strategy)
							  ->setUri($uri)
							  ->defineInput($method, $input, $useUri)
							  ->buildContext();

		$result = $dispatcher->runDispatch($context);
		if ($result instanceof AppContextInterface) {
			$context = $result;
		}

		return $context;
	}
}
