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
namespace Appfuel\App\Init;

use Appfuel\Framework\Registry,
	Appfuel\Framework\Exception,
	Appfuel\Domain\Operation\OpRouteList,
	Appfuel\Framework\App\Init\TaskInterface;

/**
 * Initialize the static route list
 */
class OpRouteTask implements TaskInterface
{

    /**  
     * @param   string		$file	file path to config ini
	 * @return	Env\State 
     */
	public function init()
	{
		$file = AF_CODEGEN_PATH . '/operations.php';
		if (! file_exists($file)) {
			throw new Exception("Could not find generated db file at $file");
		}
		
		$data = require $file;
		if (empty($data) || ! is_array($data)) {
			throw new Exception("Static operational routes must be an array");
		}

		OpRouteList::setOperationalRoutes($data);
	}

	/**
	 * @return	ErrorDisplay
	 */
	protected function createDbInitializer()
	{
		return new DbInitializer();
	}
}
