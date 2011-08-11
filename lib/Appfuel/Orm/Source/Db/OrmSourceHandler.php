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
namespace Appfuel\Orm\Source\Db;

use Appfuel\Framework\Exception,
	Appfuel\View\Template as ViewTemplate,
	Appfuel\Db\Request\QueryRequest,
	Appfuel\Db\Request\MultiQueryRequest,
	Appfuel\Db\Request\PreparedRequest,
	Appfuel\Framework\File\FileManager,
	Appfuel\Framework\Db\Request\RequestInterface,
	Appfuel\Framework\Db\Handler\HandlerInterface,
	Appfuel\Framework\Orm\Source\SourceHandlerInterface,
	Appfuel\Framework\Orm\Identity\IdentityHandlerInterface;

/**
 * The database source handles preparing the sql and executing the database
 * handler and passing back the result
 */
class OrmSourceHandler implements SourceHandlerInterface
{
	/**
	 * Database handler used to issue database operations
	 * @var DbHandler
	 */
	protected $db = null;

	/**
	 * The identity handler takes care of all the mapping and location 
	 * of objects concerned with the domain
	 * @var IdentityHandlerInterface
	 */
	protected $identity = null;

	/**
	 * @param	AssemblerInterface $asm
	 * @return	OrmRepository
	 */
	public function __construct(HandlerInterface $db,
								IdentityHandlerInterface $identity)
	{
		$this->db = $db;
		$this->identity = $identity;
	}

	/**
	 * @return	DbHandlerInterface
	 */
	public function getDataHandler()
	{
		return $this->db;
	}

	/**
	 * @return	IndentityHandlerInterface
	 */
	public function getIdentityHandler()
	{
		return $this->identity;
	}

	/**
	 * @param	string	$cat	this is the category of request to use
	 * @param	string	$type	type of operation the request will ask for
	 *							valid values: read|write|both
	 * @return	QueryRequest | false on failure
	 */
	public function createRequest($cat, $type = 'read')
	{
		$valid = array('query', 'multiquery', 'prepared');
		if (empty($cat) || ! is_string($cat) || ! in_array($cat, $valid)) {
			return false;
		}

		switch($cat) {
			case 'query'	 : $request = new QueryRequest($type);		break;
			case 'multiquery': $request = new MultiQueryRequest($type); break;
			case 'prepared'	 : $request = new PreparedRequest($type);   break;
			default: 
				return false;
		}
				
		return $request;
	}

	/**
	 * Create a simple view template for the sql file given
	 *
	 * @param	string	$path	relative path to sql file
	 * @return	ViewTemplate
	 */
	public function createTemplate($relativePath, $fullDomainPath = false)
	{
		$identity = $this->getIdentityHandler();

		if (true === $fullDomainPath) {
			$namespace = $identity->getRootNamespace();
			$dirPath  = FileManager::namespaceToPath($namespace);
		}
		else {
			$namespace = get_class($this);
			$dirPath  = FileManager::classNameToDir($namespace);
		}
			
		$fullPath = "{$dirPath}/{$relativePath}";
		$file     = FileManager::createAppfuelFile($fullPath);
		$scope    = $this->createSqlScope();

		return new ViewTemplate($file, $scope);
	}
	
	/**
	 * Scope used in sql templates
	 * 
	 * @return	Sql\Scope
	 */
	public function createSqlScope()
	{
		return new Sql\SqlScope();
	}

	/**
	 * Used the database handler to send a request to the database
	 * 
	 * @param	RequestInterface
	 * @return	ResponseInterface
	 */
	public function sendRequest(RequestInterface $request)
	{
		return $this->getDataHandler()
					->execute($request);
	}
}
