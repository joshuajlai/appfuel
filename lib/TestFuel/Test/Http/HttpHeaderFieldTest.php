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
	Appfuel\Http\HttpHeaderField,
	TestFuel\TestCase\BaseTestCase;

/**
 * The header field is a value object to hold the info for use the php
 * header function.
 */
class HttpHeaderFieldTest extends BaseTestCase
{
	/**
	 * Test use case of just setting the field text. Also ensure the correct
	 * interface is being used.
	 *
	 * @return null
	 */
	public function testInterfaceAndDefaults()
	{
		$text  = 'Location: http://www.example.com/';
		$field = new HttpHeaderField($text);
		$this->assertInstanceOf(
			'Appfuel\Framework\Http\HttpHeaderFieldInterface',
			$field
		);

		$this->assertEquals($text, $field->getField());
		
		/* defaults */
		$this->assertTrue($field->isReplace());
		$this->assertNull($field->getCode());
	}

	/**
	 * @depends	testInterfaceAndDefaults
	 * @return	null
	 */
	public function testReplaceFalse()
	{
		$text  = 'HTTP/1.0 404 Not Found';
		$field = new HttpHeaderField($text, false);
		$this->assertFalse($field->isReplace());

		/* these will always be true because thats the default */
		$field = new HttpHeaderField($text, 0);
		$this->assertTrue($field->isReplace(), '0 not a valid false');

		$field = new HttpHeaderField($text, 'false');
		$this->assertTrue($field->isReplace(), 'not a valid false');

		$field = new HttpHeaderField($text, -1);
		$this->assertTrue($field->isReplace(), 'not a valid false');

		$field = new HttpHeaderField($text, null);
		$this->assertTrue($field->isReplace(), 'not a valid false');

		$field = new HttpHeaderField($text, '');
		$this->assertTrue($field->isReplace(), 'not a valid false');
	}

	/**
	 * @depends	testInterfaceAndDefaults
	 * @return	null
	 */
	public function testValidCode()
	{
		$text      = 'HTTP/1.0 404 Not Found';
		$field = new HttpHeaderField($text, null, 100);
		$this->assertEquals(100, $field->getCode());

		$field = new HttpHeaderField($text, null, 200);
		$this->assertEquals(200, $field->getCode());

		$field = new HttpHeaderField($text, null, 300);
		$this->assertEquals(300, $field->getCode());

		$field = new HttpHeaderField($text, null, 400);
		$this->assertEquals(400, $field->getCode());

		$field = new HttpHeaderField($text, null, 500);
		$this->assertEquals(500, $field->getCode());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testTextNull_Failure()
	{
		$field = new HttpHeaderField(null);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testTextEmptyString_Failure()
	{
		$field = new HttpHeaderField('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testTextArray_Failure()
	{
		$field = new HttpHeaderField(array(1,2,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testTextInt_Failure()
	{
		$field = new HttpHeaderField(500);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testTextObject_Failure()
	{
		$field = new HttpHeaderField(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeLessThan0_Failure()
	{
		$field = new HttpHeaderField('field', null, -22);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeArray_Failure()
	{
		$field = new HttpHeaderField('field', null, array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeObject_Failure()
	{
		$field = new HttpHeaderField('field', null, new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeEmptyString_Failure()
	{
		$field = new HttpHeaderField('field', null, '');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeString_Failure()
	{
		$field = new HttpHeaderField('field', null, 'abc');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testCodeStringIsNumber_Failure()
	{
		$field = new HttpHeaderField('field', null, '22');
	}






}
