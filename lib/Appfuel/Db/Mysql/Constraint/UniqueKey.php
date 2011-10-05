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
 * Constaint used in tables to limit a column or columns to unique values
 */
class UniqueKey extends AbstractConstraint
{
	/**
	 * @return	DefaultValue
	 */
	public function __construct($columns) 
	{	
		if (is_string($columns)) {
			$this->addColumn($columns);	
		} 
		else if (is_array($columns)) {
			foreach ($columns as $column) {
				$this->addColumn($column);
			}
		}
		else {
			$err = "columns must be a string or an array of strings";
			throw new Exception($err); 
		}
		
		parent::__construct('unique key');
	}

	/**
	 * @return	string
	 */
	public function buildSql()
	{
		$sqlValue = strtolower($this->buildColumnString());			 
		if ($this->isUpperCase()) {
			$sqlValue = strtoupper($sqlValue);
		}

		return parent::buildSql() . ' ' . $sqlValue;
	}
}
