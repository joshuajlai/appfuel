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
	Testfuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary,
	Appfuel\Validate\Filter\FilterSpec,
	Appfuel\Validate\Filter\FilterInterface,
	Appfuel\Validate\Filter\ValidationFilter;

class ValidationFilterTest extends BaseTestCase
{
	/**
	 * @param	array	$data
	 * @return	SingleFieldValidator
	 */
	public function createValidationFilter()
	{
		return new ValidationFilter();
	}

	/**
	 * @test
	 * @return	ValidationFilter
	 */
	public function filterInterface()
	{
		$filter = $this->createValidationFilter();
		$interface = 'Appfuel\Validate\Filter\FilterInterface';
		$this->assertInstanceOf($interface, $filter);

		return $filter;
	}

	/**
	 * @test
	 * @depends	filterInterface
	 */	
	public function name(ValidationFilter $filter)
	{
		$this->assertNull($filter->getName());
		$this->assertSame($filter, $filter->setName('my-name'));
		$this->assertEquals('my-name', $filter->getName());

		$this->assertSame($filter, $filter->clearName());
		$this->assertNull($filter->getName());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function nameFailure($name)
	{
		$msg = 'filter name must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$filter = $this->createValidationFilter();
		$filter->setName($name);
	}

	/**
	 * @test
	 * @depends	filterInterface
	 */	
	public function options(ValidationFilter $filter)
	{
		$this->assertNull($filter->getOptions());
		
		$options = $this->getMock('Appfuel\DataStructure\DictionaryInterface');
		$this->assertSame($filter, $filter->setOptions($options));
		$this->assertSame($options, $filter->getOptions());

		$this->assertSame($filter, $filter->clearOptions());
		$this->assertNull($filter->getOptions());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 */	
	public function getOptionDictionaryInvalidString($opt)
	{
		$options = new Dictionary(array('opt-a' => 'value-a'));
		$filter = $this->createValidationFilter();
		$filter->setOptions($options);
		$this->assertNull($filter->getOption($opt));
		
		$default = 'my default';
		$this->assertEquals($default, $filter->getOption($opt, $default));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 */	
	public function getOptionNoDictionaryInvalidString($opt)
	{
		$filter = $this->createValidationFilter();
		$this->assertNull($filter->getOption($opt));
		
		$default = 'my default';
		$this->assertEquals($default, $filter->getOption($opt, $default));
	}

	/**
	 * @test
	 * @depends	filterInterface
	 */	
	public function getOptionValidOptNotFound(ValidationFilter $filter)
	{
		$options = new Dictionary(array('opt-a' => 'value-a'));
		$filter = $this->createValidationFilter();
		$filter->setOptions($options);
		$this->assertNull($filter->getOption('notfound'));
		
		$default = 'my default';
		$this->assertEquals($default, $filter->getOption('notfound', $default));
	}

	/**
	 * @test
	 * @depends	filterInterface
	 */	
	public function getOption(ValidationFilter $filter)
	{
		$options = new Dictionary(array(
			'opt-a' => 'value-a',
			'opt-b' => 'value-b'
		));
		$filter = $this->createValidationFilter();
		$filter->setOptions($options);
		$this->assertEquals('value-a', $filter->getOption('opt-a'));
		$this->assertEquals('value-b', $filter->getOption('opt-b'));
		
	}

	/**
	 * @test
	 * @depends	filterInterface
	 */	
	public function error(ValidationFilter $filter)
	{
		$this->assertNull($filter->getError());
		$this->assertSame($filter, $filter->setError('my error text'));
		$this->assertEquals('my error text', $filter->getError());

		$this->assertSame($filter, $filter->clearError());
		$this->assertNull($filter->getError());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return			null
	 */
	public function errorFailure($text)
	{
		$msg = 'error text must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$filter = $this->createValidationFilter();
		$filter->setError($text);
	}

	/**
	 * @test
	 * @depends	filterInterface
	 */	
	public function failureToken(ValidationFilter $filter)
	{
		$this->assertEquals(
			FilterInterface::FAILURE, 
			$filter->getFailureToken()
		);
	}

	/**
	 * @test
	 * @depends	filterInterface
	 * @return	null
	 */
	public function loadSpec(ValidationFilter $filter)
	{
		$data = array(
			'name'    => 'int',
			'options' => array(
				'min' => 100,
				'max' => 200
			),
			'error' => 'this is an error'
		);
		$spec = new FilterSpec($data);
		$this->assertSame($filter, $filter->loadSpec($spec));
		$this->assertEquals('int', $filter->getName());
	}

}
