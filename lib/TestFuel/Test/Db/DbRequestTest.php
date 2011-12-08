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
namespace TestFuel\Test\Db\Request;

use StdClass,
	Appfuel\Db\DbRequest,
	TestFuel\TestCase\BaseTestCase;

/**
 * The query request carries information for the handler and the handler's
 * adapter. 
 */
class DbRequestTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	QueryRequest
	 */
	protected $request = null;

	public function setUp()
	{
		$this->request = new DbRequest();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->request = null;
	}

	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Db\DbRequestInterface',
			$this->request
		);
	}
	
	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetType()
	{
		/* default value */
		$this->assertEquals('query', $this->request->getType());
		
		$this->assertSame(
			$this->request, 
			$this->request->setType('multi-query')
		);
		$this->assertEquals('multi-query', $this->request->getType());
		$this->assertSame(
			$this->request, 
			$this->request->setType('MULTI-QUERY')
		);
		$this->assertEquals('multi-query', $this->request->getType());

		$this->assertSame(
			$this->request, 
			$this->request->setType('prepared-stmt')
		);
		$this->assertEquals('prepared-stmt', $this->request->getType());

		$this->assertSame(
			$this->request, 
			$this->request->setType('PREPARED-Stmt')
		);
		$this->assertEquals('prepared-stmt', $this->request->getType());


		$this->assertSame(
			$this->request, 
			$this->request->setType('query')
		);
		$this->assertEquals('query', $this->request->getType());

		$this->assertSame(
			$this->request, 
			$this->request->setType('QUERY')
		);
		$this->assertEquals('query', $this->request->getType());
	}

	/**
	 * @depends				testInterface
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return null
	 */
	public function testSetTypeNotString_Failure($type)
	{
		$this->request->setType($type);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetStrategy()
	{
		/* default value */
		$this->assertEquals('write', $this->request->getStrategy());
		
		$this->assertSame($this->request, $this->request->setStrategy('read'));
		$this->assertEquals('read', $this->request->getStrategy());
	
		$this->assertSame($this->request, $this->request->setStrategy('READ'));
		$this->assertEquals('read', $this->request->getStrategy());
	
		$this->assertSame(
			$this->request, 
			$this->request->setStrategy('read-write')
		);
		$this->assertEquals('read-write', $this->request->getStrategy());

		$this->assertSame(
			$this->request, 
			$this->request->setStrategy('read-WRITE')
		);
		$this->assertEquals('read-write', $this->request->getStrategy());


		$this->assertSame($this->request, $this->request->setStrategy('write'));
		$this->assertEquals('write', $this->request->getStrategy());
	
		$this->assertSame($this->request, $this->request->setStrategy('WRITE'));
		$this->assertEquals('write', $this->request->getStrategy());
	}

	/**
	 * @depends				testInterface
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return null
	 */
	public function testSetStategyNotString_Failure($strategy)
	{
		$this->request->setStrategy($strategy);
	}

	/**
	 * @depends				testInterface
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetStategyNotInSet_Failure()
	{
		$this->request->setStrategy('not-write-read-or-both');
	}

	/**
	 * @depends	testGetSetStrategy
	 * @return	null
	 */
	public function testEnableReadOnly()
	{
		/* set to something not that is not read */
		$this->request->setStrategy('read-write');
		$this->assertEquals('read-write', $this->request->getStrategy());

		$this->assertSame($this->request, $this->request->enableReadOnly());
		$this->assertEquals('read', $this->request->getStrategy());

		/* nothing happens when alread read */
		$this->assertSame($this->request, $this->request->enableReadOnly());
		$this->assertEquals('read', $this->request->getStrategy());
	}

	/**
	 * @depends	testGetSetStrategy
	 * @return	null
	 */
	public function testEnableWrite()
	{
		$this->request->setStrategy('read-write');
		$this->assertEquals('read-write', $this->request->getStrategy());

		$this->assertSame($this->request, $this->request->enableWrite());
		$this->assertEquals('write', $this->request->getStrategy());

		/* nothing happens when alread write */
		$this->assertSame($this->request, $this->request->enableWrite());
		$this->assertEquals('write', $this->request->getStrategy());
	}

	/**
	 * @depends	testGetSetStrategy
	 * @return	null
	 */
	public function testEnableReadWrite()
	{
		$this->request->setStrategy('read');
		$this->assertEquals('read', $this->request->getStrategy());

		$this->assertSame($this->request, $this->request->enableReadWrite());
		$this->assertEquals('read-write', $this->request->getStrategy());

		/* nothing happens when alread read-write */
		$this->assertSame($this->request, $this->request->enableReadWrite());
		$this->assertEquals('read-write', $this->request->getStrategy());
	}

	/**
	 * @return null
	 */
	public function testGetSetSql()
	{
		/* default value */
		$this->assertNull($this->request->getSql());

		$sql = 'SELECT * FROM some_table WHERE id=some_number';
		$this->assertSame($this->request, $this->request->setSql($sql));

		$this->assertEquals($sql, $this->request->getSql());
	}

	/**
	 * @depends				testInterface
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return null
	 */
	public function testSetSqlNotString_Failure($sql)
	{
		$this->request->setSql($sql);
	}

	/**
	 * @return null
	 */
	public function testGetSetResultType()
	{
		/* default value */
		$this->assertEquals('name', $this->request->getResultType());

		$this->assertSame(
			$this->request, 
			$this->request->setResultType('position')
		);
		$this->assertEquals('position', $this->request->getResultType());
	
		$this->assertSame(
			$this->request, 
			$this->request->setResultType('POSITION')
		);
		$this->assertEquals('position', $this->request->getResultType());
			
		$this->assertSame(
			$this->request, 
			$this->request->setResultType('name-pos')
		);
		$this->assertEquals('name-pos', $this->request->getResultType());
		$this->assertSame(
			$this->request, 
			$this->request->setResultType('NAME-pos')
		);
		$this->assertEquals('name-pos', $this->request->getResultType());


		$this->assertSame(
			$this->request, 
			$this->request->setResultType('name')
		);
		$this->assertEquals('name', $this->request->getResultType());
		$this->assertSame(
			$this->request, 
			$this->request->setResultType('NAME')
		);
		$this->assertEquals('name', $this->request->getResultType());
	}

	/**
	 * @depends				testGetSetResultType
	 * @expectedException	InvalidArgumentException	
	 * @return null
	 */
	public function testSetResultTypeInvalidNoInWhiteList()
	{
		$this->request->setResultType('not-read-or-write-or-both');
	}

	/**
	 * @depends				testGetSetResultType
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return null
	 */
	public function testSetResutTypeNotString_Failure($resultType)
	{
		$this->request->setResultType($resultType);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testEnableDisableIsResultBuffer()
	{
		$this->assertTrue($this->request->isResultBuffer());
		$this->assertSame(
			$this->request, 
			$this->request->disableResultBuffer()
		);
		$this->assertFalse($this->request->isResultBuffer());

		$this->assertSame(
			$this->request, 
			$this->request->enableResultBuffer()
		);
		$this->assertTrue($this->request->isResultBuffer());
	}

	/**
	 * Used only to test saving callbacks
	 *
	 * @param	$raw
	 * @return	mixed
	 */
	public function my_callback($raw) 
	{
		return $raw;
	}

	/**
	 * @return null
	 */
	public function testGetSetCallbackArray()
	{
		$this->assertNull($this->request->getCallback());
		
		$callback = array($this, 'my_callback');	
		$this->assertSame(
			$this->request,
			$this->request->setCallback($callback)
		);

		$this->assertSame($callback, $this->request->getCallback());
	}

	/**
	 * @return null
	 */
	public function testGetSetCallbackString()
	{
		$this->assertNull($this->request->getCallback());
		
		$callback = __CLASS__ . '::my_callback' ;	
		$this->assertSame(
			$this->request,
			$this->request->setCallback($callback)
		);

		$this->assertSame($callback, $this->request->getCallback());
	}

	/**
	 * @return null
	 */
	public function testGetSetCallbackClosure()
	{
		$this->assertNull($this->request->getCallback());
		
		$callback = function($raw) {
			return $raw;
		};

		$this->assertSame(
			$this->request,
			$this->request->setCallback($callback)
		);

		$this->assertSame($callback, $this->request->getCallback());
	}

	/**
	 * @depends				testInterface
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return null
	 */
	public function testSetCallBackNotCallable_Failure($callback)
	{
		$this->request->setResultType($callback);
	}
}
