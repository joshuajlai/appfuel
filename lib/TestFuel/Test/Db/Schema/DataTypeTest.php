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
namespace TestFuel\Test\Db\Schema;

use StdClass,
	Appfuel\Db\Schema\DataType,
	TestFuel\TestCase\BaseTestCase;

/**
 * The schema datatype describe an vendor agnostic model of the data type.
 * we will test its interface here.
 */
class DataTypeTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DataType
	 */
	protected $type = null;

	/**
	 * Associative array used in the constructor
	 * @var array
	 */
	protected $attrs = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->attrs = array(
			'type-name'		=> 'integer',
			'type-modifier' => 6,
			'unsigned'		=> true,
		);

		$this->type = new DataType($this->attrs);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->type = null;
	}

	/**
	 * @return null
	 */
	public function testInteface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Schema\DataTypeInterface',
			$this->type,
			'must implment this interface'
		);
	}
}

