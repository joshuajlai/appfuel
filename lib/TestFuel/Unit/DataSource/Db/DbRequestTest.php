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
	Appfuel\DataSource\Db\DbRequest,
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
			'Appfuel\DataSource\Db\DbRequestInterface',
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
			$this->request->setType('MultiQuery')
		);
		$this->assertEquals('MultiQuery', $this->request->getType());
		$this->assertSame(
			$this->request, 
			$this->request->setType('MULTI-QUERY')
		);
		$this->assertEquals('MULTI-QUERY', $this->request->getType());
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

    /**
     * @return null
     */
    public function testGetSetIsValues()
    {  
        $this->assertFalse($this->request->isValues());
        $this->assertEquals(array(), $this->request->getValues());

        $values = array(1,2,3,4);
        $this->assertSame($this->request, $this->request->setValues($values));
        $this->assertEquals($values, $this->request->getValues());
        $this->assertTrue($this->request->isValues());

        /* empty array will work */
        $values = array();
        $this->assertSame($this->request, $this->request->setValues($values));
        $this->assertEquals($values, $this->request->getValues());
        $this->assertFalse($this->request->isValues());
    }


    /**
     * When sql has not be set addSql will use setSql instead of trying to 
     * append.
     *
     * @return null
     */
    public function testAddSqlWhenNoSqlExists()
    {  
        /* default is no sql because no was passed into constructor for 
         * setup
         */
        $this->assertFalse($this->request->isSql());
        $sql = "SELECT * FROM TABLE WHERE id=blah";
        $this->assertSame(
            $this->request,
            $this->request->addSql($sql),
            'must use a fluent interface'
        );
        $this->assertTrue($this->request->isSql());
        $this->assertEquals($sql, $this->request->getSql());
    }

    /**
     * When you use addSql multiple times, each string is concatenated and
     * use setSql to assign back the result
     *
     * @depends     testAddSqlWhenNoSqlExists
     * @return null
     */
    public function testAddSqlMultipleTimes()
    {
        $sql = "SELECT * FROM TABLE WHERE id=blah";
        $this->request->setSql($sql);
        $this->assertTrue($this->request->isSql());
        $this->assertEquals($sql, $this->request->getSql());

        $sql2 = "SELECT * FROM TABLE WHERE id=foo";
        $this->request->addSql($sql2);
        $this->assertTrue($this->request->isSql(), 'should not change');

        $expected = "{$sql};$sql2";
        $this->assertEquals($expected, $this->request->getSql());

        $sql3 = "SELECT * FROM TABLE WHERE id=bar";
        $this->request->addSql($sql3);
        $this->assertTrue($this->request->isSql(), 'should not change');

        $expected = "{$sql};{$sql2};$sql3";
        $this->assertEquals($expected, $this->request->getSql());

        /* reset to one sql like this */
        $this->request->setSql($sql);
        $this->assertTrue($this->request->isSql(), 'should not change');
        $this->assertEquals($sql, $this->request->getSql());
    }

    /**
     * When you use addSql multiple times, each string is concatenated and
     * use setSql to assign back the result
     *
     * @depends     testAddSqlMultipleTimes
     * @return null
     */
    public function testLoadSqlNoSqlExists()
    {
        $sql1 = "SELECT * FROM TABLE WHERE id=blah";
        $sql2 = "SELECT * FROM TABLE WHERE id=foo";
        $sql3 = "SELECT * FROM TABLE WHERE id=bar";
        $sql = array($sql1, $sql2, $sql3);

        $this->assertFalse($this->request->isSql());
        $this->assertSame(
            $this->request,
            $this->request->loadSql($sql),
            'must use a fluent interface'
        );
        $this->assertTrue($this->request->isSql());

        $expected = "{$sql1};{$sql2};$sql3";
        $this->assertEquals($expected, $this->request->getSql());
    }

    /**
     * @depends             testInterface
     * @expectedException   InvalidArgumentException
     * @dataProvider        provideInvalidStringsIncludeNull
     * @return null
     */
    public function testSetSqlAddSql_Failure($sql)
    {  
        $this->request->addSql($sql);
    }

    /**
     * @expectedException	InvalidArgumentException 
     * @dataProvider        provideInvalidStringsIncludeNull
     * @return null
     */
    public function testInvalidSqlLoadSql_Failure($badSql)
    {
        $sql = array(
            'SELECT * FROM TABLE',
            $badSql
        );
        $this->request->loadSql($sql);
    }

    /**
	 * @depends				testInterface
     * @return null
     */
    public function testGetSetMultiResultOptions()
    {
        $this->assertEquals(array(), $this->request->getMultiResultOptions());

        $options = array(1,2,3);
        $this->assertSame(
            $this->request,
            $this->request->setMultiResultOptions($options)
        );

        $this->assertEquals($options, $this->request->getMultiResultOptions());

        /* empty array is valid */
        $options = array();
        $this->assertSame(
            $this->request,
            $this->request->setMultiResultOptions($options)
        );

        $this->assertEquals($options, $this->request->getMultiResultOptions());
    }
}
