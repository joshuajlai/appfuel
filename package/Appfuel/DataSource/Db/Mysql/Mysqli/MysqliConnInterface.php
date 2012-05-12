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
namespace Appfuel\DataSource\Db\Mysql\Mysqli;

use mysqli as MysqliDriver,
	Appfuel\DataSource\Db\DbConnInterface;

/**
 * The primary responsibilty of the DbConnection is encapsulate vendor specific
 * details for connecting and disconnecting from the database server as well 
 * as creating a DbQuery object used to issue database queries. Opening and 
 * closing as well as finding errors are done throug delegation of the native
 * mysqi object which is created in the constructor
 */
interface MysqliConnInterface extends DbConnInterface
{
	/**
	 * @param	array $params
	 * @return	int
	 */
	public function buildConnectionFlags(array $data);

	public function setConnectionOptions(MysqliDriver $driver, array $data);

}
