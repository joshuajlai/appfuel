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
namespace 	Appfuel\StdLib\Console\Arg;

use Appfuel\Stdlib\Data\Bag;

/**
 * Console Argument Parser
 */
class Parser 
{
	public function parse(array $argv, Bag $result = NULL)
	{
		if (NULL === $result) {
			$result = new Bag();
		}

		foreach ($argv as $arg) {

			/*
			 * --item --item=value
			 */
			if (substr($arg, 0, 2) === '--') {
				$pos = strpos($arg, '=');
				
				/*
				 * --item was found with no == indicating it was set with 
				 * no value. we will put this in the bag with no value
				 */
				if (FALSE === $pos) {
					$key = substr($arg, 2);
					$result->add($key, true);
				} 
				/*
				 * item was found with a value associated to it
				 */
				else {
					$key    = substr($arg, 2, $pos - 2);
					$value  = substr($arg, $pos + 1);
					$result->add($key, $value);
				}
			}
			/*
			 * -a=value -abc
			 */
			else if (substr($arg, 0, 1) === '-') {
				
				/* found flag with = sign */
				if (substr($arg, 2, 1) === '=') {
					$key   = substr($arg, 1, 1);
					$value = substr($arg, 3);
					$result->add($key, $value);
				}
				/* short options grouped togather like -abc */
				else {
					$chars = str_split(substr($arg, 1);
					foreach ($chars as $char) {
						$result->add($char, true);
					}
				}
			}
			/* ordinary argument */
			else {
				$result->add($arg, null);
			}
		}
	}
}

