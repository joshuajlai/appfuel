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
namespace TestFuel\Test\Db\Handler;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Handler\DbRequest;

/**
 * The query request carries information for the handler and the handler's
 * adapter. 
 */
class DbRequestTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	DbRequest
	 */
	protected $request = null;

	/**
	 * @return	null
	 */
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

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Handler\DbRequestInterface',
			$this->request
		);
	}

	/**
	 * Appfuel has 3 db server modes read|write|ignore 
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetServerMode()
	{
		$this->assertEquals(
			'ignore', 
			$this->request->getServerMode(), 
			'default server mode is query'
		);

		$this->assertSame(
			$this->request,
			$this->request->setServerMode('read'),
			'exposes fluent interface'
		);
		$this->assertEquals('read', $this->request->getServerMode());

		$this->assertSame(
			$this->request,
			$this->request->setServerMode('write'),
			'exposes fluent interface'
		);
		$this->assertEquals('write', $this->request->getServerMode());

		$this->assertSame(
			$this->request,
			$this->request->setServerMode('ignore'),
			'exposes fluent interface'
		);
		$this->assertEquals('ignore', $this->request->getServerMode());
	}

	/**
	 * The mode is converted to lower case making setServer case insensitve
	 *
	 * @depends	testGetSetServerMode
	 * @return	null
	 */
	public function testSetServerModeUpperMixedCase()
	{
		$this->assertSame(
			$this->request,
			$this->request->setServerMode('wRiTe'),
			'exposes fluent interface'
		);
		$this->assertEquals('write', $this->request->getServerMode());

		$this->assertSame(
			$this->request,
			$this->request->setServerMode('READ'),
			'exposes fluent interface'
		);
		$this->assertEquals('read', $this->request->getServerMode());

		$this->assertSame(
			$this->request,
			$this->request->setServerMode('IGNORe'),
			'exposes fluent interface'
		);
		$this->assertEquals('ignore', $this->request->getServerMode());
	}

	/**
     * @depends testGetSetServerMode
     * @return  null
	 */
	public function testEnableReadOnly()
	{
		$this->assertSame(
			$this->request,
			$this->request->enableReadOnly(),
			'exposes fluent interface'
		);
		$this->assertEquals('read', $this->request->getServerMode());
	}

	/**
     * @depends testGetSetServerMode
     * @return  null
	 */
	public function testEnableWrite()
	{
		$this->assertSame(
			$this->request,
			$this->request->enableWrite(),
			'exposes fluent interface'
		);
		$this->assertEquals('write', $this->request->getServerMode());
	}

	/**
	 * Because we default to ignore we must set it to write so we can show
	 * ignoreServerMode will infact set it to ignore
	 *
     * @depends testEnableWrite
     * @return  null
	 */
	public function testIgnoreServerMode()
	{
		$this->request->enableWrite();

		$this->assertSame(
			$this->request,
			$this->request->ignoreServerMode(),
			'exposes fluent interface'
		);
		$this->assertEquals('ignore', $this->request->getServerMode());
	}

	/**
	 * Appfuel has 3 db request types query|mutli-query|prepared-stmt 
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetRequestType()
	{
		$this->assertEquals(
			'query',
			$this->request->getRequestType(),
			'default request type when nothing is specified'
		);

		$this->assertSame(
			$this->request,
			$this->request->setRequestType('multi-query'),
			'exposes fluent interface'
		);
		$this->assertEquals('multi-query', $this->request->getRequestType());

		$this->assertSame(
			$this->request,
			$this->request->setRequestType('prepared-stmt'),
			'exposes fluent interface'
		);
		$this->assertEquals('prepared-stmt', $this->request->getRequestType());
	
		$this->assertSame(
			$this->request,
			$this->request->setRequestType('query'),
			'exposes fluent interface'
		);
		$this->assertEquals('query', $this->request->getRequestType());
	}

	/**
	 * @depends	testGetSetRequestType
	 * @return	null
	 */
	public function testSetRequestTypeIsCaseInsenstive()
	{
		$this->assertSame(
			$this->request,
			$this->request->setRequestType('MULTI-QUERY'),
			'exposes fluent interface'
		);
		$this->assertEquals('multi-query', $this->request->getRequestType());

		$this->assertSame(
			$this->request,
			$this->request->setRequestType('PREPARED-stmt'),
			'exposes fluent interface'
		);
		$this->assertEquals('prepared-stmt', $this->request->getRequestType());

		$this->assertSame(
			$this->request,
			$this->request->setRequestType('QuErY'),
			'exposes fluent interface'
		);
		$this->assertEquals('query', $this->request->getRequestType());
	}

	/**
	 * @depends	testGetSetRequestType
	 * @return	null
	 */
	public function testEnablePreparedStmt()
	{
		$this->assertSame(
			$this->request,
			$this->request->enablePreparedStmt(),
			'exposes fluent interface'
		);	
		$this->assertEquals('prepared-stmt', $this->request->getRequestType());
	}

	/**
	 * @depends	testGetSetRequestType
	 * @return	null
	 */
	public function testEnableMutliQuery()
	{
		$this->assertSame(
			$this->request,
			$this->request->enableMultiQuery(),
			'exposes fluent interface'
		);	
		$this->assertEquals('multi-query', $this->request->getRequestType());
	}

	/**
	 * Because we default to query we must set it to mult-query before showing
	 * enableQuery will work
	 *
	 * @depends	testEnableMutliQuery
	 * @return	null
	 */
	public function testEnableQuery()
	{
		$this->request->enableMultiQuery();
		$this->assertSame(
			$this->request,
			$this->request->enableQuery(),
			'exposes fluent interface'
		);	
		$this->assertEquals('query', $this->request->getRequestType());
	}

	/**
	 * In the context of the request sql must only be a non empty string
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetIsSql()
	{
		$this->assertNull($this->request->getSql());
		$this->assertFalse($this->request->isSql());

		$sql = 'select * from blah';
		$this->assertEquals(
			$this->request,
			$this->request->setSql($sql),
			'expose fluent interface'
		);
		$this->assertEquals($sql, $this->request->getSql());
		$this->assertTrue($this->request->isSql());
	}

	/**
	 * @depends	testGetSetIsSql
	 * @return	null
	 */
	public function testAddSql()
	{
		$this->assertFalse($this->request->isSql());
		$sql = 'select * from blah';
		
		$this->request->setSql($sql);
		
		$sql2 = 'select * from other';
		$this->assertEquals(
			$this->request,
			$this->request->addSql($sql2),
			'exposes fluent interface'
		);

		$expected = "$sql;$sql2";
		$this->assertEquals($expected, $this->request->getSql());
		$this->assertTrue($this->request->isSql());

		$sql3 = 'insert into blah values (1,2,3)';
		$this->assertEquals(
			$this->request,
			$this->request->addSql($sql3),
			'exposes fluent interface'
		);

		$expected = "$sql;$sql2;$sql3";
		$this->assertEquals($expected, $this->request->getSql());
		$this->assertTrue($this->request->isSql());
	}

	/**
	 * @depends	testAddSql
	 * @erturn	null
	 */
	public function testLoadSql()
	{
		$sql = array(
			'select * from blah',
			'select * from other',
			'insert into blah values (1,2,3)'
		);
		$this->assertFalse($this->request->isSql());

		$this->assertSame(
			$this->request,
			$this->request->loadSql($sql),
			'exposes fluent interface'
		);

		$expected = "{$sql[0]};{$sql[1]};{$sql[2]}";
		$this->assertEquals($expected, $this->request->getSql());	
		$this->assertTrue($this->request->isSql());
	}

    /**
	 * @testInterface
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
	 * @depends	testGetSetResultType
	 * @return	null
	 */
	public function testSetResultTypeNotCaseSensitive()
	{
	    $this->assertSame(
            $this->request,
            $this->request->setResultType('POSITION')
        );
        $this->assertEquals('position', $this->request->getResultType());

	    $this->assertSame(
            $this->request,
            $this->request->setResultType('BoTh')
        );
        $this->assertEquals('both', $this->request->getResultType());

	    $this->assertSame(
            $this->request,
            $this->request->setResultType('namE')
        );
        $this->assertEquals('name', $this->request->getResultType());	
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
     * @param   $raw
     * @return  mixed
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
     * @return null
     */
    public function testGetSetResultOptions()
    {
        $this->assertEquals(array(), $this->request->getResultOptions());

        $options = array(1,2,3);
        $this->assertSame(
            $this->request,
            $this->request->setResultOptions($options)
        );

        $this->assertEquals($options, $this->request->getResultOptions());

        /* empty array is valid */
        $options = array();
        $this->assertSame(
            $this->request,
            $this->request->setResultOptions($options)
        );

        $this->assertEquals($options, $this->request->getResultOptions());
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

}
