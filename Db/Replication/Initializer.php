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
namespace Appfuel\Db\Replication;

use mysqli,
	Appfuel\Framework\Exception,
	Appfuel\Db\Connection\Parser,
	Appfuel\Db\Connection\DetailFactory,
	Appfuel\Framework\Db\Connection\DetailFactoryInterface,
	Appfuel\Framework\Db\InitializerInterface;

/**
 * The primary responsibility of the intitailzer is to initialize a list
 * of database strings into database connection objects and put them into the
 * the db resource pool.
 */
class Initializer
{
	/**
	 * @return	Initializer
	 */
	public function createConnectionDetailFactory()
	{
		return new DetailFactory(new Parser());
	}

	/**
	 * Create connection objects from the connectionstrings intialize them
	 * and save them to the Pool
	 * 
	 * @param	array	$connStrings
	 * @param	DetailFactoryInterface	$factory
	 * @return	bool
	 */
	public function initialize(array $connStrings,
							   DetailFactoryInterface $factory = null)
	{

		if (null === $factory) {
			$factory = $this->createConnectionDetailFactory();
		}

		$connDetail = null;
		foreach ($connStrings as $connString) {
			$connDetail = $factory->createConnectionDetail($connString);
			if (! $connDetail) {
				return false;
			}

			$type    = $connDetail
			$vendor  = $connDetail->getVendor();
			$adapter = $connDetail->getAdapter(); 
		}
	}
}
