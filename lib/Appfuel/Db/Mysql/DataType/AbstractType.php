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
namespace Appfuel\Db\Mysql\DataType;

use Appfuel\Framework\Exception,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * The abstract tpe handles the common details of all mysql datatypes
 */
abstract class AbstractType
{
	/**
	 * Text used in sql statements to represent this type ex) INT, TEXT 
	 * @var string
	 */
	protected $sqlString = null;

	/**
	 * Name of the validator used to determine the correctness of a value
	 * against this type
	 * @var string
	 */
	protected $validator = null;

	/**
	 * List of attributes associated with this datatype. Attributes are not
	 * static and can be added or removed dynamically
	 *
	 * @var Dictionary
	 */
	protected $attrs = null;
	
	/**
	 * @param	string	$sql	string used in sql statements
	 * @param	string	$validator	name of the validator for this type
	 * @param	DictionaryInterface	$attrs		dictionary of type attributes
	 * @return	AbstractType
	 */
	public function __construct($sql, 
								$validator, 
								DictionaryInterface $attrs = null)
	{
		$this->setSqlString($sql);
		$this->setValidator($validator);

		if (null === $attrs) {
			$attrs = new Dictionary();
		}
		$this->setAttributes($attrs);
	}

	/**
	 * @return	string
	 */
	public function buildSql()
	{
		$sql = $this->getSqlString();
		if ($this->isUpperCase()) {
			$sql = strtoupper($sql);
		}
		else {
			$sql = strtolower($sql);
		}
		
		return $sql;
	}

	/**
	 * @return	AbstractType
	 */
	public function enableUpperCase()
	{
		$this->addAttribute('is-uppercase', true);
		return $this;
	}

	/**
	 * @return	AbstractType
	 */
	public function disableUpperCase()
	{
		$this->addAttribute('is-uppercase', false);
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isUpperCase()
	{
		$result = false;
		if (true === $this->getAttribute('is-uppercase', false)) {
			$result = true;
		}

		return $result;
	}

	/**
	 * @return	Dictionary
	 */
	public function getSqlString()
	{
		return $this->sqlString;
	}

	/**
	 * @return	string
	 */
	public function getValidatorName()
	{
		return $this->validator;
	}

	/**
	 * @param	string	$name
	 * @param	mixed	$default
	 * @return	mixed	returns $default when does not exist
	 */ 
	public function getAttribute($name, $default = null)
	{
		return $this->getAttributes()
					->get($name, $default);
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
	 * @throws	Appfuel\Framework\Exception
	 * @param	string
	 * @return	null
	 */
	protected function setSqlString($sql)
	{
		if (empty($sql) || ! is_string($sql)) {
			throw new Exception("sql string must be a non empty string");
		}

		$this->sqlString = $sql;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string
	 * @return	null
	 */
	protected function setValidator($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new Exception("validator name must be a non empty string");
		}

		$this->validator = $name;
	}


	/**
	 * @param	DictionaryInterface $attrs
	 * @return	null
	 */
	protected function setAttributes(DictionaryInterface $attrs)
	{
		$this->attrs = $attrs;
	}

	/**
	 * @return	Dictionary
	 */
	protected function getAttributes()
	{
		return $this->attrs;
	}
}
