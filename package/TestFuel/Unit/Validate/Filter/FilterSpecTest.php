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
namespace Testfuel\Unit\Validate\Filter;

use StdClass,
	Appfuel\Validate\Filter\FilterSpec,
	Appfuel\DataStructure\Dictionary,
	Testfuel\TestCase\BaseTestCase;

class FilterSpecTest extends BaseTestCase
{

	/**
	 * @param	array	$data
	 * @return	FieldSpec
	 */
	public function createFilterSpec(array $data)
	{
		return new FilterSpec($data);
	}

	/**
	 * @test
	 * @return	FieldSpec
	 */
	public function minimal()
	{
		$data = array(
			'name'  => 'id',
			
		);
		$spec = $this->createFilterSpec($data);
		$interface = 'Appfuel\Validate\Filter\FilterSpecInterface';
		$this->assertInstanceOf($interface, $spec);
		$this->assertEquals($data['name'], $spec->getName());
		$this->assertNull($spec->getError());
		$this->assertInstanceOf(
			'Appfuel\DataStructure\DictionaryInterface',
			$spec->getOptions()
		);
		return $data;
	}

	/**
	 * @test
	 * @depends	minimal
	 * @return	null
	 */
	public function options(array $data)
	{
		$data['options'] = array(
			'param-a' => 'value-a',
			'param-b' => 'value-b',
			'param-c' => 'value-c' 
		);
		$spec = $this->createFilterSpec($data);

		$expected = new Dictionary($data['options']);
		$this->assertEquals($expected, $spec->getOptions());
	}

	/**
	 * @test
	 * @depends	minimal
	 * @return	null
	 */
	public function error(array $data)
	{
		$data['error'] = 'some error message';
		$spec = $this->createFilterSpec($data);
		$this->assertEquals($data['error'], $spec->getError());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function noNameFailure()
	{
		$data = array(
			'params' => array('a', 'b', 'c'),
			'error'  => 'my error'	
		);
		$msg = 'filter name must be defined with key -(name)';
		$this->setExpectedException('DomainException', $msg);
		$spec = $this->createFilterSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function invalidFieldFailure($field)
	{
		$data = array(
			'name'  => $field,
			'options' => array('a', 'b', 'c'),
		);
		$msg  = 'filter name must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFilterSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return	null
	 */
	public function invalidErrorFailure($error)
	{
		$data = array(
			'name'    => 'my-filter',
			'options' => array('a', 'b', 'c'),
			'error'  => $error
			
		);
		$msg  = 'error message must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFilterSpec($data);
	}
}
