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
namespace Appfuel\Db\Sql\Identifier;

use Appfuel\Framework\Expr\BasicExpr;

/**
 *
 */
class ColumnName extends BasicExpr
{
	/**
	 * Name of the table
	 * @var string
	 */
	protected $qualifiedName = null;

	/**
	 * Flag used to determine if the schema should be qualified
	 * @var bool
	 */
	protected $isQualified = false;

	/**
     * @param   string   $operand
     * @return  File
     */
    public function __construct($column)
    {
		$pos = strpos($column, '.');
		if (false !== $pos) {
			$this->qualifiedName = substr($column, 0, $pos);
			$operand = substr($column, $pos+1);
			$this->enableQualifiedName();
		} else {
			$operand = $column;
		}

		parent::__construct($operand);
    }

	/**
	 * @return	TableName
	 */
	public function enableQualifiedName()
	{
		if (empty($this->qualifiedName) || ! is_string($this->qualifiedName)) {
			return $this;
		}
		$this->isQualified = true;
		return $this;
	}

	/**
	 * @return	TableName
	 */
	public function disableQualifiedName()
	{
		$this->isQualified = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isQualifiedName()
	{
		return $this->isQualified;
	}

	/**
	 * @return	string
	 */
	public function getQualifiedName()
	{
		$str = $this->operand;
		if ($this->isQualified) {
			$str = "{$this->qualifiedName}.$str";
		}
		return $str;
	}

	/**
	 * Override the basic expression because we know we only deal with strings
	 *
	 * @return string
	 */
	public function doBuild()
	{
		return $this->getQualifiedName();
	}
}
