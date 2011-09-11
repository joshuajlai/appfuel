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
	Appfuel\Http\HttpResponseStatus,
	TestFuel\TestCase\BaseTestCase;

/**
 * A value object that handles mapping of default text for a given status
 */
class HttpResponseStatusTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HttpResponseStatus
	 */
	protected $status = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->status = new HttpResponseStatus();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->status = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Http\HttpResponseStatusInterface',
			$this->status
		);
	}
}
