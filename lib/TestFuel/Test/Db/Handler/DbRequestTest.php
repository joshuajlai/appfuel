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
namespace TestFuel\Test\Db\Request;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Handler\DbRequest;

/**
 * The query request carries information for the handler and the handler's
 * adapter. 
 */
class DbRequestTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	DbRequest
	 */
	protected $request = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->request = new DbRequest();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->request = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Handler\DbRequestInterface',
			$this->request
		);
	}
}
