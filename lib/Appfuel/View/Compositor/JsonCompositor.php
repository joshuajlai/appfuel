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
 * Json encode an associative array of data
 */
class JsonCompositor extends BaseCompositor implements ViewCompositorInterface
{
    /** 
     * @param   mixed	$data
	 * @return	string
     */
    public function compose(array $data)
    {
		if (! $this->isValidFormat($data)) {
			$err = 'Json format failed: data must be an associative array';
			throw new InvalidArgumentException($err);
		}

		return json_encode($data);
    }
}
