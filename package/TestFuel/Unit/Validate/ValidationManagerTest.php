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



}
