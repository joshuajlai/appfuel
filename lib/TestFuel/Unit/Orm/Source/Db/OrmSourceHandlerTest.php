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
namespace TestFuel\Test\Orm\DataSource;

use StdClass,
	Appfuel\Db\Handler\DbHandler,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Orm\Source\Db\OrmSourceHandler,
	Appfuel\Orm\Identity\OrmIdentityHandler;

/**
 * The Database source handler builds the sql and the database request and
 * sends the request to the database handing back a valid database response
 */
class OrmSourceHandlerTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var SourceHandler
	 */
	protected $sourceHandler = null;

	/**
	 * Db Handler
	 * @var DbHandler
	 */
	protected $dbHandler = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->dbHandler     = new DbHandler();
		$this->identity		 = new OrmIdentityHandler();
		$this->sourceHandler = new OrmSourceHandler(
			$this->dbHandler,
			$this->identity
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->dbHandler);
		unset($this->sourceHandler);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Source\SourceHandlerInterface',
			$this->sourceHandler
		);
	}

	/**
	 * @return null
	 */
	public function testGetDataHandler()
	{
			$this->assertInstanceOf(
			'Appfuel\Framework\Db\Handler\HandlerInterface',
			$this->sourceHandler->getDataHandler()
		);

	
	}

	/**	
	 * Creates one of three database requests based on category:
	 * QueryRequest, MultiQueryRequest, PreparedRequest.
	 *
	 * @return	null
	 */
	public function testCreateRequestsDefaultOperationType()
	{
		$request = $this->sourceHandler->createRequest('multiquery');
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Request\MultiQueryRequestInterface',
			$request
		);

		$this->assertEquals('read', $request->getType());

		$request = $this->sourceHandler->createRequest('prepared');
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Request\PreparedRequestInterface',
			$request
		);
		$this->assertEquals('read', $request->getType());

		/* invalid calls return false */
		$this->assertFalse($this->sourceHandler->createRequest(''));
		$this->assertFalse($this->sourceHandler->createRequest(123));
		$this->assertFalse($this->sourceHandler->createRequest(array(1,2,3)));
		$this->assertFalse($this->sourceHandler->createRequest(new StdClass()));
		
		/* has to be query|multiquery|prepared */
		$this->assertFalse($this->sourceHandler->createRequest('not-found'));
	}

	/**
	 * @return	null
	 */
	public function testCreateRequestQueryDefault()
	{
		/* the default operation type is read */
		$request = $this->sourceHandler->createRequest('query');
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Request\RequestInterface',
			$request
		);
		$this->assertEquals('read', $request->getType());

		return $request;
	}

	/**
	 * @depends	testCreateRequestQueryDefault
	 * @return	null
	 */
	public function testSendRequest($request)
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$request->setSql($sql);
		$response = $this->sourceHandler->sendRequest($request);
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\DbResponseInterface',
			$response
		);
	}
}
