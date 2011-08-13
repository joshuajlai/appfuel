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
namespace Test\Appfuel\View;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Validate\Validator,
	Appfuel\Validate\Coordinator;

/**
 * This is a standard validator which represents one field. It will run
 * one or more filters against that field and report the results to the 
 * the Coordinator
 */
class ValidatorTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Validatotr
	 */
	protected $validator = null;

	/**
	 * Used to handle raw, clean and error data
	 * @var Coordinator
	 */
	protected $field = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->field = 'my-field';
		$this->validator = new Validator($this->field);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->validator);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Validate\FieldValidatorInterface',
			$this->validator
		);
	}

}
