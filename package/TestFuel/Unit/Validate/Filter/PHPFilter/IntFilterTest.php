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
namespace TestFuel\Unit\Validate\Filter\PHPFilter;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary,
	Appfuel\Validate\Filter\PHPFilter\IntFilter;

/**
 * Test the controller's ability to add rules or filters to fields and 
 * validate or sanitize those fields
 */
class IntFilterTest extends BaseTestCase
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
		$this->filter = new IntFilter('int-filter');
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
			'Appfuel\Validate\Filter\FilterInterface',
			$this->filter
		);

		$this->assertInstanceOf(
			'Appfuel\Validate\Filter\ValidateFilter',
			$this->filter
		);
	}
	
	/**
	 * When the integer is valid it will be returned 
	 *
	 * @return null
	 */
	public function estFilterValidIntNoOptions()
	{
		$params = new Dictionary();
		$raw    = 12345;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = -12345;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw = PHP_INT_MAX;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		/* strings that are numbers will pass */
		$raw = "12345";
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(12345, $result);

		$raw = "+12345";
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(12345, $result);

		$raw = "-12345";
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(-12345, $result);

		/* php manual says these should fail but they don't */
		$raw = -0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(-0, $result);

		$raw = +0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(+0, $result);

		/* sign makes no difference on zero */
		$raw = +0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(-0, $result);
	}
	
	/**
	 * @return null
	 */
	public function testFilterNoOptionsInvalidInt()
	{
		$params = new Dictionary();
		$raw    = 'abcd';
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);

		$raw    = array(1,2,3,4);
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);

		$raw    = new StdClass();
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * @return null
	 */
	public function testFilterRangeMinMaxValid()
	{
		$params = new Dictionary(array('min'=>2,'max'=>5));

		$raw    = 2;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 3;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 4;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 5;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 6;
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);

		$raw    = 1;
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);

		$raw    = "5";
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);
	}

	/**
	 * @return null
	 */
	public function testFilterMinNoMax()
	{
		$params = new Dictionary(array('min' => 0));
			
		$raw    = 0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 10;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = PHP_INT_MAX;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = -10;
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);

		$raw    = -0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(0, $result);

		$raw    = -PHP_INT_MAX;
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * @return null
	 */
	public function testFilterMaxNoMin()
	{
		$params = new Dictionary(array('max' => 1));

		$raw    = 0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 1;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = -11;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = -PHP_INT_MAX;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 2;
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * Default will be returned when the filter fails
	 *
	 * @return	null
	 */
	public function testFilterDefault()
	{
		$params = new Dictionary(array('default' => 22));

		$raw    = 'abc';
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(22, $result);

		$params = new Dictionary(array('default' => 22, 'max' => 2));	
		$raw    = 3;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(22, $result);

		$params = new Dictionary(array('default' => 22, 'min' => 2));	
		$raw    = 0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(22, $result);

		$params = new Dictionary(
			array('default' => 22, 'min' => 2, 'max' => 4)
		);	
		$raw    = 8;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(22, $result);
	}

	/**
	 * @return null
	 */
	public function testOptionAllowOctal()
	{
		$params = new Dictionary(array('allow-octal' => true));
		$raw    = 0;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 01;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 02;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 03;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 04;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 05;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 06;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 07;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		/* 
		 * php wont hold an invalid octal. it silently trucates the digits
		 * that are not octal. So we embed the number in a string to get
		 * the full incorrect integer into the filter
		 */
		$raw = '099';	
		$result = $this->filter->filter('099', $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * @return null
	 */
	public function testOptionAllowOctalWithDefault()
	{
		$params = new Dictionary(
			array('allow-octal' => true,'default' => 0755)
		);

		$raw = '0889';
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(0755, $result);

		$raw = 0555;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);
	}

	/**
	 * @return null
	 */
	public function testOptionAllowHex()
	{
		$params = new Dictionary(array('allow-hex' => true));
		$raw    = 0xfff;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 0xABC;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 0x123;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 0xddd;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 0x000;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 0xffffff;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		$raw    = 0x12345;
		$result = $this->filter->filter($raw, $params);
		$this->assertEquals($raw, $result);

		$raw    = 0x2;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);

		/* 
		 * php wont hold an invalid hex. it silently trucates the digits
		 * that are not octal. So we embed the number in a string to get
		 * the full incorrect integer into the filter
		 */
		$raw = '0xjjj';	
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * @return null
	 */
	public function testOptionAllowDefaultWithDefault()
	{
		$params = new Dictionary(
			array('allow-hex' => true,'default' => 0x123)
		);

		$raw = '0889';
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(0x123, $result);

		$raw = '0xjjzz';
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(0x123, $result);

		$raw = 0xfff;
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals(0xfff, $result);
	}
}
