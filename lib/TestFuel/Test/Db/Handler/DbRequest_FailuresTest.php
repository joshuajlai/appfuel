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
	Appfuel\Db\Handler\DbRequest;

/**
 * The query request carries information for the handler and the handler's
 * adapter. 
 */
class DbRequest_FailureTest extends BaseTestCase
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
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetServerModeInvalidNoInWhiteList()
    {  
        $this->request->setServerMode('not-read-or-write-or-both');
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetServerModeEmptyString()
    {  
        $this->request->setServerMode('');
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetServerModeInvalidObject()
    {  
        $this->request->setServerMode(new stdClass());
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetServerModeInvalidArray()
    {
        $this->request->setServerMode(array(1,3,4));
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetServerModeInvalidInt()
    {
        $this->request->setServerMode(1234);
    }


    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetSqlInvalidEmptyString()
    {
        $this->request->setSql('');
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetSqlInvalidObject()
    {
        $this->request->setSql(new stdClass());
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetSqlInvalidArray()
    {
        $this->request->setSql(array(1,3,4));
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetSqlInvalidInt()
    {
        $this->request->setSql(1234);
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetResultTypeInvalidEmptyString()
    {
        $this->request->setResultType('');
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetResultTypeInvalidObject()
    {
        $this->request->setResultType(new stdClass());
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetResultTypeInvalidArray()
    {
        $this->request->setResultType(array(1,3,4));
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetResultTypeInvalidInt()
    {
        $this->request->setResultType(1234);
    }

    /** 
     * @expectedException   Exception
     * @return null
     */
    public function testSetBadValueString()
    {
        $this->request->setValues('this is a string');
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetCallbackEmptyString()
    {
        $this->request->setCallback('');
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetCallbackObject()
    {
        $this->request->setCallback(new stdClass());
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetCallbackInvalidArray()
    {
        $this->request->setCallback(array(1,3,4));
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testSetCallbackInt()
    {
        $this->request->setCallback(1234);
    }

    /** 
     * @expectedException   Exception
     * @return null
     */
    public function testSetBadValueObject()
    {
        $this->request->setValues(new StdClass());
    }

    /** 
     * @expectedException   Exception
     * @return null
     */
    public function testSetBadValueInt()
    {
        $this->request->setValues(12345);
    }


    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testInvalidSqlAddSql_EmptyString()
    {  
        $this->request->addSql('');
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testInvalidSqlAddSql_Array()
    {  
        $this->request->addSql(array('SELECT * FROM TABLE'));
    }
    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testInvalidSqlAddSql_Int()
    {
        $this->request->addSql(12345);
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testInvalidSqlLoadSql_EmptyString()
    {
        $sql = array(
            'SELECT * FROM TABLE',
            ''
        );
        $this->request->loadSql($sql);
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testInvalidSqlLoadSql_Array()
    {
        $sql = array(
            'SELECT * FROM TABLE',
            array('SELECT * FROM OTHER_TABLE')
        );
        $this->request->loadSql($sql);
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     * @return null
     */
    public function testInvalidSqlLoadSql_Number()
    {
        $sql = array(
            'SELECT * FROM TABLE',
            12345
        );
        $this->request->loadSql($sql);
    }

    /**
     * @expectedException   Exception
     * @return null
     */
    public function testSetResultOptionsBadInt()
    {
        $this->request->setResultOptions(12434);
    }

    /**
     * @expectedException   Exception
     * @return null
     */
    public function testSetResultOptionsBadObject()
    {
        $this->request->setResultOptions(new StdClass());
    }

}
