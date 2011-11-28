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
namespace Appfuel\Kernel\Mvc;


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
class MvcOutput extends OutputEngine
{
	/**
	 * @return	KernalOutput
	 */
	public function __construct($strategy)
	{
		if (empty($strategy) || ! is_string($strategy)) {
			$err = 'strategy must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if ('console' !== $strategy) {
			$adapter = new HttpOutputAdapter();
		}
		else {
			$adapter = new ConsoleOutputAdapter();
		}

		parent::__construct($adapter);
	}
}
