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
 * Create connection and connector objects used by the datasource
 */
interface DbFactoryInterface
{
	/**
	 * 1) throw an InvalidArgumentException if $class is not a string or empty
	 * 2) instantiate class passing $data into it.
	 * 3) no need to check the data type of $data as any class that implements
	 *    DbConnInterface will check that 
	 * 4) determine if the resulting object implements DbConnInterface 
	 *		throw an InvalidArgumentException if it fails
	 * 5) return the newly create connection object
	 * 
	 * @param	string	$class	
	 * @param	mixed	array | Appfuel\DataStructure\DictionaryInterface
	 * @return	DbConnInterface
	 */
	public function createConnection($class, $data);

	/**
	 * @param	DbConnInterface		$master
	 * @param	DbConnInterface		$slave
	 * @param	string	$connectorClass
	 * @return	DbConnectorInterface
	 */
	public function createConnector(DbConnInterface $master,
									DbConnInterface $slave = null, 
									$connectorClass = null);
}
