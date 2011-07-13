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

use Test\DbCase as ParentTestCase,
	Appfuel\Db\Handler;

/**
 */
class HandlerTest extends ParentTestCase
{
	protected $handle = null;

	/**
	 * Save the current state of the Pool
	 */
	public function setUp()
	{
		$this->handle = new Handler();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->handler);
	}

	public function testOne()
	{
		$this->assertTrue(true);
	}
}
