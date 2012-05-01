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
namespace Appfuel\Db\Schema\Table;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Schema\Table\ColumnInterface,
	Appfuel\Framework\Db\Schema\Table\DataTypeInterface,
	Appfuel\Framework\Db\Schema\Table\DataTypeEngineInterface;

/**
 * 
 */
class Column implements ColumnInterface
{
	/**
	 * The type engine is reposible for creating the datatype which are
	 * specific to the vendor so the column does not need to care
	 * @var	TypeEngineInterface
	 */
	protected $typeEngine = null;

	/**
	 * Name of the column
	 * @var string 
	 */
	protected $name = null;


	/**
	 * Name of the data type used for this column
	 * @var string
	 */
	protected $dataType = null;
	
	/**
	 * Flag used to determine if nulls are allowed
	 * @var bool
	 */
	protected $isNull = null;

	/**
	 * Flag used to determine if default values are enabled
	 * @var bool
	 */
	protected $isDefaultValue = false;

	/**
	 * Default value used if not value is given
	 * @var mixed
	 */
	protected $defaultValue = null;


	/**
	 * @param	string	$name		name of the column
	 * @param	string	$dataType	name used to identify the datatype
	 * @param	bool	$isNull		flag used to allow nulls
	 * @param	bool	$isDefault	flag used to enable default values
	 * @param	mixed	$value		default value used when its enabled
	 * @return	Column
	 */
	public function __construct(DataTypeEngine $dataTypeEngine)
	{
		$this->setName($name);
		$this->setDataType($type);
		
		if (null !== $attributes) {
			$this->setAttributes($attributes);
		}

		$this->setAttributes($attributes);
		if (true === $isNull) {
			$this->enableNullValues();
		}

		if (true === $isDefault) {
			$this->setDefaultValue($value);
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
	public function isNullAllowed()
	{
		return $this->isNull;
	}

	/**
	 * @return	bool
	 */
	public function isDefaultValue()
	{
		return $this->isDefaultValue;
	}

	/**
	 * @return	bool
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @return	null
	 */
	protected function enableNullValues()
	{
		$this->isNull = true;
	}

	/**
	 * @param	string	$name
	 * @return	Column
	 */
	protected function setName($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new Exception("Column name must be a non empty string");
		}

		$this->name = $name;
		return $this;
	}

	/**
	 * @param	DataTypeInterface
	 * @return	Column
	 */
	protected function setDataType($dataType)
	{
		if (empty($dataType) || ! is_string($dataType)) {
			throw new Exception("DataType name must be a non empty string");
		}
		$this->dataType = $dataType;
		return $this;
	}

	/**
	 * Because the constructor only calls this when isDefault is true we
	 * must assign the default value and toggle the flag
	 *
	 * @param	mixed	$value
	 * @return	null
	 */
	protected function setDefaultValue($value)
	{
		if (is_object($value) && ! is_callable(array($value, '__toString'))) {
			throw new Exception('Objects must support  __toString');
		}
		else if (! is_scalar($value)) {
			throw new Exception("must be scalar or supports __toString");
		}

		$this->isDefaultValue = true;
		$this->defaultValue =(string) $value;
	}
}
