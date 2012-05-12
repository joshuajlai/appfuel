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
 * Vendor agnostic object that decribes a table column. The column is designed
 * to be as dumb as possible, holding only critical info. This means the column
 * does not even know if it is a key of an index, that job is delagated to the
 * table.
 */
class Column extends SchemaObject implements ColumnInterface
{
	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var DataTypeInterface
	 */
	protected $dataType = null;

	/**
	 * Flag used to determine if nulls are allowed
	 * @var bool
	 */
	protected $isNullable = false;

	/**
	 * Flag used to determine if default value is enabled
	 * @var bool
	 */
	protected $isDefault = false;

	/**
	 * Should only be used when isDefault is true
	 * @var mixed
	 */
	protected $defaultValue = null;


	/**
	 * @param	array|DictionaryInterface	$details  list of column attrs
	 * @return	Column
	 */
	public function __construct($details)
	{
		/*
		 * inherit from schema object which will setup the dictionary for
		 * attribute list
		 */
		parent::__construct($details);
		$attrList = $this->getAttributeList();

		$err = "Failed to instatiate:";
		$name = $attrList->get('column-name');
		if (empty($name) || ! is_string($name)) {
			throw new Exception("$err column name must be a non empty string");
		}
		$this->name = $name;

		$type = $attrList->get('data-type');
		if (! ($type instanceof DataTypeInterface)) {
			$err .= " Data Type not found for key 'data-type' or does not";
			$err .= " implment Appfuel\Framework\Db\Schema\DataTypeInterface";
			throw new Exception($err);
		}
		$this->dataType = $type;
	
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
