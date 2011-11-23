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
namespace Appfuel\View\Formatter;

/**
 * Seems silly but it allows other more complex formatter to work the 
 * with the same interface. This one happens to be simple
 */
class JsonFormatter implements ViewFormatterInterface
{

    /** 
     * @param   mixed	$data
	 * @return	string
     */
    public function format($data)
    {
		return json_encode($data);
    }
}
