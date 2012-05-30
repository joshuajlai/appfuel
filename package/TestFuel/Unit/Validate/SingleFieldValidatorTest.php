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
	Appfuel\Validate\FieldSpec,
	Appfuel\Validate\ValidationFactory,
	Appfuel\Validate\SingleFieldValidator;

class SingleFieldValidatorTest extends BaseTestCase
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
     * @return  null
     */
    public function setUp()
    {
        $this->validatorMapBk = ValidationFactory::getValidatorMap();
        $this->filterMapBk = ValidationFactory::getFilterMap();
        ValidationFactory::clear();
    }

    public function tearDown()
    {
        ValidationFactory::clear();
        ValidationFactory::setValidatorMap($this->validatorMapBk);
        ValidationFactory::setFilterMap($this->filterMapBk);
    }

	/**
	 * @param	array	$data
	 * @return	SingleFieldValidator
	 */
	public function createSingleFieldValidator()
	{
		return new SingleFieldValidator();
	}

	/**
	 * @test
	 * @return	SingleFieldValidator
	 */
	public function validatorInterface()
	{
		$validator = $this->createSingleFieldValidator();
		$interface = 'Appfuel\Validate\SingleFieldValidatorInterface';
		$this->assertInstanceOf($interface, $validator);

		return $validator;
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 */	
	public function field(SingleFieldValidator $validator)
	{
		$this->assertNull($validator->getField());
		$this->assertSame($validator, $validator->setField('my-field'));
		$this->assertEquals('my-field', $validator->getField());

		$this->assertSame($validator, $validator->clearField());
		$this->assertNull($validator->getField());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function fieldFailure($name)
	{
		$msg = 'field must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$validator = $this->createSingleFieldValidator();
		$validator->setField($name);
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 * @return	SingleFieldValidator
	 */
	public function filters(SingleFieldValidator $validator)
	{
		$this->assertEquals(array(), $validator->getFilters());

		$filter1 = $this->getMock("Appfuel\Validate\Filter\FilterInterface");
		$filter2 = $this->getMock("Appfuel\Validate\Filter\FilterInterface");
		$filter3 = $this->getMock("Appfuel\Validate\Filter\FilterInterface");
		$this->assertSame($validator, $validator->addFilter($filter1));
		$this->assertSame($validator, $validator->addFilter($filter2));
		$this->assertSame($validator, $validator->addFilter($filter3));

		$expected = array($filter1, $filter2, $filter3);
		$this->assertEquals($expected, $validator->getFilters());

		$this->assertSame($validator, $validator->clearFilters());
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 */	
	public function error(SingleFieldValidator $validator)
	{
		$this->assertNull($validator->getError());
		$this->assertSame($validator, $validator->setError('my error text'));
		$this->assertEquals('my error text', $validator->getError());

		$this->assertSame($validator, $validator->clearError());
		$this->assertNull($validator->getError());
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
		$validator = $this->createSingleFieldValidator();
		$validator->setError($text);
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 * @return null
	 */
	public function loadSpec(SingleFieldValidator $validator)
	{
		$map = array(
			'int' => 'Appfuel\Validate\Filter\IntFilter'
		);
		ValidationFactory::setFilterMap($map);

		$data = array(
			'field' => 'my-field',
			'filters' => array(
				'int' => array(
					'options' => array(
						'max' => 100,
						'min' => 1,
					),
					'error' => 'integer failed',
				),
			),
			'error' => 'my field errors:',
		);
		$spec = new FieldSpec($data);
		$this->assertSame($validator, $validator->loadSpec($spec));
		$this->assertEquals($data['field'], $validator->getField());
		
		$filters = $validator->getFilters();
		$this->assertInternalType('array', $filters);
		$this->assertEquals(1, count($filters));

		$filter = current($filters);
		$this->assertInstanceOf('Appfuel\Validate\Filter\IntFilter', $filter);
		$this->assertEquals('int', $filter->getName());
		$this->assertEquals('integer failed', $filter->getError());
		
		$options = $filter->getOptions();
		$this->assertInstanceof('Appfuel\DataStructure\Dictionary', $options);
		$this->assertEquals(100, $options->get('max'));
		$this->assertEquals(1, $options->get('min'));

		$this->assertEquals('my field errors:', $validator->getError());
	}

}
