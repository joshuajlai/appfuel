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
namespace Testfuel\Unit\Validate;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Validate\SingleFieldValidator;

class SingleFieldValidatorTest extends BaseTestCase
{
	/**
	 * @param	array	$data
	 * @return	SingleFieldValidator
	 */
	public function createSingleFieldValidator()
	{
		return new SingleFieldValidator();
	}

	/**
	 * @test
	 * @return	SingleFieldValidator
	 */
	public function validatorInterface()
	{
		$validator = $this->createSingleFieldValidator();
		$interface = 'Appfuel\Validate\SingleFieldValidatorInterface';
		$this->assertInstanceOf($interface, $validator);

		return $validator;
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 */	
	public function field(SingleFieldValidator $validator)
	{
		$this->assertNull($validator->getField());
		$this->assertSame($validator, $validator->setField('my-field'));
		$this->assertEquals('my-field', $validator->getField());

		$this->assertSame($validator, $validator->clearField());
		$this->assertNull($validator->getField());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function fieldFailure($name)
	{
		$msg = 'field must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$validator = $this->createSingleFieldValidator();
		$validator->setField($name);
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 * @return	SingleFieldValidator
	 */
	public function filters(SingleFieldValidator $validator)
	{
		$this->assertEquals(array(), $validator->getFilters());

		$filter1 = $this->getMock("Appfuel\Validate\Filter\FilterInterface");
		$filter2 = $this->getMock("Appfuel\Validate\Filter\FilterInterface");
		$filter3 = $this->getMock("Appfuel\Validate\Filter\FilterInterface");
		$this->assertSame($validator, $validator->addFilter($filter1));
		$this->assertSame($validator, $validator->addFilter($filter2));
		$this->assertSame($validator, $validator->addFilter($filter3));

		$expected = array($filter1, $filter2, $filter3);
		$this->assertEquals($expected, $validator->getFilters());

		$this->assertSame($validator, $validator->clearFilters());
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 */	
	public function error(SingleFieldValidator $validator)
	{
		$this->assertNull($validator->getError());
		$this->assertSame($validator, $validator->setError('my error text'));
		$this->assertEquals('my error text', $validator->getError());

		$this->assertSame($validator, $validator->clearError());
		$this->assertNull($validator->getError());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return			null
	 */
	public function errorFailure($text)
	{
		$msg = 'error text must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$validator = $this->createSingleFieldValidator();
		$validator->setError($text);
	}



}
