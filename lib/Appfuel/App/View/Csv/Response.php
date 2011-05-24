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
namespace Appfuel\App\View\Csv;

use Appfuel\Framework\App\View\ViewInterface,
	Appfuel\App\View\Data as ViewData;

/**
 * Comma separated values is a dictionary that encodes its content
 */
class Response extends ViewData implements ViewInterface
{
	/**
	 * This name is used when the contents are to be downloaded as a file
	 * @var string
	 */
	protected $filename = null;

    /**
     * @param   string  $fileName
     * @return  Template
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return  string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

	/**
	 * @return string
	 */
	public function build($data = null)
	{
        $resule = '';
        $data = $this->getAll();
        foreach ($data as $index => $record) {
            if (! is_array($record)) {
                $record = array($index, $record);
            }

            $result .= '"' . implode('","', $record) . '"' . "\r\n";
        }

        return $result;
	}
}
