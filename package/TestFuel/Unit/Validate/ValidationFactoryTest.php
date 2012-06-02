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
	Appfuel\Validate\ValidationFactory;

class ValidationFactoryTest extends BaseTestCase
{
	/**
	 * @return	null
	 */
	public function setUp()
	{
		parent::setUp();
		$this->backupValidationMap();
		ValidationFactory::clear();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		parent::tearDown();
		ValidationFactory::clear();
		$this->restoreValidationMap();
	}

	/**
	 * @test
	 * @return	null
	 */
	public function validatorMap()
	{
		$this->assertEquals(array(), ValidationFactory::getValidatorMap());
		
		$map = array(
			'single-field' => 'Appfuel\Validate\SingleFieldValidator',
			'dual-field'   => 'Appfuel\Validate\DualFieldValidator',
			'multi-field'  => 'Appfuel\Validate\MulitFiledValidator'
		);
		$this->assertNull(ValidationFactory::setValidatorMap($map));
		$this->assertEquals($map, ValidationFactory::getValidatorMap());

		$this->assertEquals(
			$map['single-field'], 
			ValidationFactory::mapValidator('single-field')
		);

		$this->assertEquals(
			$map['dual-field'], 
			ValidationFactory::mapValidator('dual-field')
		);

		$this->assertEquals(
			$map['multi-field'], 
			ValidationFactory::mapValidator('multi-field')
		);

		$this->assertFalse(ValidationFactory::mapValidator('no-match'));

		$this->assertNull(ValidationFactory::clearValidatorMap());
		$this->assertEquals(array(), ValidationFactory::getValidatorMap());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function validatorMapNotAssociativeArrayFailure()
	{
		$map = array('field1', 'field2', 'field3');

		$msg  = 'validator map must be an associative array of key to ';
		$msg .= 'validator class name mappings';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setValidatorMap($map);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function validatorMapEmptyKeyFailure()
	{
		$map = array(
			'field1' => 'SomeClass',
			'' => 'OtherClass'
		);
		$msg = 'validator key must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setValidatorMap($map, $msg);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function validatorMapIntegerKeyFailure()
	{
		$map = array(
			'field1' => 'SomeClass',
			1234 => 'OtherClass'
		);
		$msg = 'validator key must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setValidatorMap($map, $msg);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function validatorMapEmptyClassFailure()
	{
		$map = array(
			'field1' => 'SomeClass',
			'field2' => ''
		);
		$msg = 'validator class must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setValidatorMap($map, $msg);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return	null
	 */
	public function validatorMapNonStringClassFailure($class)
	{
		$map = array(
			'filter1' => 'SomeClass',
			'filter2' => $class
		);
		$msg = 'validator class must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setValidatorMap($map, $msg);
	}

	/**
	 * @test
	 * @return
	 */
	public function createValidatorNoClassMapped()
	{
		$msg = 'validator -(not-found) is not mapped';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::createValidator('not-found');
	}

	/**
	 * @test
	 * @return	null
	 */
	public function filterMap()
	{
		$this->assertEquals(array(), ValidationFactory::getFilterMap());
		
		$map = array(
			'int'     => 'Appfuel\Validate\Filter\IntFilter',
			'string'  => 'Appfuel\Validate\Filter\StringFilter',
			'bool'	  => 'Appfuel\Validate\Filter\BoolFilter'
		);
		$this->assertNull(ValidationFactory::setFilterMap($map));
		$this->assertEquals($map, ValidationFactory::getFilterMap());

		$result = ValidationFactory::mapFilter('int');
		$this->assertEquals($map['int'], $result);

		$result = ValidationFactory::mapFilter('string');
		$this->assertEquals($map['string'], $result);

		$result = ValidationFactory::mapFilter('bool');
		$this->assertEquals($map['bool'], $result);

		$this->assertFalse(ValidationFactory::mapValidator('no-match'));

		$this->assertNull(ValidationFactory::clearFilterMap());
		$this->assertEquals(array(), ValidationFactory::getFilterMap());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function filterMapNotAssociativeArrayFailure()
	{
		$map = array('filter1', 'filter2', 'filter3');

		$msg  = 'filter map must be an associative array of key to ';
		$msg .= 'filter class name mappings';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setFilterMap($map);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function filterMapEmptyKeyFailure()
	{
		$map = array(
			'filter' => 'SomeClass',
			'' => 'OtherClass'
		);
		$msg = 'filter key must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setFilterMap($map, $msg);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function filterMapIntegerKeyFailure()
	{
		$map = array(
			'filter1' => 'SomeClass',
			1234 => 'OtherClass'
		);
		$msg = 'filter key must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setFilterMap($map, $msg);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function filterMapEmptyClassFailure()
	{
		$map = array(
			'filter1' => 'SomeClass',
			'filter2' => ''
		);
		$msg = 'filter class must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setFilterMap($map, $msg);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return	null
	 */
	public function filterMapNonStringClassFailure($class)
	{
		$map = array(
			'filter1' => 'SomeClass',
			'filter2' => $class
		);
		$msg = 'filter class must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::setFilterMap($map, $msg);
	}

	/**
	 * @test
	 * @depends	validatorMap
	 * @return	null
	 */
	public function createFilterNoCache()
	{
		$map = array(
			'my-filter' => 'Testfuel\Functional\Validate\MockFilter'
		);
		ValidationFactory::setFilterMap($map);

		$result = ValidationFactory::getFilter('my-filter');
		$this->assertInstanceof($map['my-filter'], $result);
	}

	/**
	 * @test
	 * @return
	 */
	public function getFilterNoClassMapped()
	{
		$msg = 'filter -(not-found) is not mapped';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::getFilter('not-found');
	}
}
