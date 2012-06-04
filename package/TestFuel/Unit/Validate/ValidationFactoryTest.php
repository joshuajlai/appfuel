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
	Appfuel\Validate\ValidationFactory;

class ValidationFactoryTest extends BaseTestCase
{
	/**
	 * @return	null
	 */
	public function setUp()
	{
		parent::setUp();
		$this->backupValidationMap();
		ValidationFactory::clear();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		parent::tearDown();
		ValidationFactory::clear();
		$this->restoreValidationMap();
	}

	/**
	 * @test
	 * @return	null
	 */
	public function addToHandlerMap()
	{
		$key = 'key-a';
		$class = 'ClassA';
		$this->assertNull(ValidationFactory::addToHandlerMap($key, $class));

		$expected = array($key => $class);
		$this->assertEquals($expected, ValidationFactory::getHandlerMap());

		$key2 = 'key-b';
		$class2 = 'ClassB';
		$this->assertNull(ValidationFactory::addToHandlerMap($key2, $class2));
		
		$expected[$key2] = $class2;
		$this->assertEquals($expected, ValidationFactory::getHandlerMap());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function addToHandlerMapKeyFailure($key)
	{
		$msg = 'key in handler category must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::addToHandlerMap($key, 'ClassA');
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function addToHandlerMapClassFailure($class)
	{
		$msg = 'class in handler category must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::addToHandlerMap('key-a', $class);
	}

	/**
	 * @test
	 * @depends	addToHandlerMap
	 * @return	null
	 */
	public function loadHandlerMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		$this->assertNull(ValidationFactory::loadHandlerMap($map));
		$this->assertEquals($map, ValidationFactory::getHandlerMap());

		$map2 = array(
			'key-c' => 'ClassC',
			'key-d' => 'ClassD'
		);
		$this->assertNull(ValidationFactory::loadHandlerMap($map2));

		$expected = array_merge($map, $map2);
		$this->assertEquals($expected, ValidationFactory::getHandlerMap());
	}

	/**
	 * @test
	 * @depends	loadHandlerMap
	 * @return	null
	 */
	public function clearHandlerMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		ValidationFactory::loadHandlerMap($map);
		$this->assertEquals($map, ValidationFactory::getHandlerMap());

		$this->assertNull(ValidationFactory::clearHandlerMap());
		$this->assertEquals(array(), ValidationFactory::getHandlerMap());
	}

	/**
	 * @test
	 * @depends	clearHandlerMap
	 * @return	null
	 */
	public function setHandlerMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		$this->assertNull(ValidationFactory::setHandlerMap($map));
		$this->assertEquals($map, ValidationFactory::getHandlerMap());

		$map2 = array(
			'key-c' => 'ClassC',
			'key-d' => 'ClassD'
		);
		$this->assertNull(ValidationFactory::setHandlerMap($map2));
		$this->assertEquals($map2, ValidationFactory::getHandlerMap());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function addToValidatorMap()
	{
		$key = 'key-a';
		$class = 'ClassA';
		$this->assertNull(ValidationFactory::addToValidatorMap($key, $class));

		$expected = array($key => $class);
		$this->assertEquals($expected, ValidationFactory::getValidatorMap());

		$key2 = 'key-b';
		$class2 = 'ClassB';
		$this->assertNull(ValidationFactory::addToValidatorMap($key2, $class2));
		
		$expected[$key2] = $class2;
		$this->assertEquals($expected, ValidationFactory::getValidatorMap());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function addToValidatorMapKeyFailure($key)
	{
		$msg = 'key in validator category must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::addToValidatorMap($key, 'ClassA');
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function addToValidatorMapClassFailure($class)
	{
		$msg = 'class in validator category must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::addToValidatorMap('key-a', $class);
	}

	/**
	 * Load operation always appends more data onto the list while set will
	 * clear the list before loading it.
	 *
	 * @test
	 * @depends	addToValidatorMap
	 * @return	null
	 */
	public function loadValidatorMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		$this->assertNull(ValidationFactory::loadValidatorMap($map));
		$this->assertEquals($map, ValidationFactory::getValidatorMap());

		$map2 = array(
			'key-c' => 'ClassC',
			'key-d' => 'ClassD'
		);
		$this->assertNull(ValidationFactory::loadValidatorMap($map2));

		$expected = array_merge($map, $map2);
		$this->assertEquals($expected, ValidationFactory::getValidatorMap());
	}

	/**
	 * @test
	 * @depends	loadValidatorMap
	 * @return	null
	 */
	public function clearValidatorMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		ValidationFactory::loadValidatorMap($map);
		$this->assertEquals($map, ValidationFactory::getValidatorMap());

		$this->assertNull(ValidationFactory::clearValidatorMap());
		$this->assertEquals(array(), ValidationFactory::getValidatorMap());
	}

