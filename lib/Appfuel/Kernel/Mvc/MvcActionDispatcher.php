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

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\KernelRegistry,
	Appfuel\View\ViewTemplateInterface;

/**
 */
class MvcActionDispatcher implements MvcActionDispatcherInterface
{	
	/**
	 * Used to create mvc actions and views
	 * @var ActionFactoryInterface
	 */
	protected $factory = null;

	/**
	 * @param	AppInputInterface		$input
	 * @param	ErrorStackInterface		$error
	 * @return	AppContext
	 */
	public function __construct(MvcActionFactoryInterface $factory = null)
	{
		if (null === $factory) {
			$factory = new MvcActionFactory();
		}
		$this->factory = $factory;
	}

	/**
	 * @return	array
	 */
	public function getActionFactory()
	{
		return $this->factory;
	}

	/**
	 * @param	string	$route
	 * @param	string	$strategy	
	 * @param	AppContextInterface $context
	 * @return	AppContextInterface
	 */
	public function dispatch($route, $strategy, AppContextInterface $context)
	{
		$err = 'Failed to dispatch: ';
		if (! is_string($route)) {
			$err .= 'route key must be a string';
			throw new InvalidArgumentException($err);
		}

		$namespace = KernelRegistry::getActionNamespace($route);
		if (false === $namespace) {
			$uri = $context->get('original-uri', '');
			throw new RouteNotFoundException($route, $uri);
		}

		/* 
		 * create the mvc action and assign it a dispatcher so it can call
		 * other mvc actions
		 */
		$factory = $this->getActionFactory();
		$action  = $factory->createMvcAction($namespace);
		$action->setDispatcher(new self($factory));
		if (false === $action->isContextAllowed($context->getAclRoleCodes())) {
			$uri = $context->get('original-uri', '');
			throw new RouteDeniedException($route, $uri);		
		}

		switch ($strategy) {
			case 'app-htmlpage':
				$view   = $factory->createHtmlView($namespace);
				$result = $action->processHtml($context, $view);	
				break;
			case 'app-ajax':
				$view   = $factory->createAjaxView($namespace);
				$result = $action->processAjax($context, $view);	
				break;
			case 'app-console':
				$view   = $factory->createConsoleView($namespace);
				$result = $action->processConsole($context, $view);
				break;
			default:
				$err  = "failed to dispatch: application type is not ";
				$err .= "recognized no interface exists for -($strategy)";
				throw new RunTimeException($err);
		}

		if (! ($result instanceof ViewTemplateInterface)) {
			$result = $view;
		}
		
		return $result;
	}
}
