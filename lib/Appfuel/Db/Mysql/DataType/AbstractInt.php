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
	Appfuel\Framework\Db\Mysql\DataType\IntTypeInterface;

/**
 * The abstract tpe handles the common details of all mysql datatypes
 */
abstract class AbstractInt extends AbstractType implements IntTypeInterface
{
	/**
	 * Used in sql string
	 * @var string
	 */
	protected $sqlUnsigned = 'unsigned';
	
	/**
	 * used in sql string
	 * @var string
	 */
	protected $sqlZeroFill = 'zerofill';

	/**
	 * Used in sql string
	 * @var string
	 */
	protected $sqlAutoIncrement = 'auto_increment';
	
    /**
     * @param   string  $sql    string used in sql statements
     * @param   string  $validator  name of the validator for this type
	 * @param	string	$attrs	space delimited string of attributes	
     * @return  AbstractType
     */
	public function __construct($sql,$validator, $attrs = null)
	{
		parent::__construct($sql, $validator);
		if (null !== $attrs) {
			$this->loadAttributes($attrs);
		}
	}

	/**
	 * Parse the option string into validate type attributes
	 * 
	 * @param	string	$attrString
	 * @return	AbstractInt
	 */
	public function loadAttributes($attrString)
	{
		if (! is_string($attrString)) {
			throw new Exception("Invalid attribute string");
		}
		
		$attrs = explode(' ', strtolower($attrString));
		if (! $attrs) {
			return $this;
		}

		foreach ($attrs as $attr) {

			/* the only numberic attribute is display width */
			if (is_numeric($attr)) {
				$this->setDisplayWidth((int)$attr);
				continue;
			}

			if ('unsigned' === $attr) {
				$this->enableUnsigned();
				continue;
			}

			if ('zerofill' === $attr) {
				$this->enableZeroFill();
				continue;
			}

			if ('auto_increment' === $attr) {
				$this->enableAutoIncrement();
				continue;
			}
		}

		return $this;
	}

	/**
	 * @return	string
	 */
	public function buildSql()
	{
		$sql = $this->getSqlString();
		
		$displayWidth = $this->getDisplayWidth();
		if (null !== $displayWidth) {
			$sql .= "($displayWidth)";
		}

		if ($this->isUnsigned()) {
			$sql .= " {$this->getSqlUnsigned()}"; 
		}

		if ($this->isZeroFill()) {
			$sql .= " {$this->getSqlZeroFill()}";
		}

		if ($this->isAutoIncrement()) {
			$sql .= " {$this->getSqlAutoIncrement()}";
		}

		if ($this->isUpperCase()) {
			$sql = strtoupper($sql);
		}
		else {
			$sql = strtolower($sql);
		}

		return $sql;
	}

	/**
	 * @return	string
	 */
	public function getSqlUnsigned()
	{
		return $this->sqlUnsigned;
	}

	/**
	 * @return	string
	 */
	public function getSqlZeroFill()
	{
		return $this->sqlZeroFill;
	}

	/**
	 * @return	string
	 */
	public function getSqlAutoIncrement()
	{
		return $this->sqlAutoIncrement;
	}

	/**
	 * @return	AbstractInt
	 */
	public function enableSigned()
	{
		$this->addAttribute('is-unsigned', false);
		return $this;
	}

	/**
	 * @return	AbstractInt
	 */
	public function enableUnsigned()
	{
		$this->addAttribute('is-unsigned', true);
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isUnsigned()
	{
		$result = false;
		if (true === $this->getAttribute('is-unsigned', false)) {
			$result = true;
		}

		return $result;
	}

	/**
	 * @return	AbstractInt
	 */
	public function enableAutoIncrement()
	{
		$this->addAttribute('is-auto-increment', true);
		return $this;
	}

	/**
	 * @return	AbstractInt
	 */
	public function disableAutoIncrement()
	{
		$this->addAttribute('is-auto-increment', false);
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isAutoIncrement()
	{
		$result = false;
		if (true === $this->getAttribute('is-auto-increment', false)) {
			$result = true;
		}

		return $result;
	}

	/**
	 * @return	int | null if not set
	 */
	public function getDisplayWidth()
	{
		return $this->getAttribute('display-width');
	}

	/**
	 * @param	int	$width
	 * @return	AbstractInt
	 */
	public function setDisplayWidth($width)
	{
		if (! is_int($width) || $width < 0) {
			throw new Exception("Display width must be an int > 0 ");
		}
		
		return $this->addAttribute('display-width', $width);
	}

	/**
	 * @return	AbstractInt
	 */
	public function enableZeroFill()
	{
		return $this->addAttribute('is-zero-fill', true);
	}

	/**
	 * @return	AbstractInt
	 */
	public function disableZeroFill()
	{
		return $this->addAttribute('is-zero-fill', false);
	}

	/**
	 * @return	bool
	 */
	public function isZeroFill()
	{
		$result = false;
		if (true === $this->getAttribute('is-zero-fill', false)) {
			$result = true;
		}

		return $result;
	}
}
