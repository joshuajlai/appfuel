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
	Appfuel\Framework\Db\Schema\Table\Constraint\ConstraintInterface;

/**
 * 
 */
class Column implements ColumnInterface
{
	/**
	 * @return	string
	 */
	protected $name = null;

	/**
	 * @var DataTypeInterface
	 */
	protected $dataType = null;

	/**
	 * @var bool
	 */
	protected $isUpperCase = false;

	/**
	 * @param	string	$name	name of the column
	 * @param	DataTypeInterface	$type 
	 * @param	ConstraintInterface	$notNull
	 * @param	ConstraintInterface $default
	 * @return	Column
	 */
	public function __construct($name, 
								DataTypeInterface $type, 
								ConstraintInterface $notNull = null,
								ConstraintInterface $default = null)
	{
		$this->setName($name);
		$this->setDataType($type);
		
		if (null !== $notNull) {
			$this->setNotNullConstraint($notNull);
		}

		if (null !== $default) {
			$this->setDefaultValueConstraint($default);
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
	 * @return	DataTypeInterface
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
		return $this->notNull instanceof ConstraintInterface;
	}

	public function isDefaultValue()
	{
		return $this->default instanceof ConstaintInterface;
	}

    /**
     * @return  AbstractType
     */
    public function enableUpperCase()
    {  
        $this->isUpperCase = true;
        return $this;
    }

    /**
     * @return  AbstractType
     */
    public function disableUpperCase()
    {
        $this->isUpperCase = false;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isUpperCase()
    {  
        return $this->isUpperCase;
    }
	
	/**
	 * @return	string
	 */
	public function buildSql()
	{
		return '';
	}

	/**
	 * @return	string
	 */
	public function __toString()
	{
		return $this->buildSql();
	}

	/**
	 * Only used internally by the column
	 *
	 * @return	ConstraintInterface
	 */
	protected function getNotNullConstraint()
	{
		return $this->notNull;
	}

	/**
	 * @param	ConstraintInterface $constraint
	 * @return	null
	 */
	protected function setNotNullConstraint(ConstraintInterface $constraint)
	{
		$sql = strtolower($constraint->getSqlPhrase());
		if ('not null' !== $sql) {
			throw new Exception("Constraint must be not null");
		}
		$this->notNull = $constraint;
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
	protected function setDataType(DataTypeInterface $dataType)
	{
		$this->dataType = $dataType;
		return $this;
	}
}
