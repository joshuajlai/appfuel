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

/**
 * Parse the long, short options as well as command line arguments
 */
interface ArgParserInterface
{
	/**
	 * @param	array	$list
	 * @return	array
	 */
	public function parse(array $args);
}
