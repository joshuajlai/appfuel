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
	Appfuel\Validate\ValidationManager;

class ValidationManagerTest extends BaseTestCase
{
	/**
	 * @var array
	 */
	protected $validatorMapBk = array();

	/**
	 * @var array
	 */
	protected $filterMapBk = array();


	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->validatorMapBk = ValidationManager::getValidatorMap();
		$this->filterMapBk = ValidationManager::getFilterMap();
		ValidationManager::clear();
	}

	public function tearDown()
	{
		ValidationManager::clear();
		ValidationManager::setValidatorMap($this->validatorMapBk);
		ValidationManager::setFilterMap($this->filterMapBk);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function validatorMap()
	{
		$this->assertEquals(array(), ValidationManager::getValidatorMap());
		
		$map = array(
			'single-field' => 'Appfuel\Validate\SingleFieldValidator',
			'dual-field'   => 'Appfuel\Validate\DualFieldValidator',
			'multi-field'  => 'Appfuel\Validate\MulitFiledValidator'
		);
		$this->assertNull(ValidationManager::setValidatorMap($map));
		$this->assertEquals($map, ValidationManager::getValidatorMap());

		$this->assertEquals(
			$map['single-field'], 
			ValidationManager::mapValidator('single-field')
		);

		$this->assertEquals(
			$map['dual-field'], 
			ValidationManager::mapValidator('dual-field')
		);

		$this->assertEquals(
			$map['multi-field'], 
			ValidationManager::mapValidator('multi-field')
		);

		$this->assertFalse(ValidationManager::mapValidator('no-match'));

		$this->assertNull(ValidationManager::clearValidatorMap());
		$this->assertEquals(array(), ValidationManager::getValidatorMap());
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
		ValidationManager::setValidatorMap($map);
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
		ValidationManager::setValidatorMap($map, $msg);
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
		ValidationManager::setValidatorMap($map, $msg);
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
		ValidationManager::setValidatorMap($map, $msg);
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
		ValidationManager::setValidatorMap($map, $msg);
	}
	/**
	 * @test
	 * @return	null
	 */
	public function filterMap()
	{
		$this->assertEquals(array(), ValidationManager::getFilterMap());
		
		$map = array(
			'int'     => 'Appfuel\Validate\Filter\IntFilter',
			'string'  => 'Appfuel\Validate\Filter\StringFilter',
			'bool'	  => 'Appfuel\Validate\Filter\BoolFilter'
		);
		$this->assertNull(ValidationManager::setFilterMap($map));
		$this->assertEquals($map, ValidationManager::getFilterMap());

		$result = ValidationManager::mapFilter('int');
		$this->assertEquals($map['int'], $result);

		$result = ValidationManager::mapFilter('string');
		$this->assertEquals($map['string'], $result);

		$result = ValidationManager::mapFilter('bool');
		$this->assertEquals($map['bool'], $result);

		$this->assertFalse(ValidationManager::mapValidator('no-match'));

		$this->assertNull(ValidationManager::clearFilterMap());
		$this->assertEquals(array(), ValidationManager::getFilterMap());
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
		ValidationManager::setFilterMap($map);
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
		ValidationManager::setFilterMap($map, $msg);
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
		ValidationManager::setFilterMap($map, $msg);
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
		ValidationManager::setFilterMap($map, $msg);
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
		ValidationManager::setFilterMap($map, $msg);
	}
}
