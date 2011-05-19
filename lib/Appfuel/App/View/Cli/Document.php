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
namespace Appfuel\App\View\Cli;

use Appfuel\Framework\View\ViewInterface,
	Appfuel\App\View\Data as ViewData;

/**
 * The document document is a dictionary that json encodes all its content
 */
class Document extends ViewData implements ViewInterface
{
	/**
	 * @return string
	 */
	public function build($data = null)
	{
        $result = '';
        $data     = $this->getAll();
        foreach ($data as $index => $datum) {
            if (is_scalar($datum)) {
                $result .= $index . ' ' . $datum;
            }

            if (is_array($datum)) {
                $result .= $index . ' ' . implode(' ', $datum);
            }

            if (is_object($datum) && method_exists($datum, '__toString')) {
                $result .= $index . ' ' . $datum . "\n";
            }
        }

        return $result;
	}
}
