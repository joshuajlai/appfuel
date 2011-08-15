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
	Appfuel\Validate\Controller,
	Appfuel\Validate\Coordinator;

/**
 * Test the controller's ability to add rules or filters to fields and 
 * validate or sanitize those fields
 */
class ControllerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Controller
	 */
	protected $controller = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->controller = new Controller();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->controller);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Validate\ControllerInterface',
			$this->controller
		);
	}

	/**
	 * Test the php-int-filter
	 * 
	 * @return null
	 */
	public function testIsSatisfiedBySingleFieldPHPIntFilter()
	{
		$field  = 'my-field';
		$raw    = array($field => 33);
		$filter = 'php-int-filter';

		$this->assertSame(
			$this->controller,
			$this->controller->addFilter($field, $filter),
			'exposes a fluent interface'
		);

		$this->assertTrue($this->controller->isSatisfiedBy($raw));
		$this->assertFalse($this->controller->isError());
		$this->assertEquals(33, $this->controller->getClean($field));

		$raw = array($field => 'abc');
		$this->assertFalse($this->controller->isSatisfiedBy($raw));
		$this->assertTrue($this->controller->isError());
	
		$error = $this->controller->getError($field);
		$this->assertInstanceOf(
			'Appfuel\Validate\Error',
			$error
		);

		/* prove the filters default error message was used */
		$filter = new \Appfuel\Validate\Filter\PHPFilter\IntFilter($filter);
		$this->assertEquals(
			$filter->getDefaultError(),
			$error->current()
		);

		$expected = array($field => $error);
		$this->assertEquals($expected, $this->controller->getErrors());
	}

	/**
	 * Test the php-bool-filter
	 * 
	 * @return null
	 */
	public function testIsSatisfiedBySingleFieldPHPBoolFilter()
	{
		$field  = 'my-field';
		$raw    = array($field => 'true');
		$filter = 'php-bool-filter';

		$this->assertSame(
			$this->controller,
			$this->controller->addFilter($field, $filter),
			'exposes a fluent interface'
		);

		$this->assertTrue($this->controller->isSatisfiedBy($raw));
		$this->assertFalse($this->controller->isError());
		$this->assertEquals(true, $this->controller->getClean($field));

		$raw = array($field => 'abc');
		$this->assertTrue($this->controller->isSatisfiedBy($raw));
		
		$this->assertEquals(false, $this->controller->getClean($field));
		$this->assertFalse($this->controller->isError());
	}

	/**
	 * Test the php-bool-filter with parameters. This will cause anything thats
	 * not really a false to return as a failure. This time we will also add
	 * a custom error message the will be used when the filter fails
	 * 
	 * @return null
	 */
	public function testIsSatisfiedBySingleFieldPHPBoolFilterWithParams()
	{
		$field  = 'my-field';
		$raw    = array($field => 'true');
		$filter = 'php-bool-filter';
		$params = array('strict' => true);
		$err    = "this is not a my bool value";

		$this->assertSame(
			$this->controller,
			$this->controller->addFilter($field, $filter, $params, $err),
			'exposes a fluent interface'
		);

		$this->assertTrue($this->controller->isSatisfiedBy($raw));
		$this->assertFalse($this->controller->isError());
		$this->assertEquals(true, $this->controller->getClean($field));

		$raw = array($field => 'abc');
		$this->assertFalse($this->controller->isSatisfiedBy($raw));
		
		$this->assertNull($this->controller->getClean($field));
		$this->assertTrue($this->controller->isError());

		$error = $this->controller->getError($field);
		$this->assertInstanceOf(
			'Appfuel\Validate\Error',
			$error
		);
		$this->assertEquals($err, $error->current());
		$this->assertEmpty($this->controller->getAllClean());
	}

	/**
	 *  This test we will test multiple fields against different filters
	 *
	 * @return null
	 */
	public function testIsSatisfiedByWithMultipleFields()
	{
		$raw = array(
			'field-a' => 123,
			'field-b' => 'rsb.code@gmail.com',
			'field-c' => 'true',
			'field-d' => '1.234',
			'field-e' => '192.168.1.1'
		);

		$this->controller->addFilter('field-a', 'php-int-filter')
						 ->addFilter('field-b', 'php-email-filter')
						 ->addFilter('field-c', 'php-bool-filter')
						 ->addFilter('field-d', 'php-float-filter')
						 ->addFilter('field-e', 'php-ip-filter');

		$this->assertTrue($this->controller->isSatisfiedBy($raw));
		$this->assertFalse($this->controller->isError());

		/* 
		 * notice that the string true has been changes to the php value true	
		 */
		$expected = array(
            'field-a' => 123,
            'field-b' => 'rsb.code@gmail.com',
            'field-c' => true,
            'field-d' => '1.234',
            'field-e' => '192.168.1.1'
		);

		$this->assertEquals($expected, $this->controller->getAllClean());
		$this->assertEquals(
			$expected['field-a'], 
			$this->controller->getClean('field-a')
		);
			
		$this->assertEquals(
			$expected['field-b'], 
			$this->controller->getClean('field-b')
		);

		$this->assertEquals(
			$expected['field-c'], 
			$this->controller->getClean('field-c')
		);
			
		$this->assertEquals(
			$expected['field-d'], 
			$this->controller->getClean('field-d')
		);

		$this->assertEquals(
			$expected['field-e'], 
			$this->controller->getClean('field-e')
		);
	}

	/**
	 * @return null
	 */
	public function testMultipleInputsOneFailure()
	{
		$raw = array(
			'field-a' => 123,
			'field-b' => 'rsb.code@gmail.com',
			'field-c' => 'true',
			'field-d' => '1.234',
			'field-e' => '192.168.1.1'
		);

		$params = array('min' => 144, 'max' => 155);
		$err = 'field a must be between 144 and 155';
		$this->controller->addFilter('field-a', 'php-int-filter', $params, $err)
						 ->addFilter('field-b', 'php-email-filter')
						 ->addFilter('field-c', 'php-bool-filter')
						 ->addFilter('field-d', 'php-float-filter')
						 ->addFilter('field-e', 'php-ip-filter');


		$this->assertFalse($this->controller->isSatisfiedBy($raw));
		$this->assertTrue($this->controller->isError());

		$expected = array(
			'field-b' => 'rsb.code@gmail.com',
			'field-c' => true,
			'field-d' => '1.234',
			'field-e' => '192.168.1.1'
		);

		$this->assertEquals($expected, $this->controller->getAllClean());
		
		$error = $this->controller->getError('field-a');
		$this->assertInstanceOf(
			'Appfuel\Validate\Error',
			$error
		);

		$this->assertEquals($err, $error->current());
	}

}
