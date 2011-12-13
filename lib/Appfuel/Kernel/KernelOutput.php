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
	Appfuel\Console\ConsoleOutput;

/**
 * The kernel output uses the KernelRegistry to figure out which 
 * application strategy is currently deployed by the front controller 
 * and then uses that to build the correct Output object to output the error
 */
class KernelOutput implements OutputInterface
{
	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function render($data)
	{
		if ($this->isHttpOutput()) {
			$output = new HttpOutput();
			$output->render($data);
			return;
		}

		$output = new ConsoleOutput();
		$output->render($data);
	}
	
	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function renderError($msg, $code = 500)
	{
		if ($this->isHttpOutput()) {
			$output = new HttpOutput();
			$response = new HttpResponse($msg, $code);
			$output->renderResponse($response);
			return;
		}

		$output = new ConsoleOutput();
		$output->render($msg);
	}

	public function isHttpOutput()
	{
		$strategy = KernelRegistry::getParam('af-output-strategy', 'console');
		if (empty($strategy) || ! is_string($strategy)) {
			return false;
		}
		$http = array('html', 'ajax');
		if (in_array($strategy, $http, true)) {
			return true;
		}

		return false;
	}
}
