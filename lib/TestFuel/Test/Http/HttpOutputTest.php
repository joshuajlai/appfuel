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
	Appfuel\Http\HttpOutput,
	Appfuel\Http\HttpResponse,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HttpOutputTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HttpOutput
	 */
	protected $output = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->output = new HttpOutput();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->output = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\OutputInterface',
			$this->output
		);
	}
}
