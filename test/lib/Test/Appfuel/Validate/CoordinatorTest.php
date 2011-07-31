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
	Appfuel\Validate\Coordinator;

/**
 * Test the coordinator's ability to move raw and clean data aswell as add error text
 */
class CoordinatorTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Coordinator
	 */
	protected $coord = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->coord = new Coordinator();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->coord);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Validate\CoordinatorInterface',
			$this->coord
		);
	}

}
