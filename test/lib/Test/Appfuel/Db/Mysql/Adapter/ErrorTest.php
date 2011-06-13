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
namespace Test\Appfuel\Db\Adapter;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Mysql\Adapter\Error;

/**
 * Test the adapters ability to wrap mysqli
 */
class ErrorTest extends ParentTestCase
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
		$this->errText = 'some table does not exist';
		$this->error   = new Error($this->errCode, $this->errText);
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
	public function testGetCodeMessage()
	{
		$this->assertEquals($this->errCode, $this->error->getCode());
		$this->assertEquals($this->errText, $this->error->getMessage());
	}

	/**
	 * When no message is given the default is an empty string
	 * 
	 * @return	null
	 */
	public function testDefaultMessage()
	{
		$error = new Error($this->errCode);
		$this->assertEquals('', $error->getMessage());
	}
}
