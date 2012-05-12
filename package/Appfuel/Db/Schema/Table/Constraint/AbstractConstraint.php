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
namespace Appfuel\Db\Schema\Table\Constraint;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Schema\Table\Constraint\ConstraintInterface;

/**
 * The abstract tpe handles the common details of all mysql constraints
 */
abstract class AbstractConstraint implements ConstraintInterface
{
	/**
	 * Sql word used in sql fragment for this constraint for example,
	 * 'default', 'not null', 'primary key' etc...
	 * @var string
	 */
	protected $sqlPhrase = null;

	/**
	 * Flag used to determine if the sql string will be uppercase
	 * @var bool
	 */
	protected $isUpperCase = false;

	/**
	 * @param	string	$sql keyword that represents the sql constraint
	 * @return	AbstractConstraint
	 */
	public function __construct($sql) 
	{
		$this->setSqlPhrase($sql);
	}

	/**
	 * @return	AbstractType
	 */
	public function enableUpperCase()
	{
		$this->isUpperCase = true;
		return $this;
	}

	/**
	 * @return	AbstractType
	 */
	public function disableUpperCase()
	{
		$this->isUpperCase = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isUpperCase()
	{
		return $this->isUpperCase;
	}

	/**
	 * @return	string
	 */
	public function getSqlPhrase()
	{
		return $this->sqlPhrase;
	}

	/**
	 * @return	string
	 */
	public function buildSql()
	{
		$sql = $this->getSqlPhrase();
		if ($this->isUpperCase()) {
			$sql = strtoupper($sql);
		}
		else {
			$sql = strtolower($sql);
		}
		
		return $sql;
	}

	/**
	 * I can not anticipate how a developer might extend buildSql and
	 * this method is not allowed to pass on exceptions so I catch 
	 * and return an empty string
	 *
	 * @return	string
	 */
	public function __toString()
	{
		try {
			$sql = $this->buildSql();
		} catch (\Exception $e) {
			$sql = '';
		}

		return $sql;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string
	 * @return	null
	 */
	protected function setSqlPhrase($sql)
	{
		if (empty($sql) || ! is_string($sql)) {
			throw new Exception("sql string must be a non empty string");
		}

		$this->sqlPhrase = $sql;
	}
}
