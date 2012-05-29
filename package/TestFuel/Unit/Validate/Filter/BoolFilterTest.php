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
	Appfuel\Validate\Filter\BoolFilter;

/**
 * Test bool filter which wraps php filter var
 */
class BoolFilterTest extends FilterBaseTest
{

	/**
	 * @return	BoolFilter
	 */
	public function createFilter()
	{
		return new BoolFilter();
	}

	/**
	 * @return	array
	 */
	public function provideTrueValues()
	{
		return array(
			array(true),
			array(1),
			array('1'),
			array('yes'),
			array('YES'),
			array('Yes'),
			array('on'),
			array('ON'),
			array('On'),
			array('true'),
			array('TRUE'),
			array('True'),
		);
	}

	/**
	 * @return	array
	 */
	public function provideFalseValues()
	{
		return array(
			array(false),
			array('false'),
			array('FALSE'),
			array('False'),
			array(0),
			array('0'),
			array('off'),
			array('OFF'),
			array('Off'),
		);
	}

	/**
	 * @return	array
	 */
	public function provideNonTrueValues()
	{
		return array(
			array(false),
			array(null),
			array(0),
			array('0'),
			array('false'),
			array('off'),
			array(''),
			array(1234),
			array('abc'),
			array(new StdClass()),
			array(array(1,3,4))
		);
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
	 * True is considered any of these values:
	 * true, 1, '1', 'yes', 'on', 'true'
	 *
	 * @test
	 * @dataProvider	provideTrueValues
	 * @return			null
	 */
	public function filterWithOnlyTrue($raw)
	{
		$filter = $this->createFilter();
		$this->assertTrue($filter->filter($raw));
	}

	/**
	 * With no params anything that is not true is false
	 *
	 * @test
	 * @dataProvider	provideNonTrueValues
	 * @return	null
	 */
	public function filterWithNonTrueValues($raw)
	{
		$filter = $this->createFilter();
		$this->assertFalse($filter->filter($raw));
	}

	/**
	 * Make sure truth values work the same with the strict params
	 *
	 * @test
	 * @dataProvider	provideTrueValues
	 * @return null
	 */
	public function filterTrueWithStrictTrue($raw)
	{
		$options = $this->createOptions(array('strict' => true));
		$filter = $this->createFilter();
		$filter->setOptions($options);
	
		$this->assertTrue($filter->filter($raw));
	}

	/**
	 * With strict mode false will be returned only for specific false values
	 *
	 * @test
	 * @dataProvider	provideFalseValues
	 * @return null
	 */
	public function filterFalseWithStrict($raw)
	{
		$options = $this->createOptions(array('strict' => true));
		$filter  = $this->createFilter();
		$filter->setOptions($options);

		$this->assertFalse($filter->filter($raw));
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterCustomMap(BoolFilter $filter)
	{
		$options = $this->createOptions(array(
			'map' => array(
				'true' => array('first-truth', 'second-truth', 'other-truth'),
			),
		));
		$filter->setOptions($options);
		$this->assertTrue($filter->filter('first-truth'));
		$this->assertTrue($filter->filter('second-truth'));
		$this->assertTrue($filter->filter('other-truth'));
		$this->assertFalse($filter->filter('some-other-value'));
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterCustomMapStrict(BoolFilter $filter)
	{
		$options = $this->createOptions(array(
			'strict' => true,
			'map' => array(
				'true'  => array('first-truth', 'second-truth', 'other-truth'),
				'false' => array('my-false', 'your-false', false)
			),
		));
		$filter->setOptions($options);

		$this->assertTrue($filter->filter('first-truth'));
		$this->assertTrue($filter->filter('second-truth'));
		$this->assertTrue($filter->filter('other-truth'));

		$this->assertFalse($filter->filter('my-false'));
		$this->assertFalse($filter->filter('your-false'));
		$this->assertFalse($filter->filter(false));
	
		$fail = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter('some-other-value'));
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterStrictCustomMapTrueNotMapped(BoolFilter $filter)
	{
		$options = $this->createOptions(array(
			'strict' => true,
			'map' => array(
				'wrong-true-key'  => array('first-truth', 'second-truth'),
			),
		));
		$filter->setOptions($options);

		$fail = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter('first-truth'));
		$this->assertEquals($fail, $filter->filter('second-truth'));
		$this->assertEquals($fail, $filter->filter('some-other-value'));
	}

	/**
	 * No false values will match because no false map was found
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function filterStrictCustomMapFalseNotMapped(BoolFilter $filter)
	{
		$options = $this->createOptions(array(
			'strict' => true,
			'map' => array(
				'true'  => array('first-truth', 'second-truth'),
				'worng-false-key' => array('my-false', 'your-false')
			),
		));
		$filter->setOptions($options);

		$fail = $filter->getFailureToken();
		$this->assertTrue($filter->filter('first-truth'));
		$this->assertTrue($filter->filter('second-truth'));
		$this->assertEquals($fail, $filter->filter('my-false'));
		$this->assertEquals($fail, $filter->filter('your-false'));
		$this->assertEquals($fail, $filter->filter('some-other-value'));
	}
}
