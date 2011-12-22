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
namespace Appfuel\Kernel;

use Appfuel\Http\HttpOutput,
	Appfuel\Http\HttpResponse,
	Appfuel\Http\HttpOutputInterface,
	Appfuel\Http\HttpResponseInterface,
	Appfuel\Console\ConsoleOutput,
	Appfuel\Console\ConsoleOutputInterface,
	Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * The kernel output uses the KernelRegistry to figure out which 
 * application strategy is currently deployed by the front controller 
 * and then uses that to build the correct Output object to output the error
 */
class KernelOutput implements OutputInterface
{	
	/**
	 * Output engine used to render http responses
	 * @var HttpOutputInterface
	 */
	protected $http = null;
	
	/**
	 * Output engine used to render console responses
	 * @var ConsoleOutputInterface
	 */
	protected $console = null;

	/**
	 * @param	HttpOutputInterface $http
	 * @param	ConsoleOutputInterface $console
	 * @return	KernelOutput
	 */
	public function __construct(HttpOutputInterface $http = null,
								ConsoleOutputInterface $console = null)
	{
		if (null === $http) {
			$http = new HttpOutput();
		}
		$this->http = $http;

		if (null === $console) {
			$console = new ConsoleOutput();
		}
		$this->console = $console;
	}

	/**
	 * @return	HttpOutputInterface
	 */
	public function getHttpOutput()
	{
		return $this->http;
	}

	/**
	 * @return	ConsoleOutputInterface
	 */
	public function getConsoleOutput()
	{
		return $this->console;
	}

	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function render($data)
	{
		if ($this->isHttpOutput()) {
			$this->renderHttp($data);
		}
		else {
			$this->renderConsole($data);
		}
	}
	
	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function renderError($msg, $code = null)
	{
		if ($this->isHttpOutput()) {
			if (empty($code) || ! is_int($code) || 
				$code < 100 || $code >= 600) {
				$code = 500;
			}

			$response = new HttpResponse($msg, $code);
			$this->renderHttp($response);
			return;
		}

		$console = $this->getConsoleOutput();
		$console->renderError($msg);
	}

	/**
	 * @param	scalar | AppContextInteface	$data
	 * @return	null
	 */
	public function renderConsole($data)
	{
		$output = $this->getConsoleOutput();
		if ($data instanceof MvcContextInteface) {
			$text =(string) $data->getView();;
		}
		else {
			$text = $data;
		}

		$output->render($text);
	}

	/**
	 * @param	scalar | AppContextInteface	$data
	 * @return	null
	 */
	public function renderHttp($data)
	{
		$output = $this->getHttpOutput();
		if ($data instanceof MvcContextInterface) {
			$response = $data->get('http-response', null);
			if (! ($response instanceof HttpResponseInterface)) {
				$headers = $data->get('http-headers', array());
				$view    = $data->getView();
				$code    = $data->getExitCode();
				$response = new HttpResponse($view->build(), $code);
				if (! empty($headers) && is_array($headers)) {
					$response->loadHeaders($headers);
				}
			}

		}
		else if ($data instanceof HttpResponseInterface) {
			$response = $data;
		}
		else {
			$response = new HttpResponse($data);
		}
			
		$output->render($response);
	}

	/**
	 * Detemine if the output engine is http by checking the KernelRegistry
	 * for the application strategy
	 *
	 * @return	bool
	 */
	public function isHttpOutput()
	{
		$strategy = KernelRegistry::getAppStrategy();
		if (empty($strategy) || 'console' === $strategy) {
			return false;
		}

		return true;
	}
}