	/**
	 * @test
	 * @depends	clearValidatorMap
	 * @return	null
	 */
	public function setValidatorMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		$this->assertNull(ValidationFactory::setValidatorMap($map));
		$this->assertEquals($map, ValidationFactory::getValidatorMap());

		$map2 = array(
			'key-c' => 'ClassC',
			'key-d' => 'ClassD'
		);
		$this->assertNull(ValidationFactory::setValidatorMap($map2));
		$this->assertEquals($map2, ValidationFactory::getValidatorMap());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function addToFilterMap()
	{
		$key = 'key-a';
		$class = 'ClassA';
		$this->assertNull(ValidationFactory::addToFilterMap($key, $class));

		$expected = array($key => $class);
		$this->assertEquals($expected, ValidationFactory::getFilterMap());

		$key2 = 'key-b';
		$class2 = 'ClassB';
		$this->assertNull(ValidationFactory::addToFilterMap($key2, $class2));
		
		$expected[$key2] = $class2;
		$this->assertEquals($expected, ValidationFactory::getFilterMap());
	}

	/**
	 * @test
	 * @depends	addToFilterMap
	 * @return	null
	 */
	public function loadFilterMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		$this->assertNull(ValidationFactory::loadFilterMap($map));
		$this->assertEquals($map, ValidationFactory::getFilterMap());

		$map2 = array(
			'key-c' => 'ClassC',
			'key-d' => 'ClassD'
		);
		$this->assertNull(ValidationFactory::loadFilterMap($map2));

