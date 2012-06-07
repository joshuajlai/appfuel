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
	Appfuel\Validate\Coordinator,
	Appfuel\Validate\FieldValidator,
	Appfuel\Validate\ValidationFactory;

class FieldValidatorTest extends BaseTestCase
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
		parent::setUp();
		$this->backupValidationMap();
        ValidationFactory::clear();
    }

    public function tearDown()
    {
		parent::tearDown();
        ValidationFactory::clear();
		$this->restoreValidationMap();
    }

	/**
	 * @param	array	$data
	 * @return	FieldValidator
	 */
	public function createFieldValidator()
	{
		return new FieldValidator();
	}

	/**
	 * @test
	 * @return	FieldValidator
	 */
	public function validatorInterface()
	{
		$validator = $this->createFieldValidator();
		$interface = 'Appfuel\Validate\FieldValidatorInterface';
		$this->assertInstanceOf($interface, $validator);

		return $validator;
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 */	
	public function fields(FieldValidator $validator)
	{
		$this->assertEquals(array(), $validator->getFields());
		$this->assertSame($validator, $validator->addField('my-field'));
		$this->assertEquals(array('my-field'), $validator->getFields());

		$this->assertSame($validator, $validator->clearFields());
		$this->assertEquals(array(), $validator->getFields());
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
		$validator = $this->createFieldValidator();
		$validator->addField($name);
	}

	/**
	 * @test
	 * @depends	validatorInterface
	 * @return	FieldValidator
	 */
	public function filters(FieldValidator $validator)
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
	 * @return null
	 */
	public function loadSpec(FieldValidator $validator)
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
			)
		);
		$spec = new FieldSpec($data);
		$this->assertSame($validator, $validator->loadSpec($spec));
		$this->assertEquals(array($data['field']), $validator->getFields());
		
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

		return $validator;
	}

	/**
	 * @test
	 * @depends	loadSpec
	 * @return	FieldValidator
	 */
	public function isValid(FieldValidator $validator)
	{
		$map = array('int' => 'Appfuel\Validate\Filter\IntFilter');
		ValidationFactory::setFilterMap($map);

		$coord  = new Coordinator();
		$source = array('my-field' => 99);
		$coord->setSource($source);

		$this->assertFalse($coord->isError());
		$this->assertTrue($validator->isValid($coord)); 
		$this->assertEquals(99, $coord->getClean('my-field'));

		$source['my-field'] = 101;
		$coord->clear();
		$coord->setSource($source);

		$this->assertFalse($coord->isError());
		$this->assertFalse($validator->isValid($coord)); 
		$this->assertTrue($coord->isError());

		$error = 'integer failed';
		$stack = $coord->getErrorStack();
		$this->assertInstanceOf('Appfuel\Error\ErrorStack', $stack);

		$this->assertEquals($error, $stack->getMessage());
	}

}
