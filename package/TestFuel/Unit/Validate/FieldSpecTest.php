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
	Appfuel\Validate\FieldSpec,
	Appfuel\Validate\ValidationFactory,
	Testfuel\TestCase\BaseTestCase;

class FieldSpecTest extends BaseTestCase
{
	/**
	 * @return null
	 */
	public function setUp()
	{
		parent::setUp();
		$this->backupValidationMap();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		parent::tearDown();
		$this->restoreValidationMap();
	}

	/**
	 * @param	array	$data
	 * @return	FieldSpec
	 */
	public function createFieldSpec(array $data)
	{
		return new FieldSpec($data);
	}

	/**
	 * @test
	 * @return	FieldSpec
	 */
	public function minimal()
	{
		$data = array(
			'field'  => 'id',
			'filters' => array(
				'int' => array(
					'params' => array('max' => 100),
					'error'  => 'invalid integer'
				)
			),
			
		);
		$spec = $this->createFieldSpec($data);
		$this->assertInstanceOf('Appfuel\Validate\FieldSpecInterface', $spec);

		$this->assertEquals(array($data['field']), $spec->getFields());
		$this->assertNull($spec->getLocation());

		$this->assertNull($spec->getValidator());
		$this->assertNull($spec->getFilterSpec());

		$filters = $spec->getFilters();
		$this->assertInternalType('array', $filters);
		$this->assertEquals(1, count($filters));
		
		$filter = current($filters);
		$this->assertInstanceOf('Appfuel\Validate\Filter\FilterSpec', $filter);
		return $data;
	}

	/**
	 * @test
	 * @depends	minimal
	 * @return	null
	 */
	public function location(array $data)
	{
		$key = 'post';
		$data['location'] = $key;
		$spec = $this->createFieldSpec($data);
		$this->assertEquals($key, $spec->getLocation());
	}

	/**
	 * @test
	 * @depends	minimal
	 * @return	null
	 */
	public function filterSpec(array $data)
	{
		$key = 'my-spec';	
		$class = 'Appfuel\Validate\Filter\FilterSpec';
		ValidationFactory::addToFilterMap($key, $class);
		$data['filter-spec'] = $key;
		$spec = $this->createFieldSpec($data);
		$this->assertEquals($key, $spec->getFilterSpec());
	}

	/**
	 * @test
	 * @depends	minimal
	 * @return	null
	 */
	public function validatorKey(array $data)
	{
		$key = 'my-validator';
		$data['validator'] = $key;
		$spec = $this->createFieldSpec($data);
		$this->assertEquals($key, $spec->getValidator());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function noFieldFailure()
	{
		$data = array(
			'filter' => 'int',
			'location' => 'post',
		);
        $msg  = "must use -(field) or -(fields) to indicate fields for ";
		$msg .= "the validator";
		$this->setExpectedException('DomainException', $msg);
		$spec = $this->createFieldSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function invalidFieldFailure($field)
	{
		$data = array(
			'field'  => $field,
			'filter' => 'my-filter',
			'location' => 'post',
		);
		$this->setExpectedException('DomainException');
		$spec = $this->createFieldSpec($data);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function noFilterFailure()
	{
		$data = array(
			'field' => 'my-field',
			'location' => 'post',
		);
		$msg  = 'must have one or more filters defined with key -(filters)';
		$this->setExpectedException('DomainException', $msg);
		$spec = $this->createFieldSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return	null
	 */
	public function invalidLocationFailure($location)
	{
		$data = array(
			'field'    => 'my-field',
			'filters'  => array('my-filter' => array('params' => array())),
			'location' => $location,
		);
		$msg  = 'the location of the field must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFieldSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function invalidValidatorFailure($validator)
	{
		$data = array(
			'field'     => 'my-field',
			'filters'   => array('my-filter' => array('params' => array())),
			'validator' => $validator
		);

		$msg  = 'the name of the validator must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFieldSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function invalidFilterSpecFailure($filterSpec)
	{
		$data = array(
			'field'       => 'my-field',
			'filters'     => array('my-filter' => array('params' => array())),
			'filter-spec' => $filterSpec
		);

		$msg  = 'filter spec key must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFieldSpec($data);
	}


}
