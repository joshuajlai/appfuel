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
namespace TestFuel\Test\Error;

use StdClass,
	SplFileInfo,
	Appfuel\Error\AppfuelError,
	TestFuel\TestCase\BaseTestCase;

/**
 * The AppfuelError is a simple value object that holds an error message
 * and optional code. We will be testing the constructor for its ability to
 * set the message and code as well as the getters. We will also test the 
 * ability for the error to exist in the context of a string
 */
class AppfuelErrorTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	AppfuelError
	 */
	protected $error = null;

	/**
	 * @var string
	 */
	protected $text = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->text = "this is my error";
		$this->error = new AppfuelError($this->text);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->error = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Error\ErrorInterface',
			$this->error
		);	
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor()
	{
		$this->assertEquals($this->text, $this->error->getMessage());
		$this->assertNull($this->error->getCode());

		$expected = "Error: {$this->text}";
		$this->assertEquals($expected, $this->error->__toString());
	}

	/**
	 * @return	array
	 */
	public function provideMessages()
	{
		$text  = "<error setting this message unsupported type";
		$array = "$text -(array)>";
		$obj   = "$text -(object)>";
		$int   = "$text -(integer)>";
		$float = "$text -(double)>";
		return array(
			array('', ''),
			array('regular string', 'regular string'),
			array(new SplFileInfo('my/path'), 'my/path'),
			array(" \n\t", ''),
			array(new StdClass(), $obj),
			array(array(), $array),
			array(array(1,2,3), $array),
			array(12345, $int),
			array(12.345, $float),
		);
	}

	/**
	 * A error code can be any scalar value. This represents the set of 
	 * valid and invalid types.
	 *
	 * @return	array
	 */
	public function provideCodes()
	{
		return array(
			array('',		'',		''),
			array("\t\n ",	'',		''),
			array(0,		0,		'[0]'),
			array(1,		1,		'[1]'),
			array(-1,		-1,		'[-1]'),
			array(12345,	12345,	'[12345]'),
			array(1.2345,	1.2345,	'[1.2345]'),
			array('A100',	'A100',	'[A100]'),
			array('more text',	'more text',	'[more text]'),
			array(new StdClass(),	null,		''),
			array(array(1,2,3),		null,		''),
		);
	}

	/**
	 * @dataProvider	provideMessages
	 * @depends			testInterface
	 * @param	string	$input
	 * @param	string	$expected
	 * @return	null
	 */
	public function testConstructorParamMessage($input, $expected)
	{
		$error = new AppfuelError($input);
		$this->assertEquals($expected, $error->getMessage());
		$strText = "Error: $expected";
		$this->assertEquals($strText, $error->__toString());
	}

	/**
	 * @dataProvider	provideCodes
	 * @depends			testInterface
	 * @param	string	$code
	 * @param	string	$expected
	 * @return	null
	 */
	public function testConstructorParamCode($code,$expected, $output)
	{
		$msg   = 'my message';
		$error = new AppfuelError($msg, $code);
		$this->assertEquals($expected, $error->getCode());

		$string = "Error{$output}: $msg";
		$this->assertEquals($string, $error->__toString());
	}


}
