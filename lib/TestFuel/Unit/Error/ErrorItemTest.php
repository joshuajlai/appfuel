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
namespace TestFuel\Unit\Error;

use StdClass,
	SplFileInfo,
	Appfuel\Error\ErrorItem,
	TestFuel\TestCase\BaseTestCase;

/**
 * Simple value object used to describe an error. Test the message and code
 * can accept scalar value or objects that support __toString.
 */
class ErrorItemTest extends BaseTestCase
{
	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$msg  = 'i am an error';
		$code = 9900; 
		$error = new ErrorItem($msg, $code);	
		$this->assertInstanceOf('Appfuel\Error\ErrorInterface',$error);	
		$this->assertEquals($msg, $error->getMessage());
		$expected = "[$code]: $msg";
		$this->assertEquals($expected, $error->__toString());
	}

	/**
	 * @return	array
	 */
	public function provideInvalidError()
	{
		return array(
			array(null),
			array(new StdClass()),
			array(array(1,2,3)),
		);
	}

	/**
	 * @return	array
	 */
	public function provideValidErrors()
	{
		$err = 'i am an error';
		/* object that supports __toString */
		$obj = new SplFileInfo($err);	
	return array(
			array('',			''),
			array(' ',			''),
			array($err,			$err),
			array(" $err",		$err),
			array(" $err ",		$err),
			array("\t$err",		$err),
			array("\t$err\n ",	$err),
			array($obj,			$err),
			array(12345,		12345),
			array(1.2345,		1.2345),
			array(0,			0),
			array(-1,			-1),
			array(true,			 '1'),
			array(false,		 null),
		);
	}

	/**
	 * @depends			testInterface
	 * @dataProvider	provideValidErrors
	 * @return			null
	 */
	public function testValidErrorsMessages($msg, $expected)
	{
		$error = new ErrorItem($msg);
		$this->assertEquals($expected, $error->getMessage());
		$this->assertNull($error->getCode());
		$this->assertEquals($expected, $error->__toString());
	}

	/**
	 * Since message and code share the same validation rules we will reuse
	 * message for code and test both togather with the same data provider
	 *
	 * @depends			testInterface
	 * @dataProvider	provideValidErrors
	 * @return			null
	 */
	public function testValidErrorsMessagesCode($msg, $expected)
	{
		$error = new ErrorItem($msg, $msg);
		$this->assertEquals($expected, $error->getMessage());
		$this->assertEquals($expected, $error->getCode());

		$str = $expected;
		if (strlen($expected) > 0) {
			$str = "[$expected]: $expected";
		}
		$this->assertEquals($str, $error->__toString());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidError
	 * @return				null
	 */
	public function testErrorsMessages_Failures($msg)
	{
		$error = new ErrorItem($msg);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testErrorsCodes_ObjectFailures()
	{
		$error = new ErrorItem('valid message', new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testErrorsCodes_ArrayFailures()
	{
		$error = new ErrorItem('valid message', array(1,2,3));
	}
}
