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
	 * @param	array	$params		optionally allows you to set params
	 * @return	AbstractType
	 */
	public function __construct($name)
	{
		$this->setSqlName($name);
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
}
