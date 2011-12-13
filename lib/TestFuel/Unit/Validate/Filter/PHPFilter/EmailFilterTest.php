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
namespace TestFuel\Test\Validate\Filter\PHPFilter;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Validate\Filter\PHPFilter\EmailFilter;

/**
 * Email filter wraps php email filter var
 */
class EmailFilterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var IntFilter
	 */
	protected $filter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->filter = new EmailFilter('php-email-filter');
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->filter);
	}

	/**
	 * @return	array
	 */
	public function provideValidEmails()
	{
		return array(
			array('rsb@wiredrive.com'),
			array('rsb.code@gmail.com'),
			array('some.other.long.name@name.net'),
			array('my-email@name.org'),
			array('my-email@name.edu.ca'),
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidEmails()
	{
		return array(
			array('rsb@'),
			array('@gmail.com'),
			array(''),
			array('12345'),
			array('rsb.code@gmail.com@gmail.net'),
			array('rsb#wiredrive.com')
		);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Validate\Filter\FilterInterface',
			$this->filter
		);

		$this->assertInstanceOf(
			'Appfuel\Validate\Filter\ValidateFilter',
			$this->filter
		);
	}

	/**
	 * @depends			testInterfaces
	 * @dataProvider	provideValidEmails
	 * @return	null
	 */
	public function testValidEmail($raw)
	{
		$params = new Dictionary();
		$this->assertEquals($raw, $this->filter->filter($raw, $params));
		$this->assertFalse($this->filter->isFailure());
	}

	/**
	 * @depends			testValidEmail
	 * @dataProvider	provideInValidEmails
	 * @return	null
	 */
	public function testInvalidEmail($raw)
	{
		$params = new Dictionary();
		$this->assertNull(
			$this->filter->filter($raw, $params)
		);
		
		$this->assertTrue($this->filter->isFailure());

	}

	/**
	 * @depends			testInvalidEmail
	 * @dataProvider	provideInValidEmails
	 * @return	null
	 */
	public function testInvalidEmailUsingDefault($raw)
	{
		$params = new Dictionary(array('default' => 'rsb@me.com'));
		$this->assertEquals(
			'rsb@me.com', 
			$this->filter->filter($raw, $params)
		);
		$this->assertFalse($this->filter->isFailure());
	}
}
