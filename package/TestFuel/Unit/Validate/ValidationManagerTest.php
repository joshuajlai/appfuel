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
	 * @return	
	 */
	public function validatorCache()
	{
		$key = 'my-validator';
		$result = ValidationManager::getValidatorFromCache($key);
		$this->assertFalse($result);

		$validator = $this->getMock('Appfuel\Validate\ValidatorInterface');
		
		$result = ValidationManager::addValidatorToCache($key, $validator);
		$this->assertNull($result);
		
		$result = ValidationManager::getValidatorFromCache($key);
		$this->assertSame($validator, $result);

		$key2 = 'other-validator';
		$result = ValidationManager::getValidatorFromCache($key2);
		$this->assertFalse($result);


		$validator2 = $this->getMock('Appfuel\Validate\ValidatorInterface');
		$result = ValidationManager::addValidatorToCache($key2, $validator2);
		$this->assertNull($result);
	
		$result = ValidationManager::getValidatorFromCache($key);
		$this->assertSame($validator, $result);

		$result = ValidationManager::getValidatorFromCache($key2);
		$this->assertSame($validator2, $result);
	}

	/**
	 * @test
	 * @depends	validatorCache
	 * @return	
	 */
	public function getValidatorWhenCacheIsPopulated()
	{
		$key = 'my-validator';
		$validator = $this->getMock('Appfuel\Validate\ValidatorInterface');
		ValidationManager::addValidatorToCache($key, $validator);

		$result = ValidationManager::getValidator($key);
		$this->assertSame($validator, $result);
	}

	/**
	 * @test
	 * @depends	validatorMap
	 * @return	null
	 */
	public function getValidatorNoCache()
	{
		$map = array(
			'my-validator' => 'Testfuel\Functional\Validate\MockValidator'
		);
		ValidationManager::setValidatorMap($map);

		$result = ValidationManager::getValidator('my-validator');
		$this->assertInstanceof($map['my-validator'], $result);
	}

	/**
	 * @test
	 * @return
	 */
	public function getValidatorNoClassMapped()
	{
		$msg = 'validator -(not-found) is not mapped';
		$this->setExpectedException('DomainException', $msg);
		ValidationManager::getValidator('not-found');
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function addValidatorToCacheFailure($key)
	{
		$msg = "validator key must be a non empty string";
		$this->setExpectedException('InvalidArgumentException', $msg);
		$validator = $this->getMock('Appfuel\Validate\ValidatorInterface');
		ValidationManager::addValidatorToCache($key, $validator);
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

	/**
	 * @test
	 * @return	
	 */
	public function filterCache()
	{
		$key = 'my-filter';
		$result = ValidationManager::getFilterFromCache($key);
		$this->assertFalse($result);

		$filter = $this->getMock('Appfuel\Validate\Filter\FilterInterface');
		
		$result = ValidationManager::addFilterToCache($key, $filter);
		$this->assertNull($result);
		
		$result = ValidationManager::getFilterFromCache($key);
		$this->assertSame($filter, $result);

		$key2 = 'other-key';
		$result = ValidationManager::getFilterFromCache($key2);
		$this->assertFalse($result);

		$filter2 = $this->getMock('Appfuel\Validate\Filter\FilterInterface');
		$result = ValidationManager::addFilterToCache($key2, $filter2);
		$this->assertNull($result);
		
		$result = ValidationManager::getFilterFromCache($key);
		$this->assertSame($filter, $result);
		
		$result = ValidationManager::getFilterFromCache($key2);
		$this->assertSame($filter2, $result);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function addFilterToCacheFailure($key)
	{
		$msg = "filter key must be a non empty string";
		$this->setExpectedException('InvalidArgumentException', $msg);
		$filter = $this->getMock('Appfuel\Validate\Filter\FilterInterface');
		ValidationManager::addFilterToCache($key, $filter);
	}

	/**
	 * @test
	 * @depends	filterCache
	 * @return	
	 */
	public function getFilterWhenCacheIsPopulated()
	{
		$key = 'my-filter';
		$filter = $this->getMock('Appfuel\Validate\Filter\FilterInterface');
		ValidationManager::addFilterToCache($key, $filter);

		$result = ValidationManager::getFilter($key);
		$this->assertSame($filter, $result);
	}

	/**
	 * @test
	 * @depends	validatorMap
	 * @return	null
	 */
	public function getFilterNoCache()
	{
		$map = array(
			'my-filter' => 'Testfuel\Functional\Validate\MockFilter'
		);
		ValidationManager::setFilterMap($map);

		$result = ValidationManager::getFilter('my-filter');
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
		ValidationManager::getFilter('not-found');
	}
}
