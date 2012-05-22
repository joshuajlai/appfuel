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
namespace Testfuel\Unit\Console;

use StdClass,
	Appfuel\Validate\FieldSpec,
	Testfuel\TestCase\BaseTestCase;

class FieldSpecTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidStringsWithEmpty()
	{
		$result = $this->provideInvalidStrings();
		$result[] = array('');
		return $result;
	}

	/**
	 * @return	array	
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(12345),
			array(1.234),
			array(0),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass()),
		);
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
			'filter' => 'int',
			
		);
		$spec = $this->createFieldSpec($data);
		$this->assertInstanceOf('Appfuel\Validate\FieldSpecInterface', $spec);
		$this->assertEquals($data['field'], $spec->getField());
		$this->assertEquals($data['filter'], $spec->getFilter());
		$this->assertEquals(array(), $spec->getParams());
		$this->assertNull($spec->getLocation());

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
	public function params(array $data)
	{
		$data['params'] = array(
			'param-a' => 'value-a',
			'param-b' => 'value-b',
			'param-c' => 'value-c' 
		);
		$spec = $this->createFieldSpec($data);
		$this->assertEquals($data['params'], $spec->getParams());
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
			'params' => array('a', 'b', 'c'),
			'location' => 'post',
			
		);
		$msg = 'validation field must be defined with key -(field)';
		$this->setExpectedException('DomainException', $msg);
		$spec = $this->createFieldSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsWithEmpty
	 * @return	null
	 */
	public function invalidFieldFailure($field)
	{
		$data = array(
			'field'  => $field,
			'filter' => 'my-filter',
			'params' => array('a', 'b', 'c'),
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
			'params' => array('a', 'b', 'c'),
			'location' => 'post',
			
		);
		$msg  = 'field -(my-field) must have a filter defined with ';
		$msg .= 'key -(filter)';
		$this->setExpectedException('DomainException', $msg);
		$spec = $this->createFieldSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsWithEmpty
	 * @return	null
	 */
	public function invalidFilterFailure($filter)
	{
		$data = array(
			'field'  => 'my-field',
			'filter' => $filter,
			'params' => array('a', 'b', 'c'),
			'location' => 'post',
			
		);
		$msg  = 'filter must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
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
			'filter'   => 'my-filter',
			'location' => $location,
			'params' => array('a', 'b', 'c'),
			
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
			'filter'   => 'my-filter',
			'location' => 'get',
			'params' => array('a', 'b', 'c'),
			'error'  => $error
			
		);
		$msg  = 'error message must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFieldSpec($data);
	}
}
