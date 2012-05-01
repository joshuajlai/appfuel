<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Db\Schema;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Schema\ColumnInterface,
	Appfuel\Framework\Db\Schema\DataTypeInterface,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * Vendor agnostic view of the database table.
 */
class Table extends SchemaObject implements ColumnInterface
{
	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * List of ColumnInterfaces keyed by the column name
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Creates schema objects like columns, primarykeys, foreignkeys etc.. 
	 * from space dimited string definitions
	 * @var	SchemaFactoryInterface
	 */
	protected $factory = null;

	/**
	 * @param	array|DictionaryInterface	$data  table definition
	 * @param	SchemaFactoryInterface		$factory	u
	 * @return	Column
	 */
	public function __construct($data, SchemaFactoryInterface $factory = null)
	{
		if (null === $factory) {
			$factory = new SchemaFactory();
		}

		/*
		 * inherit from schema object which will setup the dictionary for
		 * attribute list
		 */
		parent::__construct($details);
		$attrList = $this->getAttributeList();

		$err = "Failed to instatiate:";
		$name = $attrList->get('table-name');
		if (empty($name) || ! is_string($name)) {
			throw new Exception("$err column name must be a non empty string");
		}
		$this->name = $name;

		$listedCols = $attrList->get('columns');
		if (empty($columns)) {
			throw new Exception("Table must define columns");
		}

		if (! is_array($columns)) {
			throw new Exception("List of columns must be stored in an array");
		}
		
		$columns = array();
		foreach ($listedCols as $col) {
			$column = $this->createColumn($col);
			$columns[$column->getName()] = $column;
		}	
		$this->columna = $type;
	
		if ($attrList->existsAs('is-nullable', 'bool-true')) {
			$this->isNullable = true;
		}

		if ($attrList->existsAs('is-default', 'bool-true')) {
			$this->isDefault    = true;
			$this->defaultValue = $attrList->get('default-value', null);
		}
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the name of the data type
	 *
	 * @return	string
	 */
	public function getDataType()
	{
		return $this->dataType;
	}

	/**
	 * @return	bool
	 */
	public function isNullable()
	{
		return $this->isNullable;
	}

	/**
	 * @return	bool
	 */
	public function isDefault()
	{
		return $this->isDefault;
	}

	/**
	 * @return	mixed
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}
}
