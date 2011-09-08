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
namespace Appfuel\Domain\Operation;

use Appfuel\Framework\File\FileManager,
	Appfuel\Orm\Repository\OrmRepository,
	Appfuel\Framework\Domain\Operation\OperationalRouteInterface;

/**
 * Used to manage database interaction throught a uniform interface that 
 * does not care about the specifics of the database
 */
class Repository extends OrmRepository
{
	/**
	 * @return Appfuel\Framework\File\FrameworkFile
	 */
	public function findOperationalRoute($routeString)
	{
		$object = OpRouteList::findObject($routeString);
		if (! $object instanceof OperationalRouteInterface) {
			$raw = OpRouteList::findRaw($routeString);
			if (! $raw) {
				return false;
			}
			$opRoute = new OperationalRoute();
			$opRoute->_marshal($raw)
					->_markClean();
			OpRouteList::addObject($routeString, $opRoute);
		}

		return $opRoute;
	}

	/**
	 * Orm factory is used to create objects needed by the assembler
	 * which manages the datasource and databuilder
	 *
	 * @return	OrmFactory
	 */
	protected function createOrmFactory()
	{
		return new OrmFactory();
	}
}
