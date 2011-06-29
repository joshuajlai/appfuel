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
namespace Appfuel\Db\Mysql\Adapter;

use Mysqli,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Adapter\AdapterFactoryInterface,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface;

/**
 * Create the MysqliAdapter using a ConnectionDetail
 */
class AdapterFactory implements AdapterFactoryInterface
{
	/**
	 * @param	ConnectionDetailInterface	$conn
	 * @return	MysqliAdapter
	 */
	static public function createAdapter(ConnectionDetailInterface $conn)
	{
		return new MysqliAdapter(new Server($conn));
	}
}
