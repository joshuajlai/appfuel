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
namespace Appfuel\Db\Handler;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Handler\InitializerInterface,
	Appfuel\Framework\Db\Connection\DetailFactoryInterface,
	Appfuel\Db\Connection\ConnectionDetailFactory;

/**
 * The primary responsibility of the intitailzer is to initialize a list
 * of database strings into database connection objects and put them into the
 * the db resource pool.
 */
class Initializer extends InitializerInterface
{

	/**
	 * @return ConnectionDetailFactory
	 */
	public function createConnectionDetailFactory()
	{
		return new ConnectionDetailFactory(new Parser());
	}

	/**
	 * @param	array	$connStrings
	 * @param	DetailFactoryInterface	$factory
	 * @return	bool
	 */
	public function initialize(array $connStrings, 
							  DetailFactoryInterface $factory = null)
	{
		if (null === $factory) {
			$factory = new ConnectionFactory()
		}
	}
}
