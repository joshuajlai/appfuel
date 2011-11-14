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
namespace TestFuel\Test\Http;

use StdClass,
	Appfuel\Http\HttpHeaderList,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the ability of the header list to manage headers
 */
class HttpHeaderListTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HttpHeaderList
	 */
	protected $list = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->list = new HttpHeaderList();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->list = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Http\HttpHeaderListInterface',
			$this->list
		);
	}
}

