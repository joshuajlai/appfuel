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
	Appfuel\Framework\Db\Sql\SqlBuilderInterface,
	Appfuel\Framework\Db\Handler\HandlerInterface,
	Appfuel\Framework\Orm\Source\SourceHandlerInterface;

/**
 * The database source handles preparing the sql and executing the database
 * handler and passing back the result
 */
class SourceHandler implements SourceHandlerInterface
{
	/**
	 * Database handler used to issue database operations
	 * @var DbHandler
	 */
	protected $dbHandler = null;

	/**
	 * @param	AssemblerInterface $asm
	 * @return	OrmRepository
	 */
	public function __construct(HandlerInterface $dbHandler)
	{
		$this->dbhandler = $dbHandler;
	}

	/**
	 * @return	HandlerInterface
	 */
	public function getDataHandler()
	{
		return $this->dbHandler;
	}
}
