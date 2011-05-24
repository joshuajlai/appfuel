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
namespace Test\Appfuel\App\View\Json;

use Test\AfTestCase as ParentTestCase,
	Appfuel\App\View\Json\Response,
	StdClass;

/**
 * This responseument type is very simple and does not require a template file which
 * is why we do not extend the template just the view data class. The only 
 * functionality that is added is the build which json encoded the items in
 * the dictionary
 */
class DocumentTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $response = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->response = new Response();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->response);
	}

	/**
	 * @return null
	 */	
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'Appfuel\App\View\Data',
			$this->response,
			'The json response must extend the view data class'
		);

		$this->assertInstanceOf(
			'Appfuel\Data\Dictionary',
			$this->response,
			'The json response is also a dictionary'
		);
	}

	/**
	 * Test the json_encode on a single value assigned into the dictionary
	 * @return null
	 */
	public function testBuildSimpleValue()
	{
		$this->response->assign('foo', 'bar');
		$result = $this->response->build();

		$expected = json_encode(array('foo'=>'bar'));
		$this->assertEquals($expected, $result);
	}

	/**
	 * 
	 * @return null
	 */
	public function testBuildComplex()
	{
		$obj = new StdClass();
		$obj->firstName = 'bob';
		$obj->email = 'bob@bob.com';
		$obj->count = 66;

		$data = array(
			'string' => 'this is a string',
			'number' => 12345,
			'float'  => 1.2345,
			'array'  => array(1,2,'this is text',3),
			'obj'    => $obj
		);

		$expected = json_encode($data);
		$this->response->load($data);
		$result = $this->response->build();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return null
	 */
	public function testBuildNoData()
	{	
		$result = $this->response->build();
		$this->assertInternalType('string', $result);
		$this->assertEmpty($result);
	}

}
