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
namespace TestFuel\Test\Error;

use StdClass,
	Appfuel\Error\AppfuelError,
	TestFuel\TestCase\BaseTestCase;

/**
 * The AppfuelError is a simple value object that holds an error message
 * and optional code. We will be testing the constructor for its ability to
 * set the message and code as well as the getters. We will also test the 
 * ability for the error to exist in the context of a string
 */
class AppfuelErrorTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	AppfuelError
	 */
	protected $error = null;

	/**
	 * @var string
	 */
	protected $text = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->text = "this is my error";
		$this->error = new AppfuelError($this->text);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->error = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Error\ErrorInterface',
			$this->error
		);	
	}
}
