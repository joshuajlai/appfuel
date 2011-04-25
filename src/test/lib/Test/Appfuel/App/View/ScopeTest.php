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
namespace Test\Appfuel;

use Test\AfTestCase as ParentTestCase,
	Appfuel\App\View\Scope,
	StdClass,
	SplFileInfo;

/**
 * Scope is an object that binds the data which is held by the scope object 
 * with the template file which holds markup, text or any other type of
 * information that the data in scope will be used with. The template file
 * which is a phtml, pcss or pjs can use the scopes interface though $this
 * because scope is bound to that file once you use the scope build.
 */
class ScopeTest extends ParentTestCase
{
	/**
	 * Back up the data in the registry
	 * @var string
	 */
	protected $scope = null;

	/**
	 * Backup the registry data then initialize it with an empty bag
	 * @return null
	 */
	public function setUp()
	{
		$this->scope = new Scope();
	}

	/**
	 * Restore the original registry data
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->scope);
	}

	/**
	 * When no data is given in the constructor then the count is 0 and 
	 * getAll will return an empty array. Basically no data will be in scope
	 *
	 * @return null
	 */
	public function testConstructorNoData()
	{
		$this->assertEquals(0, $this->scope->count());
		
		$result = $this->scope->getAll();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
	}

	/**
	 * The normal way to use scope is to pass data into the constructor. 
	 * After build is called that data is made available to the template
	 * file via $this. Count will return the number of items in scope and
	 * get will retrieve an item from scope. Exist will tell you if a 
	 * certain key is in scope.
	 *
	 * @return null
	 */
	public function testCountExistGetGetAll()
	{
		$data = array(
			'string' => 'this is a string',
			'number' => 12345,
			'float'  => 1.2345,
			'array'  => array(1,2,3,4,5),
			'object' => new StdClass()
		);

		$scope = new Scope($data);
		$this->assertEquals(count($data), $scope->count());

		$this->assertEquals($data, $scope->getAll());
		foreach ($data as $key => $value) {
			$this->assertTrue($scope->exists($key));
			$this->assertEquals($value, $scope->get($key));
		}

		/* test for things known not to exist */
		$this->assertFalse($scope->exists('does_not_exist'));
		
		/* test return value of get */
		$this->assertNull($scope->get('does_not_exist'));

		$default = false;
		$this->assertFalse($scope->get('does_not_exist', $default));
			
		$default = 'I am default string';
		$this->assertEquals($default, $scope->get('does_not_exist', $default));

		$default = array(1,2,3,4,5);
		$this->assertEquals($default, $scope->get('does_not_exist', $default));
		
		$default = new StdClass();;
		$this->assertEquals($default, $scope->get('does_not_exist', $default));	
	}

	/**
	 * Since the data in scope is kept in an array the method get can only
	 * use scalar values.
	 *
	 * @return null
	 */
	public function testGetExistWithBadKey()
	{
		/* 
		 * show that even though php allows a null as a valid array key
		 * we don't
		 */
		$data = array(null => 1);
		$scope = new Scope($data);
		
		$key = array();
		$this->assertFalse($scope->exists($key));
		$this->assertNull($scope->get($key));

		$key = new StdClass();
		$this->assertFalse($scope->exists($key));
		$this->assertNull($scope->get($key));

		$key = null;
		$this->assertFalse($scope->exists($key));
		$this->assertNull($scope->get($key));
	}

	/**
	 * @return null
	 */
	public function testRenderstring()
	{
		$data = array(
			'string' => 'this is a string',
		);

		$scope = new scope($data);

		$this->expectOutputString('this is a string');
		$scope->render('string');
	}

	/**
	 * @return null
	 */
	public function testRenderNumber()
	{
		$data = array(
			'number' => 12345,
		);

		$scope = new scope($data);

		$this->expectOutputString('12345');
		$scope->render('number');
	}

	/**
	 * @return null
	 */
	public function testRenderFloat()
	{
		$data = array(
			'float' => 1.2345,
		);

		$scope = new scope($data);

		$this->expectOutputString('1.2345');
		$scope->render('float');
	}

	/**
	 * The third parameter of render is the separator used to to implode
	 * an array into a string. The default value for this parameter is ' '
	 *
	 * @return null
	 */
	public function testRenderArrayDefaultSep()
	{
		$data = array(
			'array' => array(1,2,3,4),
		);

		$scope = new scope($data);

		$this->expectOutputString('1 2 3 4');
		$scope->render('array');
	}
	
	/**
	 * @return null
	 */
	public function testRenderArraySep()
	{
		$data = array(
			'array' => array(1,2,3,4),
		);

		$scope = new scope($data);

		$this->expectOutputString('1:2:3:4');
		$scope->render('array', '', ':');
	}
	
	/**
	 * render an object that does not support a __toString will output
	 * an empty string
	 *
	 * @return null
	 */
	public function testRenderObjectWithoutToString()
	{
		$data = array(
			'object' => new StdClass(),
		);

		$scope = new scope($data);

		$this->expectOutputString('');
		$scope->render('object');
	}

	/**
	 * render an object that does support __toString will output
	 * __toString
	 *
	 * @return null
	 */
	public function testRenderObjectWithToString()
	{
		$data = array(
			'object' => new SplFileInfo('/some/class'),
		);

		$scope = new scope($data);

		$this->expectOutputString('/some/class');
		$scope->render('object');
	}

	/**
	 * Test render will echo an empty string when the item was not found
	 * @return null
	 */
	public function testRenderDefaultNotFound()
	{
		$scope = new scope();

		$this->expectOutputString('');
		$scope->render('notFound');
	}

	/**
	 * When render can not find a key it will render the default parameter
	 * specified. If that parameter is an array it will implode on using the
	 * separator given in the third parameter. The default value for the 
	 * separator is ' '
	 *
	 * @return null
	 */
	public function testRenderDefaultArrayNotFound()
	{
		$scope = new scope();

		$default = array(1,2,3);
		$this->expectOutputString('1 2 3');
		$scope->render('notFound', $default);
	}

	/**
	 * Test the separator for arrays with default 
	 * @return null
	 */
	public function testRenderDefaultArraySepNotFound()
	{
		$scope = new scope();

		$default = array(1,2,3);
		$this->expectOutputString('1:2:3');
		$scope->render('notFound', $default, ':');
	}

	/**
	 * When the default value is an object that does not support a __toString
	 * then an empty string will be rendered 
	 *
	 * @return null
	 */
	public function testRenderDefaultObjectNotFound()
	{
		$scope = new scope();

		$default = new StdClass();
		$this->expectOutputString('');
		$scope->render('notFound', $default);
	}

	/**
	 * Default object that supports __toString
	 * @return null
	 */
	public function testRenderDefaultObjectSupportToStringNotFound()
	{
		$scope = new scope();

		$default = new SplFileInfo('/some/class');
		$this->expectOutputString('/some/class');
		$scope->render('notFound', $default);
	}
}

