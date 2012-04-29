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
namespace Appfuel\Kernel;

use RunTimeException,
	Appfuel\Http\HttpOutput,
	Appfuel\Http\HttpResponse,
	Appfuel\Console\ConsoleOutput,
	Appfuel\View\ViewBuilder,
	Appfuel\Kernel\TaskHandler,
	Appfuel\Kernel\ConfigRegistry,
	Appfuel\Kernel\Mvc\MvcFront,
	Appfuel\Kernel\Mvc\AppInput,
	Appfuel\Kernel\Mvc\AppInputInterface,
	Appfuel\Kernel\Mvc\RequestUri,
	Appfuel\Kernel\Mvc\RequestUriInterface,
	Appfuel\Kernel\Mvc\MvcContext,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteManager,
	Appfuel\Kernel\Mvc\MvcDispatcher,
	Appfuel\Kernel\Mvc\MvcDispatcherInterface,
	Appfuel\Kernel\Mvc\InterceptChain,
	Appfuel\Kernel\Mvc\InterceptChainInterface;

/**
 * Create all object required to implement appfuels take on the mvc pattern
 */
class AppFactory implements AppFactoryInterface
{	

    /**
     * We look for query string first because the path info in the request uri
	 * gets lost with rewrite rules.
	 * 
     * @return  RequestUri
     */
	public function createUriFromServerSuperGlobal()
	{
		$isQueryString = isset($_SERVER['QUERY_STRING']) &&
						 is_string($_SERVER['QUERY_STRING']) &&
						 ! empty($_SERVER['QUERY_STRING']);

        if ($isQueryString) {
			$str = '?' . $_SERVER['QUERY_STRING'];
        }
        else if (isset($_SERVER['REQUEST_URI'])) {
            $str = $_SERVER['REQUEST_URI'];
        }
        else {
            $err  = 'ConextBuilder failed: php super global ';
            $err .= '$_SERVER[\'REQUEST_URI\']';
            throw new RunTimeException("$err is not set");
        }

		return $this->createUri($str);
	}

	/**
	 * @return	RequestUri
	 */
	public function createUri($str)
	{
		return new RequestUri($str);
	}

	/**
	 * @param	string	$method
	 * @param	array	$params
	 * @return	AppInput
	 */
	public function createInput($method, array $params = array())
	{
		return new AppInput($method, $params);
	}

	public function createRestInputFromConsole(RequestUriInterface $uri = null)
	{
		if (PHP_SAPI !== 'cli') {
			$err = 'php cli must be enabled';
			throw new LogicException($err);
		}

		$getData = array();
		if (null !== $uri) {
			$getData = $uri->getParams();
		}

		$params = array(
			'get' => $getData,
			'cli' => $_SERVER['argv']
		);
		
		return $this->createInput('cli', $params);
	}

	/**
	 * @param	RequestUriInterface $uri 
	 * @return	AppInput
	 */
	public function createRestInputFromBrowser(RequestUriInterface $uri = null)
	{
		$key = 'REQUEST_METHOD';
		if (! isset($_SERVER[$key]) || ! is_string($_SERVER[$key])) {
				$err  = 'request method was not set or is set invalid in ';
				$err .= "\$_SERVER[\'$key\']";
				throw new LogicException($err);
		}

		$method = strtolower($_SERVER[$key]);
		if ('post' === $method) {
			$putKey = 'is-http-put';
			$delKey = 'is-http-delete';
			if (isset($_POST[$putKey]) && 'true' === $_POST[$putKey]) {
				unset($_POST[$putKey]);
				$method = 'put';
			}
			else if (isset($_POST[$delKey]) && 'true' === $_POST[$delKey]) {
				unset($_POST[$delKey]);
				$method = 'delete';
			}
			$data = $_POST;
		}
		else if ('get' === $method) {
			if (null !== $uri) {
				$data = $uri->getParams();
			}
			else {
				$data  = $_GET;
			}
		}
		else {
			$err = 'only -(get, post) are supported for web browsers';
			throw new DomainException($err);
		}

		$params = array(
			$method   => $data,
			'files'   => (isset($_FILES))   ? $_FILES	: array(),
			'cookie'  => (isset($_COOKIE))  ? $_COOKIE	: array(),
			'session' => (isset($_SESSION)) ? $_SESSION : array(),
		);

		return $this->createInput($method, $params);
	}

	/**
	 * @return	AppInput
	 */
	public function createEmptyInput()
	{
		$method = 'get';
		if (PHP_SAPI === 'cli') {
			$method   = 'cli';
		}
		
		return $this->createInput($method, array());
	}

	/**
	 * @param	string	$key
	 * @return	MvcRouteDetailInterface
	 */
	public function createRouteDetail($key)
	{
		return MvcRouteManager::getRouteDetail($key);
	}

	/**
	 * @param	string	$key
	 * @param	AppInputInterface $input
	 * @param	mixed	$view
	 * @return	MvcContext
	 */
	public function createContext($key, AppInputInterface $input, $view = null)
	{
		return new MvcContext($key, $input, $view);
	}

	/**
	 * @param	string	$key
	 * @return	MvcContext
	 */
	public function createEmptyContext($key)
	{
		return $this->createContext($key, $this->createEmptyInput());
	}

	/**
	 * @return	MvcViewBuilderInterface
	 */
	public function createViewBuilder()
	{
		return new ViewBuilder();
	}

	/**
	 * @param	MvcDispatcherInterface $dispatcher
	 * @param	InterceptChainInterface $preChain
	 * @param	InterceptChainInterface $postChain
	 * @return	MvcFront
	 */
	public function createFront(MvcDispatcherInterface $dispatcher = null,
								InterceptChainInterface $preChain  = null,
								InterceptChainInterface $postChain = null)
	{
		$preList = ConfigRegistry::get('pre-filters', array());
		if (null === $preChain) {
			$preChain = new InterceptChain();
		}

		if (is_array($preList) && ! empty($preList)) {
			$preChain->loadFilters($preList);
		}

		$postList = ConfigRegistry::get('post-filters', array());
		if (null === $postChain) {
			$postChain = new InterceptChain();
		}

		if (is_array($postList) && ! empty($postList)) {
			$postChain->loadFilters($postList);
		}

		if (null === $dispatcher) {
			$dispatcher = new MvcDispatcher();
		}

		return new MvcFront($dispatcher, $preChain, $postChain);
	}

	/**
	 * @return	MvcDispatcher
	 */
	public function createDispatcher()
	{
		return new MvcDispatcher();
	}

	/**	
	 * @return	TaskHandler
	 */
	public function createTaskHandler()
	{
		return new TaskHandler();
	}

	public function createHttpResponse($data, 
									   $status,
									   $version = null,
									   array $headers = null)
	{
		return new HttpResponse($data, $status, $version, $headers);
	}

	/**
	 * @return	HttpOutputInterface
	 */
	public function createHttpOutput()
	{
		return new HttpOutput();
	}

	public function createConsoleOutput()
	{
		return new ConsoleOutput();
	}
}
