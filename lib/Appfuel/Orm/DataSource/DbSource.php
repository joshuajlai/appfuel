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
namespace Appfuel\Orm\DataSource;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Sql\SqlBuilderInterface,
	Appfuel\Framework\Db\Handler\HandlerInterface,
	Appfuel\Framework\Orm\Domain\DbDomainIdentityInterface,
	Appfuel\Framework\Orm\DataSource\DataSourceInterface;

/**
 * The database source handles preparing the sql and executing the database
 * handler and passing back the result
 */
class DbSource implements DataSourceInterface
{
	/**
	 * Handles actual operations to and from the data source
	 * @var	DataSourceInterface
	 */
	protected $sqlBuilder = null;

	/**
	 * Database handler used to issue database operations
	 * @var DbHandler
	 */
	protected $handler = null;

	/**
	 * Identity is used to map domain fields to column names and retrieve
	 * table information
	 *
	 * @var	DomainIdentityInterface
	 */
	protected $identity = null;

	/**
	 * @param	AssemblerInterface $asm
	 * @return	OrmRepository
	 */
	public function __construct(DbDomainIdentityInterface	$identity,
								HandlerInterface			$dbHandler,
								SqlBuilderInterface			$sqlBuilder)
	{
		$this->identity   = $identity;
		$this->sqlBuilder = $sqlBuilder;
		$this->handler    = $dbHandler;
	}

	/**
	 * @return	HandlerInterface
	 */
	public function getDataHandler()
	{
		return $this->handler;
	}

	/**
	 * @return	SqlBuilderInterface
	 */
	public function getSqlBuilder()
	{
		return $this->sqlBuilder;
	}

	/**
	 * @return	DomainIdentityInterface
	 */
	public function getIdentity()
	{
		return $this->identity;
	}
}
