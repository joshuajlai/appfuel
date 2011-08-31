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
	 * Framework finds operations from a file built during deployment to avoid
	 * accessing the database. This is the location of that file
	 * @var Appfuel\Framework\File\FrameworkFile
	 */
	protected $opListFile = null;

	/**
	 * Assign the location of the operations build file
	 * 
	 * @return	Repository
	 */
	public function __construct()
	{
		parent::__construct();
		$path = 'codegen/operations.php';
		$this->opListFile = FileManager::createFrameworkFile($path);
	}

	/**
	 * @return Appfuel\Framework\File\FrameworkFile
	 */
	public function getOperationListFile()
	{
		return $this->opListFile;
	}

	/**
	 * The generated operation list is a basic array kept in a standard
	 * variable name 'opList' which we will load in the OperationList class
	 * which will allow us to search on available operations
	 *
	 * @return	null
	 */
	public function loadStaticOperations()
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
