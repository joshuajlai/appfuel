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

use Appfuel\Framework\DataStructure\Dictionary;

/**
 * Define the parameters for a mysql bigint
 */
class BigInt extends AbstractIntType
{
	/**
	 * We have to set our own min max values cause our numnbers are too big
	 * for the int validation of the other classes
	 *
	 * @param	array	$attrs
	 * @return	BigInt
	 */
	public function __construct(array $attrs = null)
	{
		$this->umax = 18446744073709551615;
		$this->min  = -9223372036854775808;
		$this->max  = 9223372036854775807;
		$this->setSqlName('bigint');

        /* default attributes when none are given */
        if (null === $attrs) {
            $attrs = array(
                'unsigned'      => false,
                'is-nullable'   => true,
            );
        }

        $this->setAttributes(new Dictionary($attrs));
		
	}
}
