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
	Appfuel\Validate\UnarySpec,
	Testfuel\TestCase\BaseTestCase;

class FieldSpecTest extends BaseTestCase
{
	/**
	 * @param	array	$data
	 * @return	FieldSpec
	 */
	public function createFieldSpec(array $data)
	{
		return new UnarySpec($data);
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
		$this->assertInstanceOf(
			'Appfuel\Validate\UnaryFieldSpecInterface', 
			$spec
		);

		$this->assertEquals($data['field'], $spec->getField());
		$this->assertNull($spec->getLocation());

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
		$data['location'] = 'post';
		$spec = $this->createFieldSpec($data);
		$this->assertEquals('post', $spec->getLocation());
	}

	/**
	 * @test
	 * @depends	minimal
	 * @return	null
	 */
	public function error(array $data)
	{
		$data['error'] = 'some error message';
		$spec = $this->createFieldSpec($data);
		$this->assertEquals($data['error'], $spec->getError());
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
		$msg = 'validation field must be defined with key -(field)';
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
		$msg  = 'field must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
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
		$msg  = 'field -(my-field) must have one or more filters defined with ';
		$msg .= 'key -(filters)';
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
	 * @dataProvider	provideInvalidStrings
	 * @return	null
	 */
	public function invalidErrorFailure($error)
	{
		$data = array(
			'field'    => 'my-field',
			'filters'  => array('my-filter' => array('params' => array())),
			'location' => 'get',
			'error'  => $error
		);
		$msg  = 'error message must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFieldSpec($data);
	}
}
