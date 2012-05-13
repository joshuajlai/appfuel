<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\App;

use Appfuel\View\ViewInterface,
	Appfuel\Kernel\TaskHandlerInterface,
	Appfuel\Kernel\Mvc\RequestUriInterface,
	Appfuel\Kernel\Mvc\AppInputInterface,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 * 
 */
interface AppHandlerInterface
{
	/**
	 * @return	AppFactoryInterface
	 */
	public function getAppFactory();

	/**
	 * @param	AppFactoryInterface $factory
	 * @return	AppRunner
	 */
	public function setAppFactory(AppFactoryInterface $factory);

	/**
	 * @return	RequestUriInterface
	 */
	public function createUriFromServerSuperGlobal();

	/**
	 * @param	string
	 * @return	RequestUriInterface
	 */
	public function createUri($str);

	/**
	 * @return	AppInputInterface
	 */
	public function createRestInputFromBrowser($uri = null);

	public function createRestInputFromConsole($uri = null);

	/**
	 * @param	array	$tasks
	 * @return	AppRunner
	 */
	public function findRoute($key, $format = null);

	/**
	 * @param	string $key
	 * @param	AppInputInterface   $input
	 * @return	MvcContextInterface
	 */
	public function createContext($key, AppInputInterface $input);

	/**
	 * @param	MvcRouteDetailInterface	$route
	 * @param	MvcContextInterface		$context
	 * @return	AppRunner
	 */
	public function initializeApp(MvcRouteDetailInterface $route, 
								  MvcContextInterface $context);

	/**
	 * @param	MvcRouteDetailInterface	$route
	 * @param	MvcContextInterface		$context
	 * @param	string					$format
	 * @return	AppRunner
	 */
	public function setupView(MvcRouteDetailInterface $route, 
							  MvcContextInterface $context, 
							  $format = null);

	public function composeView(MvcRouteDetailInterface $route,
								MvcContextInterface $context);

	/**
	 * @param	MvcRouteDetailInterface $route
	 * @param	MvcContextInterface $context
	 * @param	bool	$isHttp
	 * @return	null
	 */
	public function outputHttpContext(MvcRouteDetailInterface $route, 
									  MvcContextInterface $context,
									  $version = '1.1');
	/**
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function outputConsoleContext(MvcRouteDetailInterface $route,
										 MvcContextInterface $context);

	/**
	 * @param	MvcContextInterface		$context
	 * @return	AppRunner
	 */
	public function runAction(MvcContextInterface $context);

	/**
	 * @param	array	$tasks
	 * @return	AppRunner
	 */
	public function initializeFramework();
	public function runTasks(array $tasks);

	/**
	 * @return	TaskHandlerInterface
	 */
	public function getTaskHandler();

	/**
	 * @param	TaskHandlerInterface $handler
	 * @return	AppRunner
	 */
	public function setTaskHandler(TaskHandlerInterface $handler);

	/**
	 * @return	TaskHandlerInterface
	 */
	public function loadTaskHandler();

	/**
	 * @return	TaskHandlerInterface
	 */
	public function createTaskHandler();
}
