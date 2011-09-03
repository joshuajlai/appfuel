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
	Appfuel\Orm\Repository\OrmRepository;

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
