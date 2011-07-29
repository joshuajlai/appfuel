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
 * Simple expression designed to old objects that support to string
 */
class TableName extends BasicExpr
{
	/**
	 * Name of the table
	 * @var string
	 */
	protected $schema = null;

	/**
	 * Flag used to determine if the schema should be qualified
	 * @var bool
	 */
	protected $isSchema = false;

	/**
     * @param   string   $operand
     * @return  File
     */
    public function __construct($table)
    {
		$pos = strpos($table, '.');
		if (false !== $pos) {
			$this->schema   = substr($table, 0, $pos);
			$operand		= substr($table, $pos+1);
			$this->enableSchema();
		} else {
			$operand = $table;
		}

		parent::__construct($operand);
    }

	/**
	 * @return string | null when not set
	 */
	public function getSchema()
	{
		return $this->schema;
	}

	/**
	 * @return	TableName
	 */
	public function enableSchema()
	{
		if (empty($this->schema) || ! is_string($this->schema)) {
			return $this;
		}
		$this->isSchema = true;
		return $this;
	}

	/**
	 * @return	TableName
	 */
	public function disableSchema()
	{
		$this->isSchema = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isSchema()
	{
		return $this->isSchema;
	}

	/**
	 * @return	string
	 */
	public function getQualifiedName()
	{
		$str = $this->operand;
		if ($this->isSchema) {
			$str = "{$this->schema}.$str";
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
