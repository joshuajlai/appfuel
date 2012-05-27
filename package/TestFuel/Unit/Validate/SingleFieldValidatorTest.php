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

		$this->assertNull($validator->clearField());
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
}
