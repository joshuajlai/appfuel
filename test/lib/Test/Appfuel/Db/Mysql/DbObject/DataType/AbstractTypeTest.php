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
namespace Test\Appfuel\Db\Mysql\DbObject\DataType;

use Test\AfTestCase as ParentTestCase;

/**
 * The AbstractType handle common functionality to all data types
 */
class AbstractTypeTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Error
	 */
	protected $type = null;

	/**
	 * Parameters used to describe the type
	 * @var array
	 */
	protected $typeParams = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->typeParams = array(
			'min' => 0,
			'max' => 255,
		);

		$class = 'Appfuel\Db\Mysql\DbObject\DataType\AbstractType';
		$this->type = $this->getMockBuilder($class)
						   ->setConstructorArgs(array($this->typeParams))
						   ->getMockForAbstractClass();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->type);
	}

	/**
	 * The only public method used in the abstract by all other class is 
	 * getParams to get the dictionary of parameters for the data type
	 *
	 * @return	null
	 */
	public function testGetParams()
	{
		$result = $this->type->getParams();
		$this->assertInstanceOf(
			'Appfuel\Framework\DataStructure\Dictionary',
			$result
		);

		$this->assertEquals($this->typeParams, $result->getAll());
	}
}
