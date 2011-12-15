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
 * Common logic between all Compositors.
 */
class BaseCompositor
{

    /** 
	 * Check that determines if the data is infact an associative array
	 *
     * @param   array	$data
	 * @return	bool
     */
    public function isValidFormat(array $data)
    {
		if (empty($data)) {
			return true;
		}

		return ! ($data === array_values($data));
    }
}
