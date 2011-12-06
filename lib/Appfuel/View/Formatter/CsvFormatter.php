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
 * Format an array of arrays into csv 
 */
class CsvFormatter extends BaseFormatter implements ViewFormatterInterface
{

    /** 
     * @param   mixed	$data
	 * @return	string
     */
    public function format(array $data)
    {
        if (! $this->isValidFormat($data)) {
            $err = 'Csv Formatter failed: data must be an associative array';
			throw new InvalidArgumentException($err);
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
