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

use mysqli_stmt,
	Appfuel\Error\ErrorStackInterface;

/**
 */
interface MysqliPreparedResultInterface extends MysqliResultInterface
{
	/**
	 * 
	 * @return bool
	 */
	public function organizePreparedResults(mysqli_stmt $stmt);

	/**
	 * Fetch resultset from a prepared statement
	 * 
	 * @param	mysli_stmt	$stmtHandle 
	 * @param	$filter		$null	callback or closure to filter a row
	 * @return	mixed
	 */
	public function fetchPreparedData(mysqli_stmt $stmt, 
									  ErrorStackInterface $stack,
									  $filter = null);
}
