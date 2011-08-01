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
namespace Test\Appfuel\View;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Validate\Error;

/**
 * Test the errors ability to hold, display and retrieve errors for a single
 * field
 */
class ErrorTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Coordinator
	 */
	protected $error = null;

	/**
	 * The field these errors belong to
	 * @var string
	 */
	protected $field = null;

	/**
	 * First message added in the constructor
	 * @var string
	 */
	protected $firstMsg = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->field = "my-field";
		$this->firstMsg = "this is the first message";
		$this->error = new Error($this->field, $this->firstMsg);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->error);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Validate\ErrorInterface',
			$this->error
		);
		$this->assertInstanceOf('Countable',$this->error);
		$this->assertInstanceOf('Iterator',$this->error);
	}
}
