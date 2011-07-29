<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\Orm\DataSource;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Sql\SqlBuilder,
	Appfuel\Db\Handler\DbHandler,
	Appfuel\Orm\Domain\DbIdentity,
	Appfuel\Orm\DataSource\DbSource;

/**
 */
class DbSourceTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var DbSource
	 */
	protected $dataSource = null;

	/**
	 * Domain Identity
	 * @var DomainIndentity
	 */		
	protected $identity = null;

	/**
	 * Db Handler
	 * @var DbHandler
	 */
	protected $handler = null;
	
	/**
	 * Sql Builder 
	 * @var string
	 */
	protected $sqlBuilder = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->handler    = new DbHandler();
		$this->identity   = new DbIdentity();
		$this->sqlBuilder = new SqlBuilder();
		$this->dataSource = new DbSource(
			$this->identity,
			$this->handler,
			$this->sqlBuilder
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->dataSource);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\DataSource\DataSourceInterface',
			$this->dataSource
		);
	}

	/**
	 * @return null
	 */
	public function testImmutableMembers()
	{
		$this->assertSame($this->handler, $this->dataSource->getDataHandler());
		$this->assertSame(
			$this->sqlBuilder, 
			$this->dataSource->getSqlBuilder()
		);

		$this->assertSame(
			$this->identity,
			$this->dataSource->getIdentity()
		);
	}
}
