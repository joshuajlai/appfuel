<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\Framework\Env;

use Test\AfTestCase	as ParentTestCase;

/**
 * Testing how bitwise operators work in php
 */
class BitTest extends ParentTestCase
{

    /**
	 * @return null
     */
    public function testOne()
    {
		$a = 32;
		$b = 1 << 5;

		$bitCount = 15;
		$target = E_ALL | E_STRICT;

		$result = array();
		for($i = 0; $i < $bitCount; $i++) {
			$bitValue = $target & (1 << $i);
			$result[$i] = $bitValue;
		}
		echo "\n", print_r($result, 1), "\n";exit; 
    }
}
