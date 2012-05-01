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
namespace TestFuel\Unit\Http;

use StdClass,
	Appfuel\Http\HttpStatus,
	TestFuel\TestCase\BaseTestCase;

/**
 * A value object that handles mapping of default text for a given status
 */
class HttpStatusTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HttpResponseStatus
	 */
	protected $status = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->status = new HttpStatus();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->status = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Http\HttpStatusInterface',
			$this->status
		);
	}

	/**
	 * Test that all codes are between 100 and 600 and all values and non
	 * empty strings
	 *
	 * @return	array
	 */
	public function testStatusMap()
	{
		$map = $this->status->getStatusMap();
		$this->assertInternalType('array', $map);
		$this->assertNotEmpty($map);

		foreach ($map as $code => $text) {
			$this->assertGreaterThanOrEqual(100, $code);
			$this->assertLessThan(600, $code);

			$this->assertNotEmpty($text);
			$this->assertInternalType('string', $text);
		}
	}

	/**
	 * Setup instantiated a new HttpResponseStatus with no paramaters.
	 * This should default with code 200 and text 'ok'
	 *
	 * @return	null
	 */
	public function testDefaults()
	{
		$this->assertEquals(200, $this->status->getCode());
		$this->assertEquals('OK',$this->status->getText());
		$this->assertEquals("200 OK", $this->status->__toString());
	}

	/**
	 * In this test we will get the status map and create new status objects
	 * with only the code and test that the correct text was set
	 *
	 * @depends	testStatusMap
	 * @return	null
	 */
	public function testAllStatusMapCodes()
	{
		$map = $this->status->getStatusMap();
		foreach ($map as $code => $text) {
			$status = new HttpStatus($code);
			$this->assertEquals($code, $status->getCode());
			$this->assertEquals($text, $status->getText());
			$this->assertEquals("$code $text", $status->__toString());
		}
	}

	/**
	 * This will test the ability to manually supply you own text for the 
	 * code
	 * 
	 * @return	null
	 */
	public function testManualCodeOverride()
	{
		$code = 200;
		$text = 'my own text';
		$status = new HttpStatus($code, $text);
		$this->assertEquals($code, $status->getCode());
		$this->assertEquals($text, $status->getText());
		$this->assertEquals("$code $text", $status->__toString());
	}

	/**
	 * @expectedException InvalidArgumentException	
	 * @return	null
	 */
	public function testCodeIsNotAnInt_EmptyStringFailure()
	{
		$status = new HttpStatus('');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testCodeIsNotAnInt_NonEmptyStringFailure()
	{
		$status = new HttpStatus('Abc');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testCodeIsNotAnInt_ArrayFailure()
	{
		$status = new HttpStatus(array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testCodeIsNotAnInt_ObjectFailure()
	{
		$status = new HttpStatus(new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testCodeIntLessThan100_ZeroFailure()
	{
		$status = new HttpStatus(0);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testCodeIntLessThan100_Failure()
	{
		$status = new HttpStatus(99);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testCodeIntGreaterThan600_Failure()
	{
		$status = new HttpStatus(700);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testCodeIntEqual600_Failure()
	{
		$status = new HttpStatus(600);
	}
}
