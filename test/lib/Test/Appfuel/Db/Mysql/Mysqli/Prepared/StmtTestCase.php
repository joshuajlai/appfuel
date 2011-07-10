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
namespace Test\Appfuel\Db\Mysql\Mysqli\Prepared;

use Test\DbTestCase as ParentTestCase,
	Appfuel\Db\Mysql\Mysqli\Connection,
	Appfuel\Db\Mysql\Mysqli\Prepared\Stmt,
	mysqli as mysqli_handle,
	mysqli_stmt,
	mysqli_result;

/**
 * Hold common setUp and tearDown functionality
 */
class StmtTestCase extends ParentTestCase
{
	/**
	 * System under test
	 * @var Server
	 */
	protected $stmt = null;

	/**
	 * Hold the connection
	 * @var Connection
	 */
	protected $conn = null;
	
	/**
	 * @var mysqli
	 */
	protected $handle = null;

	/**
	 * Mysqli stmt resource handle 
	 * @var mysqli_stmt
	 */
	protected $stmtHandle = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
        $this->handle  = mysqli_init();
        $this->conn = new Connection($this->getConnDetail(), $this->handle);
        $this->assertTrue($this->conn->connect());

		$this->stmtHandle = $this->handle->stmt_init();
        $this->stmt = new Stmt($this->stmtHandle);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->assertTrue($this->conn->close());
		unset($this->conn);
		unset($this->handle);
		unset($this->stmtHandle);
		unset($this->stmt);
		
	}
}
