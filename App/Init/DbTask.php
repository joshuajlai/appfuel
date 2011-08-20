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
	Appfuel\Db\Handler\DbInitializer,
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
        $conns = Registry::get('db_conns', array());
		$init = $this->createDbInitializer();
		return $init->initialize($conns);
	}

	/**
	 * @return	ErrorDisplay
	 */
	protected function createDbInitializer()
	{
		return new DbInitializer();
	}
}
