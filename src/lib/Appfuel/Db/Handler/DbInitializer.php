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

use Appfuel\Db\Connection\Parser,
	Appfuel\Db\Connection\DetailFactory,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Handler\InitializerInterface,
	Appfuel\Framework\Db\Connection\DetailFactoryInterface;

/**
 * The primary responsibility of the intitailzer is to initialize a list
 * of database strings into database connection objects and put them into the
 * the db resource pool.
 */
class DbInitializer implements InitializerInterface
{

	/**
	 * @return ConnectionDetailFactory
	 */
	public function createConnectionDetailFactory()
	{
		return new DetailFactory(new Parser());
	}

	/**
	 * @param	array	$connStrings
	 * @param	DetailFactoryInterface	$factory
	 * @return	bool
	 */
	public function initialize($connStrings, 
							  DetailFactoryInterface $factory = null)
	{
		if (empty($connStrings)) {
			return false;
		}

		if (is_string($connStrings)) {
			$connStrings = array($connStrings);
		}

		if (null === $factory) {
			$factory = $this->createConnectionDetailFactory();
		}
		$pool = new Pool();
		foreach ($connStrings as $connString) {
			$detail   = $factory->createConnectionDetail($connString);
			$type     = $detail->getType();
			$vendor   = ucfirst($detail->getVendor());
			$adapter  = ucfirst($detail->getAdapter());
			$class = "Appfuel\\Db\\$vendor\\$adapter\\Connection";
			
			$conn = new $class($detail);
			$conn->initialize();
			
			$pool->addConnection($conn, $type);			
		}

		DbHandler::setPool($pool);
	}
}
