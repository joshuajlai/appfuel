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
class UniqueKey extends Key
{
	/**
	 * @return	DefaultValue
	 */
	public function __construct($name, $columns) 
	{	
		parent::__construct($name, $columns, true);
	}
}
