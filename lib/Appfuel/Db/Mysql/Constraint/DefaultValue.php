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
namespace Appfuel\Db\Mysql\Constraint;

use Appfuel\Framework\Exception;

/**
 * Constraint used only on columns this specifies a value used when no value
 * is given.
 */
class DefaultValue extends  AbstractConstraint
{
	/**
	 * value used when do other value is given
	 * @var	mixed
	 */
	protected $value = null;

	/**
	 * @return	DefaultValue
	 */
	public function __construct($value) 
	{	
		if (is_null($value)   || 
			is_scalar($value) || 
			(is_object($value) && is_callable(array($value, '__toString')))) {
			$this->value = $value;
		}
		else {
			$err  = "Value must be a scalar or an objectect that implments ";
			$err .= "__toString";
			throw new Exception($err);
		}
		
		parent::__construct("default");
	}

	/**
	 * @return	mixed	
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return	string
	 */
	public function buildSql()
	{
		$value = $this->getValue();
		if (is_numeric($value)) {
			$sqlValue = $value;
		}
		else if (is_string($value) || is_object($value)) {
			$sqlValue = "'$value'";
		}
		else if (null === $value) {
			$sqlValue = 'null';
			
			/* 
			 * this only makes sense for the null keyword
			 */
			if ($this->isUpperCase()) {
				$sqlValue = strtoupper($sqlValue);
			}
		}

		return parent::buildSql() . ' ' . $sqlValue;
	}
}
