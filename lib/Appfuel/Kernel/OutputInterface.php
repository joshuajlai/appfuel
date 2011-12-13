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
	Appfuel\Kernel\Mvc\AppContextInterface;

/**
 * The kernel output uses the KernelRegistry to figure out which 
 * application strategy is currently deployed by the front controller 
 * and then uses that to build the correct Output object to output the error
 */
interface OutputInterface
{	
	/**
	 * @return	HttpOutputInterface
	 */
	public function getHttpOutput();

	/**
	 * @return	ConsoleOutputInterface
	 */
	public function getConsoleOutput();

	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function render($data);
	
	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function renderError($msg, $code = 500);

	/**
	 * @param	scalar | AppContextInteface	$data
	 * @return	null
	 */
	public function renderConsole($data);

	/**
	 * @param	scalar | AppContextInteface	$data
	 * @return	null
	 */
	public function renderHttp($data);

	/**
	 * Detemine if the output engine is http by checking the KernelRegistry
	 * for the application strategy
	 *
	 * @return	bool
	 */
	public function isHttpOutput();
}
