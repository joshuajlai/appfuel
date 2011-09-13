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
namespace Appfuel\Framework\View\Formatter;

/**
 * The view formatter converts a data structure, usually an array into a string
 */
interface ViewFormatterInterface
{
    /** 
     * @param	mixed	$data	data to be formatted into a string
	 * @return	string
     */
    public function format($data);

}
