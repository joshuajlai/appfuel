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


use Appfuel\Output\OutputEngine,
	Appfuel\Http\HttpOutputAdapter,
	Appfuel\Console\ConsoleOutputAdapter;

/**
 * There are only two types of output that the kernal output will render to
 * http and console. The following application types have the corresponding
 * output adapters:
 * app-console => console
 * app-api	   => http
 * app-json	   => http
 * app-page	   => http
 * 
 */
class KernelOutput extends OutputEngine
{
	/**
	 * @return	KernalOutput
	 */
	public function __construct()
	{
		if (defined('AF_APP_TYPE') && 'app-console' !== AF_APP_TYPE) {
			$adapter = new HttpOutputAdapter();
		}
		else {
			$adapter = new ConsoleOutputAdapter();
		}

		parent::__construct($adapter);
	}
}
