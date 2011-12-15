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
namespace Appfuel\View\Compositor;

/**
 * The view formatter is responsible for converting the known data structure of
 * an associative array into a string.
 */
interface ViewCompositorInterface
{
    /** 
     * @param	mixed	$data	array data to be formatted into a string
	 * @return	string
     */
    public function compose(array $data);
}
