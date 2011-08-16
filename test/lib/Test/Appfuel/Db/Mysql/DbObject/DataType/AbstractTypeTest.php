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
	protected $sqlName = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->sqlName = 'TINYINT';
		$class = 'Appfuel\Db\Mysql\DbObject\DataType\AbstractType';
		$this->type = $this->getMockBuilder($class)
						   ->setConstructorArgs(array($this->sqlName))
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
	 * @return	null
	 */
	public function testGetSqlName()
	{
		$this->assertEquals($this->sqlName, $this->type->getSqlName());
	}
}
