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
	Appfuel\Validate\Filter\FloatFilter;

/**
 * Test the validation of floating point numbers
 */
class FloatFilterTest extends FilterBaseTest
{
	/**
	 * Supplies a list of valid float point numbers. Note: integers are
	 * in the set of valid floats
	 *
	 * @return array
	 */
	public function provideValidFloats()
	{
		return array(
			array(1.2345),
			array(0.23456),
			array(1.2e3),
			array(7E-10),
			array(-7E-10),
			array(0.0),
			array(-0.0),
			array(+0.0),
			array(0),
			array(12343),
			array('1.23'),
			array('0.23'),
			array('1.2e3'),
			array('-1.2e3'),
			array('7E-10'),
			array('-7E-10')
		);
	}

	/**
	 * @return array
	 */
	public function provideInValidFloats()
	{
		return array(
			array('abc'),
			array(array(1,2,3)),
			array(new StdClass()),
			array('1.2.3'),
			array('-1.2.3'),
			array('7E-10.45'),
			array('1.2e3.5')
		);
	}

	/**
	 * @return array
	 */
	public function provideThousands()
	{
		return array(
			array('1,000.123', 1000.123),
			array('10,000.432',10000.432),
			array('10,000',10000),
			array('-10,000', -10000)
		);
	}


	/**
	 * @return	FloatFilter
	 */
	public function createFilter()
	{
		return new FloatFilter();
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
	 * @dataProvider	provideValidFloats
	 * @return null
	 */
	public function filterNoParams($raw)
	{
		$filter = $this->createFilter();
		$this->assertEquals($raw, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidFloats
	 * @return null
	 */
	public function filterInvalidFloatsNoParams($raw)
	{
		$filter = $this->createFilter();
		$fail   = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideThousands
	 * @return null
	 */
	public function filterAllowThousands($raw, $expected)
	{
		$options = $this->createOptions(array('allow-thousands' => true));
		$filter  = $this->createFilter();
		$filter->setOptions($options);
		$this->assertEquals($expected, $filter->filter($raw));
	}

	/**
	 * The decimal-sep allows you to specify a decimal separator other than
	 * the '.'. This is used for continental European-style floating point
	 * numbers
	 *
	 * @test
	 * @depends	filterInterface
	 * @return null
	 */
	public function filterDecimalSeparator(FloatFilter $filter)
	{
		$options = $this->createOptions(array('decimal-sep' => ','));
		$filter->setOptions($options);

		/* filters it back into the decimal separator '.' */
		$raw = '100,45';
		$this->assertEquals(100.45, $filter->filter($raw));

		$options = $this->createOptions(array('decimal-sep' => ':'));
		$filter->clear();
		$filter->setOptions($options);

		$raw = '100:45';
		$this->assertEquals(100.45, $filter->filter($raw));
	
		/* works ok when no separated is given */	
		$raw = '10045';
		$this->assertEquals(10045, $filter->filter($raw));
		
		$filter->clear();
	}

	/**
	 * The filter swaps the thousand separator with the decimal
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterDecimalSepWithAllowThousands(FloatFilter $filter)
	{
		$options = $this->createOptions(array(
			'decimal-sep'		=> ',',
			'allow-thousands'	=> true
		));
		$filter->setOptions($options);

		$raw = '100.789,5';
		$this->assertEquals(100789.5, $filter->filter($raw));

		$options = $this->createOptions(array(
			'decimal-sep'		=> ':',
			'allow-thousands'	=> true
		));
		$filter->setOptions($options);

		$raw = '100,789:5';
		$this->assertEquals(100789.5, $filter->filter($raw, $params));

		$filter->clear();
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterThousandsWithSpaces(FloatFilter $filter)
	{
		$options = $this->createOptions(array('allow-thousands' => true));
		$filter->setOptions($options);

		$fail = $filter->getFailureToken();
		$raw  = '100, 000.00';

		$this->assertEquals($fail, $filter->filter($raw));
	}
}
