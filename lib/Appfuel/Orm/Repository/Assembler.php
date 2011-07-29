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
namespace Appfuel\Orm\Repository;

use BadMethodCallException,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\DomainBuilderInterface,
	Appfuel\Framework\Orm\DataSource\DataSourceInterface,
	Appfuel\Framework\Orm\Repository\AssemblerInterface;

/**
 * The database assembler handles the internal logic necessary to assemble
 * a repository call into usable user data. This is done within three 
 * defined steps:
 * 
 * processCriteria:	takes the repository generated criteria and turns it
 *					into a database request object using the orm sql factory.
 *					this always returns a database request
 *
 * executeDataSource: takes a database request and uses a database handler
 *					  to convert it into domain data. It is here we decide
 *					  to add a callback for mapping raw database row into 
 *					  mapped domain data.
 *
 * processResults: takes the response form the database and used the domain
 *				   builder to marshal that data into domains, arrays or what
 *				   every shape the criteria specifies.
 *
 * After these steps are completed data is ready to be handed back the 
 * repository to be given to controllers requesting this data.	
 */
class Assembler implements AssemblerInterface
{
	/**
	 * Handles actual operations to and from the data source
	 * @var	DataSourceInterface
	 */
	protected $dataSource = null;

	/**
	 * Used to build data for different shapes like a domain object or 
	 * an array, or string with mapped data
	 * @var DomainBuilder
	 */
	protected $domainBuilder = null;

	/**
	 * @param	AssemblerInterface $asm
	 * @return	OrmRepository
	 */
	public function __construct(DataSourceInterface $dataSource,
								DomainBuilderInterface, $domainBuilder)
	{
		$this->dataSource = $dataSource;
		$this->domainBuilder = $domainBuilder;
	}

	/**
	 * @return	DataSourceInterface
	 */
	public function getDataSource()
	{
		return $this->dataSource;
	}

	/**
	 * @return	DomainBuilderInterface
	 */
	public function getDomainBuilder()
	{
		return $this->domainBuilder;
	}
}
