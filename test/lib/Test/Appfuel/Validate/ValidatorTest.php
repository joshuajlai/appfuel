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
	 * Used to buld mock object of filters
	 * @var string
	 */
	protected $filterInterface = null;
 
	/**
	 * @return null
	 */
	public function setUp()
	{
		$class = 'Appfuel\Framework\Validate\Filter\FilterInterface';
		$this->filterInterface = $class;
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
	 * @return	Appfuel\Framework\Validate\Filter\FilterInterface
	 */
	public function getMockFilter()
	{
		$methods = array('filter', 'getDefaultError', 'isFailure');
		return $this->getMock($this->filterInterface, $methods);
	}

	/**
	 * @param	mixed	$raw		the first param of the filter method
	 * @param	array	$param		the second param of the filter method
	 * @param	mixed	$returned	value the filter method returns
	 * @param	mixed	$err		default error message of filter
	 * @param	bool	$isFail		indicates a filter failure
	 * @return	Appfuel\Framework\Validate\Filter\FilterInterface
	 */ 
	public function buildMockFilter($raw, $params, $returned, $err, $isFail)
	{
		$params = new Dictionary($params);
		$filter = $this->getMockFilter();
		$filter->expects($this->once())
			   ->method('filter')
			   ->with($this->equalTo($raw), $this->equalTo($params))
			   ->will($this->returnValue($returned));

		$filter->expects($this->once())
			   ->method('getDefaultError')
			   ->will($this->returnValue($err));

		$filter->expects($this->once())
			   ->method('isFailure')
			   ->will($this->returnValue($isFail));


		return $filter;
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
		$filter = $this->getMock($this->filterInterface);
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
		$filter = $this->getMock($this->filterInterface);
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
		$filter = $this->getMock($this->filterInterface);
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
	
		$filter1 = $this->getMock($this->filterInterface);
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

		$filter2 = $this->getMock($this->filterInterface);
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

		$filter3 = $this->getMock($this->filterInterface);
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
		$filter1 = $this->getMock($this->filterInterface);
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
	public function testIsValidSingleFilterA()
	{
		$value  = 123;
		$field  = 'my-field';
		$params = new Dictionary();
		$defaultError = 'I am a default error';
		
		/* create a new coorinator with the source the filter will pull from */
		$coord = new Coordinator(array($field=>$value));

		$filter = $this->getMockFilter();
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

	/**
	 * In this test we are going apply the following conditions:
	 * 1) Only one filter is used
	 * 2) No error message will be given so the default error of the filter
	 *	  will be expected
	 * 3) the filter will fail isFailure will return true and the data
	 *	  return will be a null value
	 * 4) the filter will be mocked to replicate the above conditions
	 * 5) the coordintor will not be mocked and we will use it to collect
	 *	  the expected results.
	 *
	 * Expected results:
	 *	1) isValid should return false
	 *  2) field and value should not appear as clean data in the coordinator
	 *  3) coordinator should have one error for that field
	 *
	 * @depends	testIsValidSingleFilterA
	 * @return	null
	 */
	public function testIsValidSingleFilterB()
	{
		$value  = 123;
		$field  = 'my-field';
		$params = new Dictionary();
		$err    = 'I am a default error';
		
		/* create a new coorinator with the source the filter will pull from */
		$coord = new Coordinator(array($field=>$value));

		$filter = $this->buildMockFilter($value, array(), null, $err, true);
		
		$validator = new Validator($field, $filter);
		$this->assertFalse($validator->isValid($coord));
		$this->assertFalse($coord->getClean($field, false));
		$this->assertTrue($coord->isError());
		$this->assertTrue($coord->isFieldError($field));
		
		$error = $coord->getError($field);
		$this->assertInstanceOf(
			'Appfuel\Validate\Error',
			$error
		);

		$this->assertEquals($err, $error->current());
	}

	/**
	 * In this test we are going apply the following conditions:
	 * 1) Two filters wil be used. 
	 * 2) Filter A will provide the same value and filter be will add
	 *	  the letter b to that value
	 * 3) All filters will pass
	 *
	 * Expected results:
	 *	1) isValid should return true
	 *  2) new modified field sould appear in clean data
	 *  3) coordinator should have no errors
	 *
	 * @return	null
	 */
	public function testIsValidDoubleFilterA()
	{
		$value  = 'filtera';
		$field  = 'my-field';
		$param  = array();
		$err    = 'I am a default error';
		
		/* create a new coorinator with the source the filter will pull from */
		$coord = new Coordinator(array($field=>$value));

		$filter1 = $this->buildMockFilter($value, $param, $value, $err, false);
		
		$value2  = "{$value}b";
		$filter2 = $this->buildMockFilter($value, $param, $value2, $err, false);
		
		$validator = new Validator($field, $filter1);
		$validator->addFilter($filter2, $param);

		$this->assertTrue($validator->isValid($coord));
		$this->assertEquals($value2, $coord->getClean($field, false));
		$this->assertFalse($coord->isError());
	}

	/**
	 * In this test we are going apply the following conditions:
	 * 1) Two filters wil be used. 
	 * 2) Filter A will fail returning null an provide a custom error
	 * 3) Filter B will pass returning the value passed ito it
	 *
	 * Expected results:
	 *	1) isValid should return false
	 *  2) field should not show up in clean data
	 *  3) coordinator should have one custom error message for field
	 *
	 * @return	null
	 */
	public function testIsValidDoubleFilterFirstFilterFails()
	{
		$value  = 'filtera';
		$field  = 'my-field';
		$param  = array();
		$err    = 'first filter failure';
		
		/* create a new coorinator with the source the filter will pull from */
		$coord = new Coordinator(array($field=>$value));

		/*
		 * this filter will fail and return null. We pass in an empty string
		 * for the default error because a manual error will be supplied
		 */
		$filter1 = $this->buildMockFilter($value, $param, null, '', true);
		
		/*
		 * this filter will pass and return the value passed into it
		 */
		$filter2 = $this->buildMockFilter($value, $param, $value, '', false);
		
		$validator = new Validator($field, $filter1, $param, $err);
		$validator->addFilter($filter2, $param);

		$this->assertFalse($validator->isValid($coord));
		$this->assertFalse($coord->getClean($field, false));
		$this->assertTrue($coord->isError());

		$error = $coord->getError($field);
		$this->assertInstanceOf(
			'Appfuel\Validate\Error',
			$error
		);
		$this->assertEquals($err, $error->current());
	}

	/**
	 * In this test we are going apply the following conditions:
	 * 1) Two filters wil be used. 
	 * 2) Filter A will pass returning the value passed in
	 * 3) Filter B will fail returning null
	 *
	 * Expected results:
	 *	1) isValid should return false
	 *  2) field should not show up in clean data
	 *  3) coordinator should have one custom error message for field
	 *
	 * @return	null
	 */
	public function testIsValidDoubleFilterSecondFilterFails()
	{
		$value  = 'filtera';
		$field  = 'my-field';
		$param  = array();
		$err    = 'second filter failure';
		
		/* create a new coorinator with the source the filter will pull from */
		$coord = new Coordinator(array($field=>$value));

		$filter1 = $this->buildMockFilter($value, $param, $value, '', false);
		$filter2 = $this->buildMockFilter($value, $param, null, '', true);
		
		$validator = new Validator($field, $filter1);
		$validator->addFilter($filter2, $param, $err);

		$this->assertFalse($validator->isValid($coord));
		$this->assertFalse($coord->getClean($field, false));
		$this->assertTrue($coord->isError());

		$error = $coord->getError($field);
		$this->assertInstanceOf(
			'Appfuel\Validate\Error',
			$error
		);
		$this->assertEquals($err, $error->current());
	}

	/**
	 * In this test we are going apply the following conditions:
	 * 1) Two filters wil be used. 
	 * 2) Filter A will fail returning null
	 * 3) Filter B will fail returning null
	 *
	 * Expected results:
	 *	1) isValid should return false
	 *  2) field should not show up in clean data
	 *  3) coordinator should have two custom error message for field
	 *
	 * @return	null
	 */
	public function testIsValidDoubleFilterBothFiltersFails()
	{
		$value  = 'filtera';
		$field  = 'my-field';
		$param  = array();
		$errA   = 'this is a default error message';
		$errB   = 'this is a custom error message for filter b'; 
		/* create a new coorinator with the source the filter will pull from */
		$coord = new Coordinator(array($field=>$value));

		$filter1 = $this->buildMockFilter($value, $param, null, $errA, true);
		$filter2 = $this->buildMockFilter($value, $param, null, '', true);
		
		$validator = new Validator($field, $filter1, $param, $errA);
		$validator->addFilter($filter2, $param, $errB);

		$this->assertFalse($validator->isValid($coord));
		$this->assertFalse($coord->getClean($field, false));
		$this->assertTrue($coord->isError());

		$error = $coord->getError($field);
		$this->assertInstanceOf(
			'Appfuel\Validate\Error',
			$error
		);
		$this->assertEquals(2, $error->count());
		$this->assertEquals($errA, $error->current());
		
		$error->next();
		$this->assertEquals($errB, $error->current());	
	}



}
