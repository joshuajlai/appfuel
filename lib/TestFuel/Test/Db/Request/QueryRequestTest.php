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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Request\QueryRequest;

/**
 * The query request carries information for the handler and the handler's
 * adapter. 
 */
class QueryRequestTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	QueryRequest
	 */
	protected $request = null;

    /**
     * Type of db operation
     * @var string
     */
    protected $opType = null;

	public function setUp()
	{
		$this->opType  = 'read';
		$this->request = new QueryRequest($this->opType);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->request);
	}

	/**
	 * @return null
	 */
	public function testGetSetType()
	{
		/* default value */
		$this->assertEquals('read', $this->request->getType());

		$this->assertSame($this->request, $this->request->setType('write'));
		$this->assertEquals('write', $this->request->getType());
		
		$this->assertSame($this->request, $this->request->setType('both'));
		$this->assertEquals('both', $this->request->getType());

		$this->assertSame($this->request, $this->request->setType('read'));
		$this->assertEquals('read', $this->request->getType());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTypeInvalidNoInWhiteList()
	{
		$this->request->setType('not-read-or-write-or-both');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTypeInvalidEmptyString()
	{
		$this->request->setType('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTypeInvalidObject()
	{
		$this->request->setType(new stdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTypeInvalidArray()
	{
		$this->request->setType(array(1,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTypeInvalidInt()
	{
		$this->request->setType(1234);
	}


	/**
	 * Convience method to setType to read used for replication to determine
	 * slave db server
	 * 
	 * @return null
	 */
	public function testEnableReadOnly()
	{
		$this->request->setType('both');
		$this->assertEquals('both', $this->request->getType());

		$this->assertSame($this->request, $this->request->enableReadOnly());
		$this->assertEquals('read', $this->request->getType());

		/* nothing happens when alread read */
		$this->assertSame($this->request, $this->request->enableReadOnly());
		$this->assertEquals('read', $this->request->getType());
	}

	/**
	 * Convience method to setType to write used for replication to determine
	 * master db server
	 * 
	 * @return null
	 */
	public function testEnableWrite()
	{
		$this->request->setType('both');
		$this->assertEquals('both', $this->request->getType());

		$this->assertSame($this->request, $this->request->enableWrite());
		$this->assertEquals('write', $this->request->getType());

		/* nothing happens when alread read */
		$this->assertSame($this->request, $this->request->enableWrite());
		$this->assertEquals('write', $this->request->getType());
	}

	/**
	 * Convience method to setType to both used for replication to determine
	 * master db server because request has both read and writes in it
	 * 
	 * @return null
	 */
	public function testEnableReadWrite()
	{
		$this->request->setType('read');
		$this->assertEquals('read', $this->request->getType());

		$this->assertSame($this->request, $this->request->enableReadWrite());
		$this->assertEquals('both', $this->request->getType());

		/* nothing happens when alread read */
		$this->assertSame($this->request, $this->request->enableReadWrite());
		$this->assertEquals('both', $this->request->getType());
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
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetSqlInvalidEmptyString()
	{
		$this->request->setSql('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetSqlInvalidObject()
	{
		$this->request->setSql(new stdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetSqlInvalidArray()
	{
		$this->request->setSql(array(1,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetSqlInvalidInt()
	{
		$this->request->setSql(1234);
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
			$this->request->setResultType('both')
		);
		$this->assertEquals('both', $this->request->getResultType());

		$this->assertSame(
			$this->request, 
			$this->request->setResultType('name')
		);
		$this->assertEquals('name', $this->request->getResultType());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetResultTypeInvalidNoInWhiteList()
	{
		$this->request->setResultType('not-read-or-write-or-both');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetResultTypeInvalidEmptyString()
	{
		$this->request->setResultType('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetResultTypeInvalidObject()
	{
		$this->request->setResultType(new stdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetResultTypeInvalidArray()
	{
		$this->request->setResultType(array(1,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetResultTypeInvalidInt()
	{
		$this->request->setType(1234);
	}

	/**
	 * This is a hint to the db adapter to use buffering or no
	 *
	 * @return bool
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
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetCallbackEmptyString()
	{
		$this->request->setCallback('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetCallbackObject()
	{
		$this->request->setCallback(new stdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetCallbackInvalidArray()
	{
		$this->request->setCallback(array(1,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetCallbackInt()
	{
		$this->request->setCallback(1234);
	}


}
