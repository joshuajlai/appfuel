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
namespace Appfuel\Orm;

use Appfuel\Db\Handler\DbHandler,
	Appfuel\Framework\Orm\OrmFactoryInterface;

/**
 * The Orm factory interface enforces a series of creation methods used 
 * to build orm objects. It is used by the repository to create a
 * SourceHandler which determines what datasource is being used, 
 * DataBuilder which determines how the data will be formatted and built, and
 * IdentityHandler which is used for mapping raw data into domain data
 */
abstract class AbstractOrmFactory implements OrmFactoryInterface
{
	/**
	 * The database handler is used to communicate with the database 
	 *
	 * @return	DbHandler
	 */
	public function createDbHandler()
	{
		return new DbHandler();
	}

	/**
	 * The data builder is used to convert raw data from a given source into
	 * domain models or domain datasets into different formats like arrays
	 * or strings
	 *
	 * @return	DataBuilderInterface
	 */
	public function createDataBuilder()
	{
		return new Domain\OrmDataBuilder($this->createObjectFactory());
	}

	/**
	 * The object factory is responsible for create new domain or domain 
	 * related objects. It is used by the domains data builder
	 *
	 * @return	Domain\ObjectFactory
	 */
	public function createObjectFactory()
	{
		return new Domain\OrmObjectFactory();
	}

	/**
	 * Assembler which is exclusively used by the repository requires
	 * a source handler and data builder to function
	 *
	 * @return	Respository\Assembler
	 */
	public function createAssembler()
	{
		$source  = $this->createSourceHandler();
		$builder = $this->createDataBuilder();
		return new Repository\OrmAssembler($source, $builder);
	}
}
