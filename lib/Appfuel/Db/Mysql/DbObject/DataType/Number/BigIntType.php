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
 * Define the parameters for a mysql bigint
 */
class BigIntType extends AbstractIntType
{
	/**
	 * @param	bool	$isUnsigned		determines if this type is unsigned
	 * @return	SmallIntType
	 */
	public function __construct($isUnsigned = false)
	{
		parent::__construct(
			'bigint', 
			18446744073709551615, 
			-9223372036854775808, 
			9223372036854775807, 
			(bool) $isUnsigned
		);
	}
}
