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
namespace Appfuel\Db\Mysql\DbObject\DataType;

use Appfuel\Framework\Exception,
	Appfuel\Validate\Filter\ValidateFilter,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * The abstract tpe handles the common details of all datatypes
 */
abstract class AbstractType
{
	/**
	 * Name used in sql statements
	 * @var string
	 */
	protected $sqlName = null;

	/**
	 * List of attributes associated with this datatype. Attributes are not
	 * static and can be added or removed dynamically
	 *
	 * @var Dictionary
	 */
	protected $attrs = null;
	
	/**
	 * @param	array	$params		optionally allows you to set params
	 * @return	AbstractType
	 */
	public function __construct($name, DictionaryInterface $attrs)
	{
		$this->setSqlName($name);
		$this->setAttributes($attrs);
	}

	/**
	 * @return	Dictionary
	 */
	public function getSqlName()
	{
		return $this->sqlName;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string
	 * @return	null
	 */
	protected function setSqlName($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new Exception("sql name must be a non empty string");
		}

		$this->sqlName = $name;
	}

	/**
	 * @return	Dictionary
	 */
	public function getAttributes()
	{
		return $this->attrs;
	}

	/**
	 * @param	string	$name,	
	 * @param	mixed	$value
	 * @return	AbstractDataType
	 */
	protected function addAttribute($name, $value)
	{
		if (empty($name) || ! is_string($name)) {
			throw new Exception("attr name must be non empty string");
		}

		$this->getAttributes()
			 ->add($name, $value);

		return $this;
	}

	/**
	 * @param	DictionaryInterface $attrs
	 * @return	null
	 */
	protected function setAttributes(DictionaryInterface $attrs)
	{
		$this->attrs = $attrs;
	}
}
