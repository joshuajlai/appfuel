<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\View;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Validate\Validator,
	Appfuel\Validate\Coordinator;

/**
 * This is a standard validator which represents one field. It will run
 * one or more filters against that field and report the results to the 
 * the Coordinator
 */
class ValidatorTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Validatotr
	 */
	protected $validator = null;

	/**
	 * Used to handle raw, clean and error data
	 * @var Coordinator
	 */
	protected $field = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->field = 'my-field';
		$this->validator = new Validator($this->field);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->validator);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Validate\FieldValidatorInterface',
			$this->validator
		);
	}

	/**
	 * The field is an immutable member passed into the constructor
	 *
	 * @return null
	 */
	public function testGetField()
	{
		$this->assertEquals($this->field, $this->validator->getField());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorFieldEmptyString()
	{
		$validator = new Validator('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorFieldArray()
	{
		$validator = new Validator(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorFieldObject()
	{
		$validator = new Validator(new StdClass());
	}

	/**
	 * Scalar are allowed
	 * 
	 * @return	null
	 */
	public function testConstructorInt()
	{
		/* scalar values are valid */
		$validator = new Validator(12345);
		$this->assertEquals(12345, $validator->getField());
	}

	/**
	 * You can optionally add a filter while you instantiate the class.
	 * In this test I will provide a filter with no error message
	 *
	 * @return null
	 */
	public function testAddFilterConstructorWithFilterNoParamsNoErrorMsg()
	{
		$interface = 'Appfuel\Framework\Validate\Filter\FilterInterface';
		$filter = $this->getMock($interface);
		$field  = 'my-field';

		$validator = new Validator($field, $filter);
		$this->assertEquals($field, $validator->getField());

		/* each filter is addded as the following */
		$expected = array(
			array(
				'filter' => $filter,
				'params' => null,
				'error'  => null
			)
		);
		$this->assertEquals($expected, $validator->getFilters());
	}

	/** 
	 * @return null
	 */
	public function testAddFilterConstructorWithFilterParamsNoErrorMsg()
	{
		$interface = 'Appfuel\Framework\Validate\Filter\FilterInterface';
		$filter = $this->getMock($interface);
		$field  = 'my-field';
		$params = array('a' => 'b');

		$validator = new Validator($field, $filter, $params);
		$this->assertEquals($field, $validator->getField());

		/* each filter is addded as the following */
		$expected = array(
			array(
				'filter' => $filter,
				'params' => $params,
				'error'  => null
			)
		);
		$this->assertEquals($expected, $validator->getFilters());
	}

	/**
	 * @return null
	 */
	public function testAddFilterConstructorWithFilterParamsrrorMsg()
	{
		$interface = 'Appfuel\Framework\Validate\Filter\FilterInterface';
		$filter = $this->getMock($interface);
		$field  = 'my-field';
		$params = array('a' => 'b');
		$error  = 'this is an error message';
		$validator = new Validator($field, $filter, $params, $error);
		$this->assertEquals($field, $validator->getField());

		/* each filter is addded as the following */
		$expected = array(
			array(
				'filter' => $filter,
				'params' => $params,
				'error'  => $error
			)
		);
		$this->assertEquals($expected, $validator->getFilters());
	}

	/**
	 * @return null
	 */
	public function testAddParams()
	{
		$this->assertEquals(array(), $this->validator->getFilters());
	
		$interface = 'Appfuel\Framework\Validate\Filter\FilterInterface';
		$filter1 = $this->getMock($interface);
		$field1  = 'my-field';
		$params1 = null;
		$error1  = null;
		$filterData1 = array(
			'filter' => $filter1,
			'params' => $params1,
			'error'  => $error1
		);

		$this->assertSame(
			$this->validator,
			$this->validator->addFilter($filter1, $params1, $error1),
			'exposes a fluent interface'
		);

		$expected = array($filterData1);
		$this->assertEquals($expected, $this->validator->getFilters());

		$filter2 = $this->getMock($interface);
		$field2  = 'my-field';
		$params2 = array('c' => 'b');
		$error2  = null;
		$filterData2 = array(
			'filter' => $filter2,
			'params' => $params2,
			'error'  => $error2
		);

		$this->assertSame(
			$this->validator,
			$this->validator->addFilter($filter2, $params2, $error2),
			'exposes a fluent interface'
		);

		$expected = array($filterData1, $filterData2);
		$this->assertEquals($expected, $this->validator->getFilters());

		$filter3 = $this->getMock($interface);
		$field3  = 'my-field';
		$params3 = array('c' => 'b');
		$error3  = 'this is an error message';
		$filterData3 = array(
			'filter' => $filter3,
			'params' => $params3,
			'error'  => $error3
		);

		$this->assertSame(
			$this->validator,
			$this->validator->addFilter($filter3, $params3, $error3),
			'exposes a fluent interface'
		);

		$expected = array($filterData1, $filterData2, $filterData3);
		$this->assertEquals($expected, $this->validator->getFilters());
	}

	/**
	 * Duplicate are not checked against and can occur
	 *
	 * @return null
	 */
	public function testAddParamsDuplicates()
	{
	
		$interface = 'Appfuel\Framework\Validate\Filter\FilterInterface';
		$filter1 = $this->getMock($interface);
		$field1  = 'my-field';
		$params1 = null;
		$error1  = null;
		$filterData1 = array(
			'filter' => $filter1,
			'params' => $params1,
			'error'  => $error1
		);

		$this->assertSame(
			$this->validator,
			$this->validator->addFilter($filter1, $params1, $error1),
			'exposes a fluent interface'
		);


		$this->assertSame(
			$this->validator,
			$this->validator->addFilter($filter1, $params1, $error1),
			'exposes a fluent interface'
		);

		$expected = array($filterData1, $filterData1);
		$this->assertEquals($expected, $this->validator->getFilters());
	}

	/**
	 * In this test we are going apply the following conditions:
	 * 1) Only one filter is used
	 * 2) No error message will be given so the default error of the filter
	 *	  will be expected
	 * 3) the filter will pass isFailure will return false and the data
	 *	  will be returned uneffected
	 * 4) the filter will be mocked to replicate the above conditions
	 * 5) the coordintor will not be mocked and we will use it to collect
	 *	  the expected results.
	 *
	 * Expected results:
	 *	1) isValid should return true
	 *  2) field and value should appear as clean data in the coordinator
	 *  3) coordinator should have to errors.
	 *
	 * @return	null
	 */
	public function testIsSatiSingleFilter()
	{
		$interface = 'Appfuel\Framework\Validate\Filter\FilterInterface';
		$methods   = array('filter', 'getDefaultError', 'isFailure');
		
		$value  = 123;
		$field  = 'my-field';
		$params = new Dictionary();
		$defaultError = 'I am a default error';
		
		/* create a new coorinator with the source the filter will pull from */
		$coord = new Coordinator(array($field=>$value));

		$filter = $this->getMock($interface, $methods);
		$filter->expects($this->once())
			   ->method('filter')
			   ->with($this->equalTo($value), $this->equalTo($params))
			   ->will($this->returnValue($value));

		$filter->expects($this->once())
			   ->method('getDefaultError')
			   ->will($this->returnValue($defaultError));

		$filter->expects($this->once())
			   ->method('isFailure')
			   ->will($this->returnValue(false));

		$validator = new Validator($field, $filter);
		$this->assertTrue($validator->isValid($coord));
		$this->assertEquals($value, $coord->getClean($field));
		$this->assertFalse($coord->isError());
	}
}
