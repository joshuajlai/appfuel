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

use RunTimeException,
	mysqli as MysqliDriver,
	Appfuel\DataSource\Db\DbResponseInterface,
	Appfuel\DataSource\Db\DbRequestInterface;

/**
 * The database adapter is 
 */
interface MysqliAdapterInterface
{
	public function execute(MysqliDriver        $driver,
							DbRequestInterface  $request,
							DbResponseInterface $response);
}
