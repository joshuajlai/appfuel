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
namespace Test\Appfuel\Db\Request;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Request\MultiQueryRequest;

/**
 */
class MultiQueryRequestTest extends ParentTestCase
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
		$this->request = new MultiQueryRequest($this->opType);
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
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetResultOptionsBadNoParams()
	{
		$this->request->setResultOptions();
	}

	/**
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetResultOptionsBadString()
	{
		$this->request->setResultOptions('this is a string');
	}

	/**
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetResultOptionsBadInt()
	{
		$this->request->setResultOptions(12434);
	}

	/**
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetResultOptionsBadObject()
	{
		$this->request->setResultOptions(new StdClass());
	}
}
