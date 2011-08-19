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
namespace Appfuel\Db\Mysql\DbObject\DataType\Number;

/**
 * Define the parameters for a mysql tinyint
 */
class TinyInt extends AbstractIntType
{
	/**
	 * @param	bool	$isUnsigned		determines if this type is unsigned
	 * @return	TinyIntType
	 */
	public function __construct(array $attrs = null)
	{
		parent::__construct('tinyint', 255, -128, 127, $attrs);
	}
}
