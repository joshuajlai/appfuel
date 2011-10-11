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
	Appfuel\Framework\Db\Schema\Table\DataTypeInterface;

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
	 * @return	string
	 */
	public function buildSql()
	{
		return '';
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param	string	$name
	 * @return	Column
	 */
	public function setName($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new Exception("Column name must be a non empty string");
		}

		$this->name = $name;
		return $this;
	}

	/**
	 * @return	DataTypeInterface
	 */
	public function getDataType()
	{
		return $this->dataType;
	}

	/**
	 * @param	DataTypeInterface
	 * @return	Column
	 */
	public function setDataType(DataTypeInterface $dataType)
	{
		$this->dataType = $dataType;
		return $this;
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
	public function __toString()
	{
		return $this->buildSql();
	}
}
