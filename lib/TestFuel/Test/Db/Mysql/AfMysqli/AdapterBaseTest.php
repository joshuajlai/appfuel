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
namespace TestFuel\Test\Db\Mysql\AfMysqli;

use mysqli,
	TestFuel\TestCase\DbTestCase,
	Appfuel\Db\Mysql\AfMysqli\Connection,
	Appfuel\Db\Mysql\AfMysqli\AdapterBase,
	Appfuel\Db\Connection\ConnectionDetail;

/**
 * Test the adapters ability to wrap mysqli
 */
class AdapterBaseTest extends DbTestCase
{
	/**
	 * System under test
	 * @var AdapterBase
	 */
	protected $adapter = null;

	/**
	 * Used to connect and get driver for adapter
	 * @var Connection
	 */
	protected $conn = null;

	/**
	 * Mysqli object used only parameter in constructor
	 * @var mysqli
	 */
	protected $driver = null;

	/**
	 * Used to test the instance of a db response
	 * @var string
	 */
	protected $responseClass = 'Appfuel\Db\DbResponse';

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->conn = new Connection($this->getConnectionDetail());
		$this->assertTrue($this->conn->initialize());
		$this->driver = $this->conn->getDriver();
		
		$this->adapter = new AdapterBase($this->driver);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->assertTrue($this->conn->close());
		unset($this->driver);
		unset($this->adapter);
		unset($this->conn);
	}

	/**
	 * An immutable object passed in via the constructor
	 *
	 * @return null
	 */
	public function testGetDriver()
	{
		$this->assertSame($this->driver, $this->adapter->getDriver());
	}

	/**
	 * @return	null
	 */
	public function testCreateError()
	{
		$error = $this->adapter->createError(1, 'my error', 'my sqlstate');
		$this->assertInstanceOf('Appfuel\Db\DbError', $error);

		
		$this->assertEquals(1, $error->getCode());
		$this->assertEquals('my error', $error->getMessage());
		$this->assertEquals('my sqlstate', $error->getSqlState());

		/* won't throw an error on an emty code or text */	
		$error = $this->adapter->createError('', '');
		$this->assertEquals('', $error->getCode());
		$this->assertEquals('', $error->getMessage());
		$this->assertNull($error->getSqlState());	
	}

	/**
	 * When you create a response with no parameters it represents as 
	 * sucessful query that ran but does not need to send back any resultset
	 *
	 * @return null
	 */
	public function testCreateResponseNullData()
	{
		$response = $this->adapter->createResponse();
		$this->assertInstanceOf($this->responseClass, $response);
		$this->assertTrue($response->getStatus());
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertNull($response->getResultset());
		$this->assertEquals(0, $response->getRowCount());
		$this->assertFalse($response->getCurrentResult());
		$this->assertNull($response->getError());
		
	}

	/**
	 * When you create a response with a valid dataset of an empty array
	 * meaning a query ran and did not find anything 
	 *
	 * @return null
	 */
	public function testCreateResponseValidDatasetEmptyArray()
	{
		$result   = array();
		$response = $this->adapter->createResponse($result);
		$this->assertInstanceOf($this->responseClass, $response);
		$this->assertTrue($response->getStatus());
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertEquals(0, $response->getRowCount());
		$this->assertEquals($result, $response->getResultset());
		$this->assertFalse($response->getCurrentResult());
		$this->assertNull($response->getError());
	}

	/**
	 * When you create a response with a valid dataset of an array with
	 * many items
	 *
	 * @return null
	 */
	public function testCreateResponseValidDataset()
	{
		$result   = array(
			array('id' => 1),
			array('id' => 2)
		);
		$response = $this->adapter->createResponse($result);
		$this->assertInstanceOf($this->responseClass, $response);
		$this->assertTrue($response->getStatus());
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertEquals(2, $response->getRowCount());
		$this->assertEquals($result, $response->getResultset());
		$this->assertEquals(current($result), $response->getCurrentResult());
		$this->assertNull($response->getError());
	}

	/**
	 * When you create a response with an Exception which could be returned
	 * by a stmt or caught and handled
	 *
	 * @return null
	 */
	public function testCreateResponseWithException()
	{
		$result   = new \Exception('this is a error', 99);
		$response = $this->adapter->createResponse($result);
		$this->assertInstanceOf($this->responseClass, $response);
		$this->assertFalse($response->getStatus());
		$this->assertFalse($response->isSuccess());
		$this->assertTrue($response->isError());
		$this->assertEquals(0, $response->getRowCount());
		$this->assertNull($response->getResultset());
		$this->assertFalse($response->getCurrentResult());

		$error = $response->getError();
		$this->assertInstanceOf('Appfuel\Db\DbError', $error);
		$this->assertEquals(99, $error->getCode());
		$this->assertEquals('this is a error', $error->getMessage());
		$this->assertNull($error->getSqlState());	
	}

	/**
	 * When you create a response with a DbErrorInterface which happens mostly
	 * when adapter stmts encounter problems
	 * 
	 * @return null
	 */
	public function testCreateResponseWithDbError()
	{
		$result = $this->adapter->createError(99, 'my-error', 'my-sqlstate');
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\DbErrorInterface', 
			$result
		);
		$this->assertInstanceOf('Appfuel\Db\DbError', $result);
		
		$response = $this->adapter->createResponse($result);
		$this->assertInstanceOf($this->responseClass, $response);
		$this->assertFalse($response->getStatus());
		$this->assertFalse($response->isSuccess());
		$this->assertTrue($response->isError());
		$this->assertEquals(0, $response->getRowCount());
		$this->assertNull($response->getResultset());
		$this->assertFalse($response->getCurrentResult());

		$error = $response->getError();
		$this->assertSame($result, $error);
	}
}
