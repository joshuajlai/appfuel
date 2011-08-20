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
namespace Test\Appfuel\Validate\Filter;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Validate\Filter\PHPFilter\FloatFilter,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * Test the validation of floating point numbers
 */
class FloatFilterTest extends ParentTestCase
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
		$this->filter = new FloatFilter('float-filter');
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->filter);
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
	 * @depends			testInterfaces
	 * @dataProvider	provideValidFloats
	 * @return null
	 */
	public function testFilterNoParams($raw)
	{
		$params = new Dictionary();
		$result = $this->filter->filter($raw, $params);
		$this->assertEquals($raw, $result);
		$this->assertFalse($this->filter->isFailure());
	}

	/**
	 * @depends			testInterfaces
	 * @dataProvider	provideInvalidFloats
	 * @return null
	 */
	public function testFilterInvalidFloatsNoParams($raw)
	{
		$params = new Dictionary();
		$result = $this->filter->filter($raw, $params);
		$this->assertNull($result);
		$this->assertTrue($this->filter->isFailure());
	}

	/**
	 * @depends			testInterfaces
	 * @dataProvider	provideThousands
	 * @return null
	 */
	public function testFilterAllowThousands($raw, $final)
	{
		$params = new Dictionary(array('allow-thousands' => true));
		$result = $this->filter->filter($raw, $params);
		$this->assertEquals($final, $result);
		$this->assertFalse($this->filter->isFailure());
	}

	/**
	 * The decimal-sep allows you to specify a decimal separator other than
	 * the '.'. This is used for continental European-style floating point
	 * numbers
	 *
	 * @return null
	 */
	public function testFilterDecimalSeparator()
	{
		$params = new Dictionary(array('decimal-sep' => ','));
		$raw = '100,45';
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		
		/* filters it back into the decimal separator '.' */
		$this->assertEquals('100.45', $result);

		$params = new Dictionary(array('decimal-sep' => ':'));
		$raw = '100:45';
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals('100.45', $result);
	
		/* works ok when no separated is given */	
		$raw = '10045';
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals('10045', $result);
	}

	/**
	 * The filter swaps the thousand separator with the decimal
	 * @return null
	 */
	public function testFilterDecimalSeparatorWithAllowThousands()
	{
		$params = array(
			'decimal-sep'		=> ',',
			'allow-thousands'	=> true
		);
		$params = new Dictionary($params);

		$raw = '100.789,5';
		$result = $this->filter->filter($raw, $params);
		/* filters it back into the decimal separator '.' */
		$this->assertEquals('100789.5', $result);

		$params = array(
			'decimal-sep'		=> ':',
			'allow-thousands'	=> true
		);
		$params = new Dictionary($params);

		$raw = '100,789:5';
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		/* filters it back into the decimal separator '.' */
		$this->assertEquals('100789.5', $result);
	}

	/**
	 * @return	null
	 */
	public function testFilterThousandsWithSpaces()
	{
		$params = new Dictionary(array('allow-thousands' => true));
		$raw    = '100, 000.00';
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result); 	
	}
}
