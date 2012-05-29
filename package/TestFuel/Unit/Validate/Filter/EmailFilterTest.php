<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Validate\Filter;

use StdClass,
	Appfuel\Validate\Filter\EmailFilter;

/**
 * Email filter wraps php email filter var
 */
class EmailFilterTest extends FilterBaseTest
{
	/**
	 * @return	array
	 */
	public function provideValidEmails()
	{
		return array(
			array('rsb@rsb.me'),
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
	 * @return	EmailFilter
	 */
	public function createFilter()
	{
		return new EmailFilter();
	}

	/**
	 * @test
	 * @return null
	 */
	public function filterInterface()
	{
		return parent::filterInterface();
	}

	/**
	 * @test
	 * @dataProvider	provideValidEmails
	 * @return	null
	 */
	public function validEmail($raw)
	{
		$filter = $this->createFilter();
		$this->assertEquals($raw, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideInValidEmails
	 * @return	null
	 */
	public function invalidEmail($raw)
	{
		$filter = $this->createFilter();
		$fail   = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideInValidEmails
	 * @return	null
	 */
	public function invalidEmailUsingDefault($raw)
	{
		$options = $this->createOptions(array('default' => 'rsb@me.com'));
		$filter  = $this->createFilter();
		$filter->setOptions($options);
		$this->assertEquals('rsb@me.com', $filter->filter($raw));
	}
}
