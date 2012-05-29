<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Validate\Filter;

use StdClass,
	Appfuel\Validate\Filter\IntFilter;

/**
 * Test the controller's ability to add rules or filters to fields and 
 * validate or sanitize those fields
 */
class IntFilterTest extends FilterBaseTest
{
	/**
	 * @return	IntFilter
	 */
	public function createFilter()
	{
		return new IntFilter();
	}

	/**
	 * @return array
	 */
	public function provideValidIntegers()
	{
		return array(
			array(12345),
			array(-12345),
			array(+12345),
			array(0),
			array(+0),
			array(-0),
			array(PHP_INT_MAX),
		);
	}

	/**
	 * @return array
	 */
	public function provideCastableIntegers()
	{
		return array(
			array("12345", 12345),
			array("-12345", -12345),
			array("+12345", 12345),
			array("0", 0),
			array("1", 1),
			array(true, 1),
		);
	}


	/**
	 * @return array
	 */
	public function provideInvalidIntegers()
	{
		return array(
			array('abcdef'),
			array(array(1,2,3,4)),
			array(new StdClass()),
			array(false),
		);
	}

	/**
	 * @test
	 * @return	IntFilter
	 */
	public function filterInterface()
	{
		return parent::filterInterface();
	}
	
	/**
	 * @test
	 * @dataProvider	provideValidIntegers
	 * @return null
	 */
	public function filterWithNoOptions($int)
	{
		$filter = $this->createFilter();
		$result = $filter->filter($int);
		$this->assertEquals($int, $result);
	}
	
	/**
	 * @test
	 * @dataProvider	provideInvalidIntegers
	 * @return null
	 */
	public function filterInvalidWithNoOptions($int)
	{
		$filter = $this->createFilter();
		$result = $filter->filter($int);
		$this->assertEquals($filter->getFailureToken(), $result);
	}
	
	/**
	 * @test
	 * @dataProvider	provideCastableIntegers
	 * @return null
	 */
	public function filterCastableWithNoOptions($raw, $expected)
	{
		$filter = $this->createFilter();
		$result = $filter->filter($raw);
		$this->assertEquals($expected, $result);
	}
	
