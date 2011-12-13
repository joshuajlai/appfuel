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
	Appfuel\Db\Schema\Column,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * Columns are fairly dumb, basically holds a datatype, nullable flag, 
 * default value and flag. We will be testing those interfaces in this 
 * test case.
 */
class ColumnTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Column
	 */
	protected $column = null;

	/**
	 * Associative array used in the constructor
	 * @var array
	 */
	protected $attrs = null;

	protected $dataType = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->dataType = $this->getMock(
			'Appfuel\Framework\Db\Schema\DataTypeInterface'
		);
		$this->attrs = array(
			'column-name'	=> 'my-column',
			'data-type'		=> $this->dataType,
			'is-nullable'	=> true,
			'is-default'	=> true,
			'default-value' => 'my default'
		);

		$this->column = new Column($this->attrs);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->column = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Schema\SchemaObjectInterface',
			$this->column,
			'must implment this interface'
		);
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Schema\ColumnInterface',
			$this->column,
			'must implment this interface'
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetName()
	{
		$this->assertEquals(
			$this->attrs['column-name'],
			$this->column->getName()
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetDataType()
	{
		$type = $this->column->getDataType();
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Schema\DataTypeInterface',
			$type
		);

		$this->assertSame($this->attrs['data-type'], $type);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsNullable()
	{
		$this->assertTrue($this->column->isNullable());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefault()
	{
		$this->assertTrue($this->column->isDefault());
		$this->assertSame(
			$this->attrs['default-value'],
			$this->column->getDefaultValue()
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testNotNullable()
	{
		/* columns by default are not nullable */
		$attrs = array(
			'column-name' => 'column',
			'data-type'   => $this->dataType,
		);
		$column = new Column($attrs);
		$this->assertFalse($column->isNullable());

		/* only a strict bool value of true can toggle the default value */
		/* columns by default are not nullable */
		$attrs['is-nullable'] = false;
		$column = new Column($attrs);
		$this->assertFalse($column->isNullable());

		$attrs['is-nullable'] = '';
		$column = new Column($attrs);
		$this->assertFalse($column->isNullable());

		$attrs['is-nullable'] = 0;
		$column = new Column($attrs);
		$this->assertFalse($column->isNullable());

		/* this is not a strict true so isNullable will be false */
		$attrs['is-nullable'] = 1;
		$column = new Column($attrs);
		$this->assertFalse($column->isNullable());

		/* the only value that will work */
		$attrs['is-nullable'] = true;
		$column = new Column($attrs);
		$this->assertTrue($column->isNullable());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultConstructor()
	{
		/* isDefault has a default setting of false */
		$attrs = array(
			'column-name' => 'column',
			'data-type'   => $this->dataType,
		);
		$column = new Column($attrs);
		$this->assertFalse($column->isDefault());
		$this->assertNull($column->getDefaultValue());

		$attrs['is-default'] = false;
		$attrs['default-value'] = 12345;
		$column = new Column($attrs);
		$this->assertFalse($column->isDefault());
		$this->assertNull($column->getDefaultValue());
	
		$attrs['is-default'] = '';
		$attrs['default-value'] = 12345;
		$column = new Column($attrs);
		$this->assertFalse($column->isDefault());
		$this->assertNull($column->getDefaultValue());

		$attrs['is-default'] = 0;
		$attrs['default-value'] = 12345;
		$column = new Column($attrs);
		$this->assertFalse($column->isDefault());
		$this->assertNull($column->getDefaultValue());
		
		/* this will not work cause its not a bool true */
		$attrs['is-default'] = 1;
		$attrs['default-value'] = 12345;
		$column = new Column($attrs);
		$this->assertFalse($column->isDefault());
		$this->assertNull($column->getDefaultValue());

		$attrs['is-default'] = true;
		$attrs['default-value'] = 12345;
		$column = new Column($attrs);
		$this->assertTrue($column->isDefault());
		$this->assertEquals(12345, $column->getDefaultValue());	
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testColumnName_NotSetFailure()
	{
		/* isDefault has a default setting of false */
		$attrs = array(
			'data-type'   => $this->dataType,
		);
		$column = new Column($attrs);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testColumnName_EmptyStringFailure()
	{
		/* isDefault has a default setting of false */
		$attrs = array(
			'column-name' => '',
			'data-type'   => $this->dataType,
		);
		$column = new Column($attrs);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testColumnName_ArrayFailure()
	{
		/* isDefault has a default setting of false */
		$attrs = array(
			'column-name' => array(1,2,3),
			'data-type'   => $this->dataType,
		);
		$column = new Column($attrs);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testColumnName_ObjectFailure()
	{
		/* isDefault has a default setting of false */
		$attrs = array(
			'column-name' => new StdClass(),
			'data-type'   => $this->dataType,
		);
		$column = new Column($attrs);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testDataType_NotSetFailure()
	{
		/* isDefault has a default setting of false */
		$attrs = array(
			'column-name' => 'my-column',
		);
		$column = new Column($attrs);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testDataType_ArrayFailure()
	{
		/* isDefault has a default setting of false */
		$attrs = array(
			'column-name' => 'my-column',
			'data-type'	  => array(1,2,3)
		);
		$column = new Column($attrs);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testDataType_StringFailure()
	{
		/* isDefault has a default setting of false */
		$attrs = array(
			'column-name' => 'my-column',
			'data-type'	  => 'not the correct interface'
		);
		$column = new Column($attrs);
	}






}
