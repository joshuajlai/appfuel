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
namespace Appfuel\Framework\Orm;

/**
 * The Orm factory interface enforces a series of creation methods used 
 * to build orm objects. It is used by the repository to create a
 * SourceHandler which determines what datasource is being used, 
 * DataBuilder which determines how the data will be formatted and built, and
 * IdentityHandler which is used for mapping raw data into domain data
 */
interface OrmFactoryInterface
{
	/**
	 * Handles interactions with the database
	 *
	 * @return	Appfuel\Framework\Db\DbHandlerInterface
	 */
	public function createDbHandler();

	/**
	 * The source handler is the generic system used to handle specific
	 * data sources. This way the respository does not care about the specific 
	 * data source just the SourceHandlers interface
	 *
	 * @return SourceHandlerInterface
	 */
	public function createSourceHandler();
	
	/**
	 * The data builder is used to convert raw data from the source into
	 * domain models or domain data shapped into different formats like arrays
	 * or strings
	 *
	 * @return	DataBuilderInterface
	 */
	public function createDataBuilder();

	/**
	 * The identity handler is a facade hiding simple or complex mapping 
	 * systems depending on what is needed
	 *
	 * @return	IdentityHandlerInterface
	 */
	public function createIdentityHandler();
	
	/**
	 * The object factory is responsible for create new domain or domain 
	 * related objects.
	 *
	 * @return	Domain\ObjectFactory
	 */
	public function createObjectFactory();

	/**
	 * The assembler is used by the repository to coordinate the interactions
	 * with the source handler (getting data to and from the data source) and
	 * the data builder (marshalling domain and datasets into existence)
	 *
	 * @return	Repository\AssemblerInterface
	 */
	public function createAssembler();
}
