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
	Appfuel\Http\HttpResponse,
	Appfuel\Http\HttpOutputAdapter,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HttpOutputAdapterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HttpOutputAdapter
	 */
	protected $adapter = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->adapter = new HttpOutputAdapter();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->adapter = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Output\OutputAdapterInterface',
			$this->adapter
		);
	}
}
