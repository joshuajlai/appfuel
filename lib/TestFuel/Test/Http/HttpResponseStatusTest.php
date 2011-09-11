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
namespace TestFuel\Test\Http;

use StdClass,
	Appfuel\Http\HttpResponseStatus,
	TestFuel\TestCase\BaseTestCase;

/**
 * A value object that handles mapping of default text for a given status
 */
class HttpResponseStatusTest extends BaseTestCase
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
		$this->status = new HttpResponseStatus();
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
			'Appfuel\Framework\Http\HttpResponseStatusInterface',
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
			$status = new HttpResponseStatus($code);
			$this->assertEquals($code, $status->getCode());
			$this->assertEquals($text, $status->getText());
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
		$status = new HttpResponseStatus($code, $text);
		$this->assertEquals($code, $status->getCode());
		$this->assertEquals($text, $status->getText());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIsNotAnInt_EmptyStringFailure()
	{
		$status = new HttpResponseStatus('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIsNotAnInt_NonEmptyStringFailure()
	{
		$status = new HttpResponseStatus('Abc');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIsNotAnInt_ArrayFailure()
	{
		$status = new HttpResponseStatus(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIsNotAnInt_ObjectFailure()
	{
		$status = new HttpResponseStatus(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIsNotAnInt_FloadFailure()
	{
		$status = new HttpResponseStatus(103.233);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIntLessThan100_ZeroFailure()
	{
		$status = new HttpResponseStatus(0);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIntLessThan100_Failure()
	{
		$status = new HttpResponseStatus(99);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIntGreaterThan600_Failure()
	{
		$status = new HttpResponseStatus(700);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeIntEqual600_Failure()
	{
		$status = new HttpResponseStatus(600);
	}


}
