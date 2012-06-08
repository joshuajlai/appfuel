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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\RouteInputValidation;

class RouteInputValidationTest extends BaseTestCase
{
	/**
	 * @return RouteInputValidation
	 */
	public function createRouteInputValidation()
	{
		return new RouteInputValidation();
	}

	/**
	 * @test
	 * @return	RouteInputValidation
	 */
	public function routeInterface()
	{
		$input = $this->createRouteInputValidation();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RouteInputValidationInterface',
			$input
		);

		return $input;
	}

	/**
	 * @test
	 * @depends	routeInterface
	 * @return	RouteInputValidation
	 */
	public function isInputValidation(RouteInputValidation $input)
	{
		$this->assertTrue($input->isInputValidation());

		$this->assertSame($input, $input->ignoreInputValidation());
		$this->assertFalse($input->isInputValidation());

		$this->assertSame($input, $input->validateInput());
		$this->assertTrue($input->isInputValidation());

		return $input;
	}

	/**
	 * @test
	 * @depends	routeInterface
	 * @return	RouteInputValidation
	 */
	public function isThrowOnFailure(RouteInputValidation $input)
	{
		$this->assertTrue($input->isThrowOnFailure());

		$this->assertSame($input, $input->ignoreValidationFailure());
		$this->assertFalse($input->isThrowOnFailure());

		$this->assertSame($input, $input->throwExceptionOnFailure());
		$this->assertTrue($input->isThrowOnFailure());

		return $input;
	}

	/**
	 * @test
	 * @depends	routeInterface
	 * @return	RouteInputValidation
	 */
	public function errorCode(RouteInputValidation $input)
	{
		$this->assertEquals(500, $input->getErrorCode());
		
		$this->assertSame($input, $input->setErrorCode(404));
		$this->assertEquals(404, $input->getErrorCode());

		$this->assertSame($input, $input->setErrorCode('A100'));
		$this->assertEquals('A100', $input->getErrorCode());

		$this->assertSame($input, $input->setErrorCode(''));
		$this->assertEquals('', $input->getErrorCode());

		$this->assertSame($input, $input->setErrorCode(null));
		$this->assertNull($input->getErrorCode());

		/* restore to default */
		$input->setErrorCode(500);

		return $input;
	}

	/**
	 * @test
	 * @depends	routeInterface
	 * @return	RouteInputValidation
	 */
	public function errorCodeNotScalar(RouteInputValidation $input)
	{
		$msg = 'error code must be a scalar value or null';
		$this->setExpectedException('DomainException', $msg);
		$input->setErrorCode(array(1,2,3));
	}

	/**
	 * @test
	 * @depends	routeInterface
	 * @return	RouteInputValidation
	 */
	public function specList(RouteInputValidation $input)
	{
		$this->assertEquals(array(), $input->getSpecList());
	
		$list = array(
			array(
				'field'    => 'my-field', 
				'location' => 'get',
				'filters'  => array()
			)
		);
		$this->assertSame($input, $input->setSpecList($list));
		$this->assertEquals($list, $input->getSpecList($list));
		
		$this->assertSame($input, $input->setSpecList(array()));
		$this->assertEquals(array(), $input->getSpecList($list));
			
		return $input;
	}
}
