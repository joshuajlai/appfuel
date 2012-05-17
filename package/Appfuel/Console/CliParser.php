<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Console;


use InvalidArgumentException;

/**
 */
class CliParser
{

	/**
	 * @param	array	$list
	 * @return	array
	 */
	public function parse(array $args, array $spec)
	{
		$out = array();
		$cmd = array_shift($args);
		foreach ($args as $arg) {
			if ('--' === substr($arg, 0, 2)) {
				$eqPos = strpos($arg, '=');
				if (false === $eqPos) {
					$key = substr($arg, 2);
					$out[$key] = isset($out[$key])? $out[$key] : true;
				}
				else {
					$key = substr($arg, 2, $eqPos - 2);
					$out[$key] = substr($arg, $eqPos+1);
				}
			}
			else if ('-' === substr($arg, 0, 1)) {
				if ('=' === substr($arg, 2, 1)) {
					$key = substr($arg, 1, 1);
					$out[$key] = substr($arg, 3);
				}
				else {
					$chars = str_split(substr($arg, 1));
					foreach ($chars as $char) {
						$out[$char] = isset($out[$char])? $out[$char] : true;
					}
				}
			}
			else {
				$result[] = $arg;
			}
		}

		echo "\n", print_r($out,1), "\n";exit;
	}
}
