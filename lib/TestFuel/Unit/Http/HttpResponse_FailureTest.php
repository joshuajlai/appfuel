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
	SplFileInfo,
	Appfuel\Http\HttpStatus,
	Appfuel\Http\HttpResponse,
	Appfuel\Http\HttpHeaderField,
	TestFuel\TestCase\BaseTestCase;

/**
 * A value object that handles mapping of default text for a given status
 */
class HttpResponse_FailureTest extends BaseTestCase
{
	/**
	 * The protocol version can only be two values 1.0 and 1.1. These values
	 * are strings
	 * 
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorVersionNotCorrect()
	{
		$response = new HttpResponse('data', '1.2');	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorVersionNotCorrectIsAFloat()
	{
		$response = new HttpResponse('data', 1.2);	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorVersionIsObject()
	{
		$response = new HttpResponse('data', new StdClass());	
	}


	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorVersionIsArray()
	{
		$response = new HttpResponse('data', array());	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testConstructorVersionIsEmptyString()
	{
		$response = new HttpResponse('data', '');	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testSetContentObjectDoesNotSupportToString()
	{
		$response = new HttpResponse();
		$response->setContent(new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */ 
	public function testLoadHeaderNotHeaderInterfaceFirstOneValid()
	{
		$header = 'Location: http://www.example.com/';
		$response = new HttpResponse();
		$headers = array($header, new StdClass());
		$response->loadHeaders($headers);
	}
}
