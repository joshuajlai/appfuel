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
 * Parse long, short options and arguments from the command line
 */
class ArgParser implements ArgParserInterface
{

	/**
	 * when parsing long option and short options along with their values
	 * are revoved from the args array so that the only thing remaining are
	 * the arguments. When a -- is used with a space it tells the parser to
	 * stop parsing any long/short options and treat everything as an arg
	 *
	 * @param	array	$args
	 * @return	array
	 */
	public function parse(array $args)
    {
        $out = array(
            'cmd'   => null,
            'long'  => array(),
            'short' => array(),
            'args'  => array()
        );
        $out['cmd'] = array_shift($args);

		$max = count($args);
		$pattern = '/^--?.+/';
		$isOptionsEnd = false;
		for ($i = 0; $i < $max; $i++) {
			$str  = $args[$i];
			$len  = strlen($str);

			/*
			 * Used for either long or short option. When a value is delimited
			 * by a space, that value will be found as the next item in argv
			 */
			$next = isset($args[$i +1]) ? $args[$i + 1]: null;
			if ($len > 2 && '--' === substr($str, 0, 2)) {
				$str   = substr($str, 2);
				$parts = explode('=', $str);
				$key   = current($parts);
				$value = next($parts);
				$out['long'][$key] = true;

				if (count($parts) === 1 &&
					isset($next) && 
					preg_match($pattern, $next) === 0) {
						$out['long'][$key] = $next;
						unset($args[$i+1]);
						unset($args[$i]);
						$i++;
				}
				else if (count($parts) === 2) {
					$out['long'][$key] = $value;
					unset($args[$i]);
				}
			}
			/* a double dash and space tells the parser to stop parsing 
			 * options
			 */
			else if ($len === 2 && '--' === $str) {
				unset($args[$i]);
				$isOptionsEnd = true;
				break;
			}
			else if ($len === 2 && '-' === $str[0]) {
				$key = $str[1];
				$out['short'][$key] = true;
				if (isset($next) && preg_match($pattern, $next) === 0) {
					$out['short'][$key] = $next;
					unset($args[$i+1]);
					unset($args[$i]);
					$i++;
				}
			}
			else if ($len > 1 && '-' === $str[0]) {
				$argLen = strlen($str);
				for($j = 1; $j < $argLen; $j++) {
					$out['short'][$str[$j]] = true;
				}
				unset($args[$i]);
			}
		}    

		$args = array_values($args);
		$max  = count($args) - 1;

		if (true === $isOptionsEnd) {
			$out['args'] = $args;
		}
		else {
			for($i = $max; $i >= 0; $i--) {
				if (preg_match($pattern, $args[$i]) === 0) {
					$out['args'][] = $args[$i];
				}
			}
			$out['args'] = array_reverse($out['args']);
		}


		return $out;
    }
}
