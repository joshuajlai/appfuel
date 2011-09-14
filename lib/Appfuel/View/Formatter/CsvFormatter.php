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

use Appfuel\Framework\View\Formatter\ViewFormatterInterface;

/**
 * Format an array of arrays into csv 
 */
class CsvFormatter implements ViewFormatterInterface
{

    /** 
     * @param   mixed	$data
	 * @return	string
     */
    public function format($data)
    {
        if (is_string($data)) {
            return $data;
        }
        else if (is_object($data) && is_callable(array($data, '__toString'))) {
            return $data->__toString();
        }
        else if (! is_array($data)) {
            return '';
        }

		$result = '';
		foreach ($data as $index => $record) {
			if (is_string($record)) {
				$record = array($index, $record);
			}
			$result .= implode(',', $record) . PHP_EOL; 
		}

		return $result;
    }
}
