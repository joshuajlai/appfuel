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
namespace Test\Appfuel\Framework\App;

use PHPUnit_Framework_TestCase		as ParentTestCase,
	Appfuel\Framework\App\Factory	as AppFactory;

/**
 * 
 */
class InitializerTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Framework\App\Factory
	 */
	protected $factory = NULL;

	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->basePath = AF_BASE_PATH;
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
	}

	public function testInit()
	{
		$this->assertTrue(TRUE);
	}

}

