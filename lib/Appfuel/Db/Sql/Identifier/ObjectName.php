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
 * Generic implementation for the string <table|schema>.<identifier>
 */
abstract class ObjectName extends BasicExpr
{
	/**
	 * Qualified name of the object
	 * @var string
	 */
	protected $qName = null;

	/**
	 * Flag used to determine if the schema should be qualified
	 * @var bool
	 */
	protected $isQualified = false;

	/**
     * @param   string   $operand
     * @return  File
     */
    public function __construct($name)
    {
		$pos = strpos($name, '.');
		if (false !== $pos) {
			$this->qName = substr($name, 0, $pos);
			$operand     = substr($name, $pos+1);
			$this->enableQualifiedName();
		} else {
			$operand = $name;
		}

		parent::__construct($operand);
    }

	/**
	 * @return	TableName
	 */
	public function enableQualifiedName()
	{
		if (empty($this->qName) || ! is_string($this->qName)) {
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
			$str = "{$this->qName}.$str";
		}

		return $str;
	}

	/**
	 * @return	string | null 
	 */
	public function getParent()
	{
		return $this->qName;
	}

	/**
	 * Override the basic expr because we know we only deal with strings
	 *
	 * @return string
	 */
	public function doBuild()
	{
		return $this->getQualifiedName();
	}
}
