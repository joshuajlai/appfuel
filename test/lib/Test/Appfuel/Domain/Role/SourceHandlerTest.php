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
	Appfuel\Domain\Role\SourceHandler,
	Appfuel\Domain\Role\IdentityHandler;

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
		$this->handler = new SourceHandler(
			new DbHandler(),
			new IdentityHandler()
		);
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
	
		$expr1 = new DomainExpr('role.id=3');
		$expr2 = new DomainExpr('user.name is not empty');

		$criteria->add('domain-key', 'role')
				 ->add('id', 3)
				 ->addExpr('where-filters', $expr1, 'or');

		//$result = $this->handler->fetchDesendantsById($criteria);
		//echo "\n", print_r($result,1), "\n";exit;
				
	}
}
