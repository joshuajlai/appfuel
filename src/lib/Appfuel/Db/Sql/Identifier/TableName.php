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
class TableName extends ObjectName
{
	/**
     * @param   string   $operand
     * @return  File
     */
    public function __construct($table)
    {
		parent::__construct($table);
    }

	/**
	 * @return string
	 */
	public function getSchema()
	{
		return $this->getParent();
	}
}