		$expected = array_merge($map, $map2);
		$this->assertEquals($expected, ValidationFactory::getFilterMap());
	}

	/**
	 * @test
	 * @depends	loadFilterMap
	 * @return	null
	 */
	public function clearFilterMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		ValidationFactory::loadFilterMap($map);
		$this->assertEquals($map, ValidationFactory::getFilterMap());

		$this->assertNull(ValidationFactory::clearFilterMap());
		$this->assertEquals(array(), ValidationFactory::getFilterMap());
	}

	/**
	 * @test
	 * @depends	clearFilterMap
	 * @return	null
	 */
	public function setFilterMap()
	{
		$map = array(
			'key-a' => 'ClassA',
			'key-b' => 'ClassB'
		);
		$this->assertNull(ValidationFactory::setFilterMap($map));
		$this->assertEquals($map, ValidationFactory::getFilterMap());

		$map2 = array(
			'key-c' => 'ClassC',
			'key-d' => 'ClassD'
		);
		$this->assertNull(ValidationFactory::setFilterMap($map2));
		$this->assertEquals($map2, ValidationFactory::getFilterMap());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function addToFilterMapKeyFailure($key)
	{
		$msg = 'key in filter category must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::addToFilterMap($key, 'ClassA');
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function addToFilterMapClassFailure($class)
	{
		$msg = 'class in filter category must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		ValidationFactory::addToFilterMap('key-a', $class);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function validationMap()
	{
		$map = array(
			'validator' => array(
				'key-a' => 'ClassA',
				'key-b' => 'ClassB',
			),
			'filter' => array(
				'key-c' => 'ClassC',
				'key-d' => 'ClassD'
			),
			'handler' => array(
				'key-e' => 'ClassE'
			),
		);
		$expected = array(
			'handler'   => array(),
			'validator' => array(), 
			'filter'	=> array()
		);
		$this->assertEquals($expected, ValidationFactory::getMap());
		$this->assertNull(ValidationFactory::setMap($map));

		$this->assertEquals($map, ValidationFactory::getMap());
		$this->assertNull(ValidationFactory::clear());
		$this->assertEquals($expected, ValidationFactory::getMap());
	}

	/**
	 * @test
	 * @depends	validationMap
	 * @return	null
	 */
	public function map()
	{
		$map = array(
			'validator' => array(
				'key-a' => 'ClassA',
				'key-b' => 'ClassB',
			),
			'filter' => array(
				'key-c' => 'ClassC',
				'key-d' => 'ClassD'
			),
			'handler' => array(
				'key-e' => 'ClassE'
			),
		);
		ValidationFactory::setMap($map);

		$this->assertEquals(
			'ClassA', 
			ValidationFactory::map('validator', 'key-a')
		);

		$this->assertEquals(
			'ClassB', 
			ValidationFactory::map('validator', 'key-b')
		);

		$this->assertEquals(
			'ClassC', 
			ValidationFactory::map('filter', 'key-c')
		);

		$this->assertEquals(
			'ClassD', 
			ValidationFactory::map('filter', 'key-d')
		);

		$this->assertEquals(
			'ClassE', 
			ValidationFactory::map('handler', 'key-e')
		);

		$this->assertFalse(ValidationFactory::map('no-category', 'key-b'));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function mapCategoryFailures($cat)
	{
		$map = array(
			'validator' => array(
				'key-a' => 'ClassA',
				'key-b' => 'ClassB',
			),
			'filter' => array(
				'key-c' => 'ClassC',
				'key-d' => 'ClassD'
			),
		);
		ValidationFactory::setMap($map);

		$this->assertFalse(ValidationFactory::map($cat, 'key-b'));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function mapKeyFailures($key)
	{
		$map = array(
			'validator' => array(
				'key-a' => 'ClassA',
				'key-b' => 'ClassB',
			),
			'filter' => array(
				'key-c' => 'ClassC',
				'key-d' => 'ClassD'
			),
		);
		ValidationFactory::setMap($map);

		$this->assertFalse(ValidationFactory::map('filter', $key));
	}

	/**
	 * @test
	 * @depends	map
	 * @return	null
	 */
	public function create()
	{
		$map = array(
			'validator' => array(
				'key-a' => 'StdClass',
			),
			'filter' => array(
				'key-b' => 'StdClass',
			),
			'handler' => array(
				'key-c' => 'StdClass',
			),
		);
		ValidationFactory::setMap($map);

		$obj = ValidationFactory::create('validator', 'key-a');
		$this->assertInstanceOf('StdClass', $obj);
	
		$obj = ValidationFactory::create('filter', 'key-b');
		$this->assertInstanceOf('StdClass', $obj);
		
		$obj = ValidationFactory::create('handler', 'key-c');
		$this->assertInstanceOf('StdClass', $obj);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createNotMappedFailure()
	{
		$msg = 'could not create object: could not map -(validator, not-there)';
		$this->setExpectedException('DomainException', $msg);
		$obj = ValidationFactory::create('validator', 'not-there');
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createHandlerNoKey()
	{
		$obj = ValidationFactory::createHandler();
		$this->assertInstanceOf('Appfuel\Validate\ValidationHandler', $obj);

		$coord = $this->getMock('Appfuel\Validate\CoordinatorInterface');
		$obj = ValidationFactory::createHandler(null, $coord);
		$this->assertInstanceOf('Appfuel\Validate\ValidationHandler', $obj);
		$this->assertSame($coord, $obj->getCoordinator());
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createHandlerWithKey()
	{
		$key   = 'handler';
		$class = 'Testfuel\Functional\Validate\MockHandler';
		ValidationFactory::addToHandlerMap($key, $class);
 
		$obj = ValidationFactory::createHandler($key);
		$this->assertInstanceOf($class, $obj);
		
		$coord = $this->getMock('Appfuel\Validate\CoordinatorInterface');
		$obj = ValidationFactory::createHandler($key, $coord);
		$this->assertInstanceOf($class, $obj);
		$this->assertSame($coord, $obj->getCoordinator());
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createHandlerWithKeyObjFailure()
	{
		$key   = 'handler';
		$class = 'StdClass';
		ValidationFactory::addToHandlerMap($key, $class);
		
		$msg  = 'handler -(handler,StdClass) does not implment ';
		$msg .= '-(Appfuel\Validate\ValidationHandlerInterface)';
		$this->setExpectedException('DomainException', $msg); 
		$obj = ValidationFactory::createHandler($key);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createHandlerNotMappedFailure()
	{
		$key = 'handler';
		$msg  = 'could not map validation handler with -(handler)';
		$this->setExpectedException('DomainException', $msg); 
		$obj = ValidationFactory::createHandler($key);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createCoordinatorNoKey()
	{
		$obj = ValidationFactory::createCoordinator();
		$this->assertInstanceOf('Appfuel\Validate\Coordinator', $obj);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createCoordinatorWithKey()
	{
		$key   = 'coordinator';
		$class = 'Testfuel\Functional\Validate\MockCoordinator';
		ValidationFactory::addToValidatorMap($key, $class);
 
		$obj = ValidationFactory::createCoordinator($key);
		$this->assertInstanceOf($class, $obj);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createCoordinatorWithKeyObjFailure()
	{
		$key   = 'coordinator';
		$class = 'StdClass';
		ValidationFactory::addToValidatorMap($key, $class);

		$msg  = 'coordinator -(coordinator,stdClass) does not implment '; 
		$msg .= '-(Appfuel\Validate\CoordinatorInterface)';
		$this->setExpectedException('DomainException', $msg); 
		$obj = ValidationFactory::createCoordinator($key);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createValidator()
	{
		$key   = 'field-validator';
		$class = 'Testfuel\Functional\Validate\MockValidator';
		ValidationFactory::addToValidatorMap($key, $class);
 
		$obj = ValidationFactory::createValidator($key);
		$this->assertInstanceOf($class, $obj);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createValidatorWithKeyObjFailure()
	{
		$key   = 'field-validator';
		$class = 'StdClass';
		ValidationFactory::addToValidatorMap($key, $class);

		$msg  = 'validator -(field-validator, stdClass) must implement ';
		$msg .= '-(Appfuel\Validate\ValidatorInterface)';
		$this->setExpectedException('DomainException', $msg); 
		$obj = ValidationFactory::createValidator($key);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createFilter()
	{
		$key   = 'my-int';
		$class = 'Testfuel\Functional\Validate\MockFilter';
		ValidationFactory::addToFilterMap($key, $class);
 
		$obj = ValidationFactory::createFilter($key);
		$this->assertInstanceOf($class, $obj);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createFilterObjFailure()
	{
		$key   = 'my-int';
		$class = 'StdClass';
		ValidationFactory::addToFilterMap($key, $class);

		$msg  = 'filter -(my-int, stdClass) must implement ';
		$msg .= '-(Appfuel\Validate\Filter\FilterInterface)';
		$this->setExpectedException('DomainException', $msg); 
		$obj = ValidationFactory::createFilter($key);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createDefaultFilterSpec()
	{	
		$data = array(
			'name' => 'int',
			'options' => array('max' => 100),
			'error' => 'my error'
		);
		$class = 'Appfuel\Validate\Filter\FilterSpec';
		$obj = ValidationFactory::createFilterSpec($data);
		$this->assertInstanceOf($class, $obj);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createFilterSpecWithKey()
	{
		$key   = 'my-spec';
		$class = 'Testfuel\Functional\Validate\MockFilterSpec';
		ValidationFactory::addToFilterMap($key, $class);
 
		$data = array(
			'name' => 'int',
			'options' => array('max' => 100),
			'error' => 'my error'
		);
		$obj = ValidationFactory::createFilterSpec($data, $key);
		$this->assertInstanceOf($class, $obj);
	}

	/**
	 * @test
	 * @depends	create
	 * @return	null
	 */
	public function createFilterSpecObjFailure()
	{
		$key   = 'my-spec';
		$class = 'StdClass';
		ValidationFactory::addToFilterMap($key, $class);

		$data = array(
			'name' => 'int',
			'options' => array('max' => 100),
			'error' => 'my error'
		);

		$msg  = 'filter spec -(my-spec, StdClass) must implement ';
		$msg .= '-(Appfuel\Validate\Filter\FilterSpecInterface)';
		$this->setExpectedException('DomainException', $msg); 
		
		$obj = ValidationFactory::createFilterSpec($data, $key);
	}



}