	/**
	 * @test
	 * @depends	filterInterface
	 * @return null
	 */
	public function filterRangeMin(IntFilter $filter)
	{
		$opts = $this->createOptions(array('min'=>2));
		$filter->setOptions($opts);

		$this->assertEquals(2, $filter->filter(2));
		$this->assertEquals(3, $filter->filter(3));
		$this->assertEquals(300, $filter->filter(300));
		$this->assertEquals(PHP_INT_MAX, $filter->filter(PHP_INT_MAX));

		$this->assertEquals($filter->getFailureToken(), $filter->filter(1));
		$this->assertEquals($filter->getFailureToken(), $filter->filter(0));
		$this->assertEquals($filter->getFailureToken(), $filter->filter(-1000));
	
		$filter->clear();
		return $filter;	
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return null
	 */
	public function filterRangeMax(IntFilter $filter)
	{
		$opts = $this->createOptions(array('max' => 10));
		$filter->setOptions($opts);

		$this->assertEquals(0, $filter->filter(0));
		$this->assertEquals(-200, $filter->filter(-200));
		$this->assertEquals(8, $filter->filter(8));
		$this->assertEquals(10, $filter->filter(10));


		$this->assertEquals($filter->getFailureToken(), $filter->filter(11));
		$this->assertEquals($filter->getFailureToken(), $filter->filter(100));
		$this->assertEquals($filter->getFailureToken(), $filter->filter(1000));
	
		$filter->clear();
		return $filter;	
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return null
	 */
	public function filterRangeMinMax(IntFilter $filter)
	{
		$opts = $this->createOptions(array('min' => 8, 'max' => 10));
		$filter->setOptions($opts);

		$this->assertEquals(8, $filter->filter(8));
		$this->assertEquals(9, $filter->filter(9));
		$this->assertEquals(10, $filter->filter(10));


		$this->assertEquals($filter->getFailureToken(), $filter->filter(11));
		$this->assertEquals($filter->getFailureToken(), $filter->filter(7));
		$this->assertEquals($filter->getFailureToken(), $filter->filter(0));
	
		$filter->clear();
		return $filter;	
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterWithDefault(IntFilter $filter)
	{
		$default = 22;
		$opts = $this->createOptions(array(
			'default' => $default,
			'min'     => 22,
			'max'	  => 24,
		));
		$filter->setOptions($opts);

		/* valid int but out of range will result in default */
		$this->assertEquals($default, $filter->filter(12));
		$this->assertEquals($default, $filter->filter(26));

		$this->assertEquals($default, $filter->filter('abcd'));	
		$this->assertEquals($default, $filter->filter(array('a', 'b', 'c')));	
		$this->assertEquals($default, $filter->filter(new StdClass()));

		$this->assertEquals(22, $filter->filter(22));	
		$this->assertEquals(23, $filter->filter(23));	
		$this->assertEquals(24, $filter->filter(24));	
		$filter->clear();
		return $filter;	
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterWithIn(IntFilter $filter)
	{
		$opts = $this->createOptions(array(
			'in' => array(99, 44, 33),
		));
		$filter->setOptions($opts);

		$fail = $filter->getFailureToken();
		/* valid int but out of range will result in default */
		$this->assertEquals($fail, $filter->filter(12));
		$this->assertEquals($fail, $filter->filter(26));

		$this->assertEquals($fail, $filter->filter('abcd'));	
		$this->assertEquals($fail, $filter->filter(array('a', 'b', 'c')));	
		$this->assertEquals($fail, $filter->filter(new StdClass()));

		$this->assertEquals(99, $filter->filter(99));	
		$this->assertEquals(44, $filter->filter(44));	
		$this->assertEquals(33, $filter->filter(33));	

		$filter->clear();
		return $filter;	
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterWithNotIn(IntFilter $filter)
	{
		$opts = $this->createOptions(array(
			'not-in' => array(99, 44, 33),
		));
		$filter->setOptions($opts);

		$fail = $filter->getFailureToken();
		/* valid int but out of range will result in default */
		$this->assertEquals(12, $filter->filter(12));
		$this->assertEquals(26, $filter->filter(26));
		$this->assertEquals(77, $filter->filter('77'));

		$this->assertEquals($fail, $filter->filter('abcd'));	
		$this->assertEquals($fail, $filter->filter(array('a', 'b', 'c')));	
		$this->assertEquals($fail, $filter->filter(new StdClass()));

		$this->assertEquals($fail, $filter->filter(99));	
		$this->assertEquals($fail, $filter->filter(44));	
		$this->assertEquals($fail, $filter->filter(33));	

		$filter->clear();
		return $filter;	
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterAllowOctal(IntFilter $filter)
	{
		$options = $this->createOptions(array('allow-octal' => true));
		$filter->setOptions($options);

		$this->assertEquals(0,  $filter->filter(0));
		$this->assertEquals(01, $filter->filter(01));
		$this->assertEquals(02, $filter->filter(02));
		$this->assertEquals(03, $filter->filter(03));
		$this->assertEquals(04, $filter->filter(04));
		$this->assertEquals(05, $filter->filter(05));
		$this->assertEquals(06, $filter->filter(06));
		$this->assertEquals(07, $filter->filter(07));
		$this->assertEquals(0755, $filter->filter(0755));

		/* 
		 * php wont hold an invalid octal. it silently trucates the digits
		 * that are not octal. So we embed the number in a string to get
		 * the full incorrect integer into the filter
		 */
		$fail = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter('099'));
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterAllowHex(IntFilter $filter)
	{
		$options = $this->createOptions(array('allow-hex' => true));
		$filter->setOptions($options);
		
		$this->assertEquals(0xfff, $filter->filter(0xfff));
		$this->assertEquals(0xABC, $filter->filter(0xabc));
		$this->assertEquals(0x123, $filter->filter(0x123));
		$this->assertEquals(0xddd, $filter->filter(0xddd));
		$this->assertEquals(0x000, $filter->filter(0x000));
		$this->assertEquals(0xffffff, $filter->filter(0xffffff));
		$this->assertEquals(0x12345, $filter->filter(0x12345));
		$this->assertEquals(0x2, $filter->filter(0x2));

		/* 
		 * php wont hold an invalid hex. it silently trucates the digits
		 * that are not octal. So we embed the number in a string to get
		 * the full incorrect integer into the filter
		 */
		$raw = '0xjjj';	
		$fail = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter($raw));
	}
}
