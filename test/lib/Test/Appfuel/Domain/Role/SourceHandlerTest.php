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
namespace Test\Appfuel\Domain\Role;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Handler\DbHandler,
	Appfuel\Orm\Domain\DomainExpr,
	Appfuel\Orm\Repository\Criteria,
	Appfuel\Domain\Role\SourceHandler;

/**
 * Executes sql against the database and returns the result 
 */
class SourceHandlerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var SourceHandler
	 */
	protected $handler = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->handler = new SourceHandler(new DbHandler());
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->handler);
	}

	/**
	 * @return null
	 */
	public function testHasInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Domain\Role\SourceHandler',
			$this->handler
		);
	}

	/**
	 * @return null
	 */
	public function testFetchDesendantsById()
	{
		$criteria = new Criteria();
	
		$where = new DomainExpr('role.id=3');
		$criteria->add('domain-key', 'role')
				 ->addExpr('where-filters', $where);

		//$result = $this->handler->fetchDesendantsById($criteria);
				
	}
}
