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
	Appfuel\Db\Request\PreparedRequest;

/**
 */
class PreparedRequestTest extends BaseTestCase
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

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->opType  = 'read';
		$this->request = new PreparedRequest($this->opType);
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
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetBadValueString()
	{
		$this->request->setValues('this is a string');
	}

	/**	
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetBadValueObject()
	{
		$this->request->setValues(new StdClass());
	}

	/**	
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetBadValueInt()
	{
		$this->request->setValues(12345);
	}



}
