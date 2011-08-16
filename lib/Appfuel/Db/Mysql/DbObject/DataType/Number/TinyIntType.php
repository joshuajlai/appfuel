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

use Appfuel\Framework\Exception;

/**
 * Handle logic common to all integer types
 */
class TinyIntType extends AbstractIntType
{
	/**
	 * @param	bool	$isUnsigned		determines if this type is unsigned
	 * @return	TinyIntType
	 */
	public function __construct($isUnsigned = false)
	{
		$params = array(
			'min'		=> -128,
			'max'		=> 127,
			'umax'		=> 255,
			'bytes'		=> 1,
			'unsigned'  => (bool) $isUnsigned
		);

		parent::__construct($params);
	}
}
