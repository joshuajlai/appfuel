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
namespace Appfuel\DataSource\Db;

use InvalidArgumentException;

/**
 * Creates DbConnectorInterfaces from an array structure
 */
class DbFactory implements DbFactoryInterface
{
	/**
	 * @param	string	$class	
	 * @param	mixed	array | Appfuel\DataStructure\DictionaryInterface
	 * @return	DbConnInterface
	 */
	public function createConnection($class, $data)
	{
		if (! is_string($class) || empty($class)) {
			$err = 'connection adapter class must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$conn = new $class($data);
		if (! $conn instanceof DbConnInterface) {
			$inter = 'Appfuel\DataSource\Db\DbConnInterface';
			$err = "connection class -($class) does not implement -($inter)";
			throw new InvalidArgumentException($err);
		}

		return $conn;
	}

	/**
	 * @param	DbConnInterface		$master
	 * @param	DbConnInterface		$slave
	 * @param	string	$connectorClass
	 * @return	DbConnectorInterface
	 */
	public function createConnector(DbConnInterface $master,
									DbConnInterface $slave = null, 
									$connectorClass = null)
	{
		if (is_string($connectorClass) && ! empty($connectorClass)) {
			$connector = new $connectorClass($master, $slave);
			if (! $connector instanceof DbConnectorInterface) {
				$err  = 'connector class must implment Appfuel\DataSource';
				$err .= '\Db\DbConnectorInterface';
				throw new InvalidArgumentException($err);
			}
		}
		else {
			$connector = new DbConnector($master, $slave);
		}

		return $connector;
	}
}
