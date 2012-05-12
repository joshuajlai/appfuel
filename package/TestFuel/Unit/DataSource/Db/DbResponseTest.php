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
namespace TestFuel\Unit\DataSource\Db;

use StdClass,
	Appfuel\Error\ErrorStack,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataSource\Db\DbResponse;

/**
 */
class DbResponseTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DbResponse
	 */
	protected $response = null;

	/**
	 * ErrorStack injected, used to test error support
	 * @var ErrorStack
	 */	
	protected $errorStack = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->errorStack = new ErrorStack();
		$this->response   = new DbResponse($this->errorStack);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->errorStack = null;
		$this->response = null;
	}

	/**
	 * Test immutable objects and interface
	 *
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\DataSource\Db\DbResponseInterface',
			$this->response
		);
	}

	/**
	 * The only way to set the error stack is through the constructor. If
	 * you don't set a default will be created for you.
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetErrorStack()
	{
		$this->assertSame($this->errorStack, $this->response->getErrorStack());
		
		$stack = $this->getMock('Appfuel\Error\ErrorStackInterface');
		$response = new DbResponse($stack);
		$this->assertSame($stack, $response->getErrorStack());	
	
		/* when no error stack is given the response will create a
		 * Appfuel\Error\ErrorStack
		 */
		$response = new DbResponse();
		$this->assertInstanceOf(
			'Appfuel\Error\ErrorStack', 
			$response->getErrorStack()
		);	
	}

	/**
	 * This method delegates to the error stacks isError, so we will test
	 * by manipulating the error stack injected during setUp
	 *
	 * @depends	testGetSetErrorStack
	 * @return	null
	 */
	public function testIsError()
	{
		/* make sure the error stack has no errors */
		$this->assertFalse($this->errorStack->isError());
		$this->assertFalse($this->response->isError());

		$this->errorStack->addError('my error');
		$this->assertTrue($this->errorStack->isError());
		$this->assertTrue($this->response->isError());
	}

	/**
	 * This method also delegates to the error stacks iterator interface 
	 * ErrorStack::current() for the current error
	 *
	 * @depends	testGetSetErrorStack
	 * @return	null
	 */
	public function testGetCurrentError()
	{
		/* prove no error exist so current will return false */
		$this->assertFalse($this->errorStack->current());
		$this->assertFalse($this->response->current());
	
		/* add an error to the error stack */
		$this->errorStack->addError('my first error')
						 ->addError('my second error');
		
		$current = $this->errorStack->current();
		$this->assertSame($current, $this->response->getError());

		$this->errorStack->next();
		$current = $this->errorStack->current();
		$this->assertSame($current, $this->response->getError());
	}

	/**
	 * A result set must be an array and each item in the resultset
	 * must be an array or another DbResponseInterface.
	 *
	 * It is valid to set an empty array
	 * The contents are not validated during set. On very large datasets
	 * this would not be efficient.
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetResultSet()
	{
		$results = array(
			array('id' => 12345),
			array('id' => 54321)
		);
		$this->assertSame(
			$this->response,
			$this->response->setResultSet($results)
		);

		$this->assertEquals($results, $this->response->getResultSet());
		$this->assertEquals(2, $this->response->count());

		$this->assertSame(
			$this->response,
			$this->response->setResultSet(array())
		);
		$this->assertEquals(array(), $this->response->getResultSet());
		$this->assertEquals(0, $this->response->count());

		/* an invalid result set */
		$results = array(1,2,3,4);

		$this->assertSame(
			$this->response,
			$this->response->setResultSet($results)
		);
		$this->assertEquals($results, $this->response->getResultSet());
	}

	/**
	 * Add a result when no results have been set and the key automatically 
	 * added. When no results exist and a result is added with addResult
	 * and no key is specified then the key assigned will be 0
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddResultEmptyIndexedNoKey()
	{
		$this->assertEquals(0, $this->response->count());
		$this->assertEquals(array(), $this->response->getResultSet());

		$result = array('id' => 1, 'name' => 'bob');
		$this->assertSame(
			$this->response, 
			$this->response->addResult($result)
		);

		$expected = array(0 => $result);
		$this->assertEquals($expected, $this->response->getResultSet());
	}

	/**
	 * @return	array
	 */
	public function provideValidResultKeys()
	{
		$key = 'my-key';
		return array(
			array(null,		0),
			array(0,		0),
			array(99,		99),
			array(-99,		-99),
			array($key,			$key),
			array(" $key ",		$key),
			array("\t$key",		$key),
			array("\n$key",		$key),
			array("$key ",		$key),
			array("$key\t",		$key),
			array("$key\n",		$key),
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidResultKeys()
	{
		return array(
			array(''),
			array(' '),
			array("\t"),
			array("\n"),
			array(" \n\t"),
			array(array(1,2,3)),
			array(new StdClass()),
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidResults()
	{
		return	array(
				array(null),
				array(''),
				array(0),
				array('this is a stiring'),
				array(12345),
				array(1.23454),
				array(true),
				array(false),
				array(new StdClass())
		);
	}

	/**
	 * This test show what happens when no results exist and you added on
	 * and specify your own key as an integer
	 *
	 * @dataProvider	provideValidResultKeys
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddResultArrayWithKey($key, $expectedKey)
	{
		$this->assertEquals(0, $this->response->count());
		$this->assertEquals(array(), $this->response->getResultSet());

		$result = array('id' => 1, 'name' => 'bob');
		$this->assertSame(
			$this->response, 
			$this->response->addResult($result, $key)
		);

		$expected = array($expectedKey => $result);
		$this->assertEquals($expected, $this->response->getResultSet());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddResultResponseInterface()
	{
		$this->assertEquals(0, $this->response->count());
		$this->assertEquals(array(), $this->response->getResultSet());

		$result = $this->getMock('Appfuel\DataSource\Db\DbResponseInterface');
		$this->assertSame(
			$this->response, 
			$this->response->addResult($result)
		);

		$expected = array(0 => $result);
		$this->assertEquals($expected, $this->response->getResultSet());
	}


	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidResultKeys
	 * @return				null
	 */
	public function testAddResultKey_Failure($key)
	{
		$this->response->addResult(array('id' => 123), $key);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidResults
	 * @return				null
	 */
	public function testAddResultInvalidResult_Failure($result)
	{
		$this->response->addResult($result);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIterator()
	{
		$resultset = array(
			array('id' => 1),
			array('id' => 2),
			array('id' => 3),
		);

		$this->response->setResultSet($resultset);
		$this->assertEquals(3, $this->response->count());

		$this->assertEquals(0, 	$this->response->key());
		$this->assertTrue($this->response->valid());
		$this->assertEquals($resultset[0], $this->response->current());

		$this->assertNull($this->response->next());
		$this->assertEquals(1, 	$this->response->key());
		$this->assertTrue($this->response->valid());
		$this->assertEquals($resultset[1], $this->response->current());

		$this->assertNull($this->response->next());
		$this->assertEquals(2, 	$this->response->key());
		$this->assertTrue($this->response->valid());
		$this->assertEquals($resultset[2], $this->response->current());

		/* out of range */
		$this->assertNull($this->response->next());
		$this->assertNull($this->response->key());
		$this->assertFalse($this->response->valid());
		$this->assertFalse($this->response->current());

		/* rewind to the beginning */
		$this->assertNull($this->response->rewind());
		$this->assertEquals(0, 	$this->response->key());
		$this->assertTrue($this->response->valid());
		$this->assertEquals($resultset[0], $this->response->current());
	}

	/**
	 * When used with multi query the many results will be an array
	 * of response objects
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIteratorResponses()
	{
		$interface = 'Appfuel\DataSource\Db\DbResponseInterface';
		$response1 = $this->getMock($interface);
		$response2 = $this->getMock($interface);
		$response3 = $this->getMock($interface);

		$resultset = array(
			$response1,
			$response2,
			$response3
		);

		$this->response->setResultSet($resultset);
		$this->assertEquals(3, $this->response->count());

		$this->assertEquals(0, 	$this->response->key());
		$this->assertTrue($this->response->valid());
		$this->assertEquals($resultset[0], $this->response->current());

		$this->assertNull($this->response->next());
		$this->assertEquals(1, 	$this->response->key());
		$this->assertTrue($this->response->valid());
		$this->assertEquals($resultset[1], $this->response->current());

		$this->assertNull($this->response->next());
		$this->assertEquals(2, 	$this->response->key());
		$this->assertTrue($this->response->valid());
		$this->assertEquals($resultset[2], $this->response->current());

		/* out of range */
		$this->assertNull($this->response->next());
		$this->assertNull($this->response->key());
		$this->assertFalse($this->response->valid());
		$this->assertFalse($this->response->current());

		/* rewind to the beginning */
		$this->assertNull($this->response->rewind());
		$this->assertEquals(0, 	$this->response->key());
		$this->assertTrue($this->response->valid());
		$this->assertEquals($resultset[0], $this->response->current());
	}
}
