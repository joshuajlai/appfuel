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
namespace TestFuel\Test\Db;

use Appfuel\Db\DbError,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the ability of the db error to handle codes messages and sql state
 */
class ErrorTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Error
	 */
	protected $error = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->errCode  = 1146;
		$this->errText  = 'some table does not exist';
		$this->sqlState = 'HY000';
		$this->error    = new DbError(
			$this->errCode, 
			$this->errText, 
			$this->sqlState
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->error);
	}

	/**
	 * Message as two immutable members code and message that can only be
	 * set in the constructor
	 *
	 * @return null
	 */
	public function testGetCodeMessageSql()
	{
		$this->assertEquals($this->errCode, $this->error->getCode());
		$this->assertEquals($this->errText, $this->error->getMessage());
		$this->assertEquals($this->sqlState, $this->error->getSqlState());
	}

	/**
	 * @return null
	 */
	public function testToStringOutput()
	{
		$expected = 'ERROR 1146 (HY000): some table does not exist';
		$this->expectOutputString($expected);
		
		echo $this->error;
	}

	/**
	 * @return null
	 */
	public function testConstructDefaultValues()
	{
		$error = new DbError(12345);
		$this->assertEquals(12345, $error->getCode());
		$this->assertNull($error->getMessage());
		$this->assertNull($error->getSqlState());
	}

	/**
	 * @return	null
	 */
	public function testDefaultMessage()
	{
		$error = new DbError(12345, null, 'HY000');
		$this->assertEquals(12345, $error->getCode());
		$this->assertNull($error->getMessage());
		$this->assertEquals('HY000', $error->getSqlState());		
	}

	/**
	 * @return	null
	 */
	public function testDefaultSqlState()
	{
		$error = new DbError(12345, 'my message');
		$this->assertEquals(12345, $error->getCode());
		$this->assertEquals('my message', $error->getMessage());
		$this->assertNull($error->getSqlState());	
	}
}
