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

use Appfuel\Db\DbManager,
	Appfuel\Framework\Registry,
	Appfuel\Framework\Exception,
	Appfuel\Framework\App\Init\TaskInterface;

/**
 * Use the db initializer to intialize the database system
 */
class DbTask implements TaskInterface
{
    /**  
     * @param   string		$file	file path to config ini
	 * @return	Env\State 
     */
	public function init()
	{
		$file = AF_CODEGEN_PATH . '/db.php';
		if (! file_exists($file)) {
			throw new Exception("Could not find generated db file at $file");
		}
		
		$data = include $file;
		if (empty($data) || ! is_array($data)) {
			throw new Exception("Db data must be in an array");
		}
		DbManager::setRawData($data);
		DbManager::initialize();
	}

	/**
	 * @return	ErrorDisplay
	 */
	protected function createDbInitializer()
	{
		return new DbInitializer();
	}
}
